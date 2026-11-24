{{--
    ONE delete confirmation modal for the whole page.
    Any <x-delete-button> sends a "confirm-delete" event with that product's details;
    the modal fills itself with them and points its form at that product.
--}}
<div x-data="deleteModal" @confirm-delete.window="openFor($event.detail)" @keydown.escape.window="open && close()">
    <div x-cloak x-show="open" x-transition.opacity
         class="fixed inset-0 z-[65] flex items-start justify-center overflow-y-auto bg-ink/40 p-4 backdrop-blur-[2px] sm:items-center"
         @click.self="close()">

        <div x-show="open" x-transition
             class="w-full max-w-[512px] overflow-hidden rounded-2xl border border-line/60 bg-white shadow-2xl"
             role="alertdialog" aria-modal="true" aria-labelledby="delete-title" aria-describedby="delete-text">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-line px-6 py-3.5">
                <p class="flex items-center gap-1.5 text-[13px] leading-5 font-bold tracking-[0.5px] text-danger uppercase">
                    <x-icon name="warning" class="size-4" />
                    Destructive action
                </p>
                <button type="button" class="-mr-1 rounded-lg p-1.5 text-muted hover:bg-page hover:text-ink" @click="close()" aria-label="Close">
                    <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                        <path d="M5 5l10 10M15 5L5 15" />
                    </svg>
                </button>
            </div>

            {{-- The real form: POST + @method('DELETE') = DELETE /products/{id}. :action is filled in by Alpine. --}}
            <form method="POST" :action="product.action">
                @csrf
                @method('DELETE')

                <div class="flex flex-col gap-5 px-6 py-6">
                    <div class="flex gap-4">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-danger-soft text-danger" aria-hidden="true">
                            <x-icon name="trash" class="size-6" />
                        </span>
                        <div class="min-w-0">
                            <h2 id="delete-title" class="text-lg leading-6 font-semibold tracking-[-0.2px] break-words text-ink">
                                Delete '<span x-text="product.name"></span>'?
                            </h2>
                            <p id="delete-text" class="mt-1.5 text-sm leading-[22px] text-muted">
                                Are you sure you want to delete this product?
                                <span class="font-semibold text-ink">SKU: <span x-text="product.sku"></span></span>.
                                This product will be permanently deleted. This action cannot be undone.
                            </p>
                        </div>
                    </div>

                    {{-- Product summary card --}}
                    <div class="flex items-center gap-4 rounded-xl border border-line bg-page p-4">
                        <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-line bg-white text-subtle">
                            <img x-show="product.imageUrl" :src="product.imageUrl" :alt="product.name" class="size-full object-cover">
                            <x-icon name="image" class="size-6" x-show="!product.imageUrl" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-[15px] leading-5 font-semibold text-ink" x-text="product.name"></p>
                                <p class="text-[15px] leading-5 font-semibold text-ink" x-text="product.price"></p>
                            </div>
                            <p class="mt-1 text-xs leading-4 text-muted">
                                Category: <span class="font-medium text-ink" x-text="product.category"></span>
                                <span class="mx-1.5" aria-hidden="true">•</span>
                                Current Stock:
                                <span :class="product.stockWarning ? 'text-danger' : 'text-ink'" class="font-medium"
                                      x-text="product.stock + (product.stock === 1 ? ' unit' : ' units')"></span>
                            </p>
                            <p class="mt-0.5 text-xs leading-4 text-muted">Database ID: <span class="font-mono" x-text="'#' + product.id"></span></p>
                        </div>
                    </div>

                    {{-- "I understand" – the delete button stays disabled until this is ticked --}}
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-line p-4 hover:bg-page has-checked:border-danger/40 has-checked:bg-danger-soft/40">
                        <input type="checkbox" x-model="confirmed" x-ref="confirm" class="mt-0.5 size-4 shrink-0 cursor-pointer accent-danger">
                        <span>
                            <span class="block text-sm leading-5 font-medium text-ink">I understand that this action is irreversible.</span>
                            <span class="block text-xs leading-4 text-muted">This product will be permanently deleted.</span>
                        </span>
                    </label>
                </div>

                {{-- Footer --}}
                <div class="flex flex-col-reverse gap-2 border-t border-line bg-page px-6 py-4 sm:flex-row sm:justify-end">
                    <button type="button" @click="close()"
                            class="h-10 rounded-lg border border-line bg-white px-5 text-sm font-medium text-ink hover:bg-page">
                        Cancel
                    </button>
                    <button type="submit" disabled :disabled="!confirmed"
                            class="flex h-10 items-center justify-center gap-2 rounded-lg bg-danger px-5 text-sm font-semibold text-white shadow-sm hover:bg-[#9a1515] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-danger">
                        <x-icon name="trash" class="size-4" />
                        Yes, Delete Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
