import Alpine from 'alpinejs';

/*
 * Alpine.js is only used for small bits of interactivity:
 * the mobile sidebar, the product form and delete modals, and the toast message.
 * All real validation happens on the server (ProductRequest).
 */

/*
 * Page scroll lock shared by all modals. A counter (not just on/off) so that
 * closing the delete modal on top of the edit modal doesn't unlock scrolling
 * while the edit modal is still open.
 */
let scrollLocks = 0;
const lockScroll = () => {
    scrollLocks++;
    document.body.classList.add('overflow-hidden');
};
const unlockScroll = () => {
    scrollLocks = Math.max(0, scrollLocks - 1);
    if (scrollLocks === 0) {
        document.body.classList.remove('overflow-hidden');
    }
};

/**
 * Add / Edit product modal.
 *   open      – start opened (e.g. after a validation error)
 *   closeUrl  – when the modal was opened by its own URL (/products/create, /products/5/edit),
 *               closing it goes back to the product list instead of just hiding it
 *   imageUrl  – the product's current image (edit), shown as the preview
 *   status    – 'active' / 'inactive' (drives the Edit modal's toggle switch)
 */
Alpine.data('productModal', ({ open = false, closeUrl = null, imageUrl = null, status = 'active' } = {}) => ({
    open,
    imageUrl,
    statusActive: status === 'active',
    copied: false,
    previewUrl: imageUrl,
    fileName: '',
    fileError: '',
    removeImage: false,
    descriptionLength: 0,
    skuHint: '',

    init() {
        this.descriptionLength = this.$refs.description.value.length;
        this.$watch('open', (isOpen) => (isOpen ? lockScroll() : unlockScroll()));
        if (this.open) {
            lockScroll();
        }
    },

    /** Esc closes this modal – unless the delete confirmation is open on top of it. */
    onEscape() {
        if (this.open && !document.body.dataset.confirmOpen) {
            this.close();
        }
    },

    show() {
        this.open = true;
        this.$nextTick(() => this.$refs.name.focus());
    },

    close() {
        if (closeUrl) {
            window.location.href = closeUrl;
        } else {
            this.open = false;
        }
    },

    /**
     * Builds a SKU like the seeded ones: CATEGORY-NAME-NUMBER, e.g. "APP-SHR-482".
     * The server still checks that it is unique.
     */
    generateSku() {
        const category = this.$refs.category.value;
        const words = this.$refs.name.value.toUpperCase().replace(/[^A-Z0-9]+/g, ' ').trim().split(' ').filter(Boolean);

        if (!category || words.length === 0) {
            this.skuHint = 'Enter a product name and choose a category first.';
            return;
        }

        // First 3 letters of the category: "Home & Living" -> "HOM"
        const categoryCode = category.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 3);

        // Last word of the name, first letter + its consonants: "T-Shirt" -> "SHR"
        const lastWord = words[words.length - 1];
        const nameCode = (lastWord[0] + lastWord.slice(1).replace(/[AEIOU]/g, '')).slice(0, 3).padEnd(3, 'X');

        const number = Math.floor(100 + Math.random() * 900);

        this.$refs.sku.value = `${categoryCode}-${nameCode}-${number}`;
        this.skuHint = '';
    },

    /** Edit modal: copy the SKU to the clipboard. */
    async copySku() {
        const sku = this.$refs.sku.value;
        try {
            await navigator.clipboard.writeText(sku);
        } catch {
            // Older browsers / non-secure pages: fall back to selecting + copying.
            this.$refs.sku.select();
            document.execCommand('copy');
        }
        this.copied = true;
        setTimeout(() => (this.copied = false), 1500);
    },

    /** Show a preview of the chosen image straight away (before uploading). */
    previewImage(file) {
        this.fileError = '';
        if (!file) {
            return;
        }

        // Quick check in the browser for a nicer experience; the server checks again.
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.fileError = 'The image must be a JPG, PNG or WEBP file.';
        } else if (file.size > 2 * 1024 * 1024) {
            this.fileError = 'The image must not be larger than 2 MB.';
        }

        if (this.fileError) {
            // Clear the rejected file AND its preview, so the preview always matches what will be uploaded.
            this.$refs.image.value = '';
            this.previewUrl = this.removeImage ? null : this.imageUrl;
            this.fileName = '';
            return;
        }

        this.previewUrl = URL.createObjectURL(file);
        this.fileName = `${file.name} · ${(file.size / 1024).toFixed(0)} KB`;
        this.removeImage = false;
    },

    /** Files dropped onto the upload box are put into the real file input. */
    dropImage(event) {
        const file = event.dataTransfer.files[0];
        if (!file) {
            return;
        }
        const transfer = new DataTransfer();
        transfer.items.add(file);
        this.$refs.image.files = transfer.files;
        this.previewImage(file);
    },

    clearImage() {
        this.$refs.image.value = '';
        this.previewUrl = null;
        this.fileName = '';
        this.fileError = '';
        // If the product already had an image, tell the server to delete it.
        this.removeImage = Boolean(this.imageUrl);
    },

    /** Edit modal: changed your mind after "Remove image" – show the current image again. */
    undoRemoveImage() {
        this.removeImage = false;
        this.previewUrl = this.imageUrl;
    },
}));

/**
 * The single delete confirmation modal. <x-delete-button> sends a "confirm-delete"
 * event with the product's details; openFor() shows them. The "Yes, Delete Product"
 * button stays disabled until the "I understand" box is ticked.
 */
Alpine.data('deleteModal', () => ({
    open: false,
    confirmed: false,
    product: { id: null, name: '', sku: '', price: '', category: '', stock: 0, stockWarning: false, imageUrl: null, action: '' },

    openFor(product) {
        this.product = product;
        this.confirmed = false; // never carry a tick over from a previous product
        this.open = true;
        document.body.dataset.confirmOpen = '1'; // tells the edit modal to ignore Esc
        lockScroll();
        this.$nextTick(() => this.$refs.confirm.focus());
    },

    close() {
        if (!this.open) {
            return;
        }
        this.open = false;
        unlockScroll();
        // Cleared a moment later, so the same Esc key press doesn't also close the edit modal.
        setTimeout(() => delete document.body.dataset.confirmOpen);
    },
}));

/** Success toast: slides in, disappears by itself after 5 seconds. */
Alpine.data('toast', () => ({
    visible: false,

    init() {
        this.$nextTick(() => (this.visible = true));
        setTimeout(() => (this.visible = false), 5000);
    },
}));

window.Alpine = Alpine;
Alpine.start();
