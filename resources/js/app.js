import Alpine from 'alpinejs';

/*
 * Alpine.js is only used for small bits of interactivity:
 * the mobile sidebar, the product form modal and the toast message.
 * All real validation happens on the server (ProductRequest).
 */

/**
 * Add / Edit product modal.
 *   open      – start opened (e.g. after a validation error)
 *   closeUrl  – when the modal was opened by its own URL (/products/create, /products/5/edit),
 *               closing it goes back to the product list instead of just hiding it
 *   imageUrl  – the product's current image (edit), shown as the preview
 */
Alpine.data('productModal', ({ open = false, closeUrl = null, imageUrl = null } = {}) => ({
    open,
    imageUrl,
    previewUrl: imageUrl,
    fileName: '',
    fileError: '',
    removeImage: false,
    descriptionLength: 0,
    skuHint: '',

    init() {
        this.descriptionLength = this.$refs.description.value.length;
        this.$watch('open', (isOpen) => document.body.classList.toggle('overflow-hidden', isOpen));
        if (this.open) {
            document.body.classList.add('overflow-hidden');
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
            this.$refs.image.value = '';
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
