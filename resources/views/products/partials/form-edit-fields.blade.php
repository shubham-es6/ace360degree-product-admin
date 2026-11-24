{{--
    Field layout for the EDIT modal (design-edit). Included from form-modal.blade.php,
    so it uses the same helpers: $id(), $value(), $errorFor(), $inputState(), $error ...
    The field NAMES are exactly the same as in the Add form, so ProductRequest
    validates both the same way.
--}}
<div class="grid grid-cols-1 gap-x-6 gap-y-6 px-5 py-6 sm:grid-cols-3 sm:px-7">

    {{-- Product title --}}
    <div class="sm:col-span-2">
        <div class="flex items-end justify-between gap-2">
            <label for="{{ $id('name') }}" class="{{ $labelLg }}">Product Title <span class="text-danger">*</span></label>
            <span class="hidden text-[13px] text-muted sm:inline">Visible on multi-store catalog</span>
        </div>
        <input id="{{ $id('name') }}" x-ref="name" type="text" name="name" maxlength="255" autocomplete="off"
               value="{{ $value('name', $product->name) }}"
               class="mt-2 {{ $inputLg }} {{ $inputState('name') }}">
        @if ($errorFor('name')) <p class="{{ $error }}">{{ $errorFor('name') }}</p> @endif
    </div>

    {{-- SKU: still editable (the server rejects duplicates), with a copy button --}}
    <div>
        <label for="{{ $id('sku') }}" class="{{ $labelLg }}">SKU Identifier <span class="text-danger">*</span></label>
        <div class="mt-2 flex">
            <input id="{{ $id('sku') }}" x-ref="sku" type="text" name="sku" maxlength="64" autocomplete="off"
                   value="{{ $value('sku', $product->sku) }}"
                   class="{{ str_replace(['rounded-lg', 'bg-white'], ['rounded-l-lg', 'bg-primary-soft'], $inputLg) }} {{ $inputState('sku') }} min-w-0 font-semibold uppercase">
            <button type="button" @click="copySku()"
                    class="flex h-11 w-12 shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-line bg-primary-soft text-muted hover:bg-tint hover:text-ink"
                    :title="copied ? 'Copied!' : 'Copy SKU'" aria-label="Copy SKU">
                <x-icon name="copy" class="size-4" x-show="!copied" />
                <x-icon name="check" class="size-4 text-success" x-show="copied" x-cloak />
            </button>
        </div>
        @if ($errorFor('sku'))
            <p class="{{ $error }}">{{ $errorFor('sku') }}</p>
        @else
            <p class="mt-1 text-[11px] leading-[14px] text-success" x-show="copied" x-cloak>SKU copied to clipboard</p>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:col-span-3 sm:grid-cols-2">
        {{-- Category --}}
        <div>
            <label for="{{ $id('category') }}" class="{{ $labelLg }}">Category <span class="text-danger">*</span></label>
            <div class="relative mt-2">
                <select id="{{ $id('category') }}" x-ref="category" name="category"
                        class="{{ $inputLg }} {{ $inputState('category') }} appearance-none pr-10">
                    @foreach (\App\Models\Product::CATEGORIES as $category)
                        <option value="{{ $category }}" @selected($value('category', $product->category) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
                <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-3.5 size-5 -translate-y-1/2 text-muted" />
            </div>
            @if ($errorFor('category')) <p class="{{ $error }}">{{ $errorFor('category') }}</p> @endif
        </div>

        {{--
            Status as a toggle switch. The switch only changes Alpine's "statusActive";
            the hidden input sends status=active / status=inactive with the form.
        --}}
        <div>
            <span class="{{ $labelLg }}" id="{{ $id('status-label') }}">Catalog Publishing Status <span class="text-danger">*</span></span>
            <input type="hidden" name="status" value="{{ $currentStatus }}" :value="statusActive ? 'active' : 'inactive'">
            <div class="mt-2 flex h-11 items-center justify-between gap-3 rounded-lg border bg-white px-4 {{ $errorFor('status') ? 'border-danger' : 'border-line' }}">
                <span class="flex min-w-0 items-center gap-3 text-[15px] font-medium text-ink">
                    <span class="size-2.5 shrink-0 rounded-full" :class="statusActive ? 'bg-success' : 'bg-subtle'" aria-hidden="true"></span>
                    <span class="truncate" x-text="statusActive ? 'Active (Visible in Storefronts)' : 'Inactive (Hidden from customers)'"></span>
                </span>
                <button type="button" role="switch" :aria-checked="statusActive.toString()" aria-labelledby="{{ $id('status-label') }}"
                        @click="statusActive = !statusActive"
                        class="relative h-7 w-12 shrink-0 rounded-full transition-colors focus:ring-2 focus:ring-primary/30 focus:outline-none"
                        :class="statusActive ? 'bg-primary' : 'bg-line'">
                    <span class="absolute top-1 left-1 size-5 rounded-full bg-white shadow transition-transform"
                          :class="statusActive ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>
            @if ($errorFor('status')) <p class="{{ $error }}">{{ $errorFor('status') }}</p> @endif
        </div>

        {{-- Price panel --}}
        <div class="rounded-xl border border-line/70 bg-page p-4">
            <label for="{{ $id('price') }}" class="{{ $panelLabel }}">Regular Price ($) <span class="text-danger">*</span></label>
            <div class="relative mt-2">
                <span class="pointer-events-none absolute top-1/2 left-4 -translate-y-1/2 text-[15px] text-muted">$</span>
                <input id="{{ $id('price') }}" type="number" name="price" step="0.01" min="0" inputmode="decimal"
                       value="{{ $value('price', $product->price) }}"
                       class="{{ $inputLg }} {{ $inputState('price') }} pl-9 text-right font-semibold">
            </div>
            @if ($errorFor('price')) <p class="{{ $error }}">{{ $errorFor('price') }}</p> @endif
        </div>

        {{-- Stock panel --}}
        <div class="rounded-xl border border-line/70 bg-page p-4">
            <label for="{{ $id('stock_quantity') }}" class="{{ $panelLabel }}">Available Units <span class="text-danger">*</span></label>
            <input id="{{ $id('stock_quantity') }}" type="number" name="stock_quantity" step="1" min="0" inputmode="numeric"
                   value="{{ $value('stock_quantity', $product->stock_quantity) }}"
                   class="mt-2 {{ $inputLg }} {{ $inputState('stock_quantity') }} text-right font-semibold">
            @if ($errorFor('stock_quantity')) <p class="{{ $error }}">{{ $errorFor('stock_quantity') }}</p> @endif
        </div>
    </div>

    {{-- Description: plain textarea --}}
    <div class="sm:col-span-3">
        <div class="flex items-end justify-between gap-2">
            <label for="{{ $id('description') }}" class="{{ $labelLg }}">Product Narrative Description</label>
            <span class="text-[13px] text-muted" aria-live="polite">
                Plain text only ·
                <span x-text="descriptionLength">{{ mb_strlen((string) $value('description', $product->description)) }}</span> / 2000 chars
            </span>
        </div>
        <textarea id="{{ $id('description') }}" x-ref="description" name="description" rows="3" maxlength="2000"
                  @input="descriptionLength = $event.target.value.length"
                  class="mt-2 block w-full resize-y rounded-lg border bg-white px-4 py-3 text-[15px] leading-6 text-ink placeholder:text-subtle focus:ring-2 focus:outline-none {{ $inputState('description') }}">{{ $value('description', $product->description) }}</textarea>
        @if ($errorFor('description')) <p class="{{ $error }}">{{ $errorFor('description') }}</p> @endif
    </div>

    {{-- Product image: the single current image shown large + a "Change image" upload tile --}}
    <div class="sm:col-span-3">
        <div class="flex flex-wrap items-end justify-between gap-2">
            <span class="{{ $labelLg }}">Product Image</span>
            <span class="text-[13px] text-muted">JPG, PNG or WEBP · max 2 MB</span>
        </div>

        <div class="mt-3 flex flex-wrap gap-4">
            {{-- Current (or newly chosen) image --}}
            <div class="relative flex size-[186px] items-center justify-center overflow-hidden rounded-xl border-2 bg-page text-subtle"
                 :class="previewUrl ? 'border-primary' : 'border-line border-dashed'">
                <img x-show="previewUrl" :src="previewUrl" alt="{{ $product->name }}" class="size-full object-cover"
                     @if ($product->imageUrl()) src="{{ $product->imageUrl() }}" @else x-cloak @endif>
                <div x-show="!previewUrl" class="flex flex-col items-center gap-1.5 text-center">
                    <x-icon name="image" class="size-8" />
                    <span class="text-xs" x-text="removeImage ? 'Image will be removed' : 'No image yet'">No image yet</span>
                </div>
                <span x-show="previewUrl" x-cloak
                      class="absolute top-2 left-2 rounded-md px-2 py-1 text-[11px] leading-none font-bold tracking-[0.5px] text-white uppercase shadow"
                      :class="fileName ? 'bg-success' : 'bg-primary'"
                      x-text="fileName ? 'New – not saved yet' : 'Current'"></span>
            </div>

            {{-- Change image: click or drop a file. The <label> opens the hidden file input. --}}
            <label for="{{ $id('image') }}"
                   x-data="{ dragging: false }"
                   @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                   @drop.prevent="dragging = false; dropImage($event)"
                   :class="dragging ? 'border-primary bg-primary-soft' : 'border-line bg-white hover:bg-page'"
                   @class([
                       'flex size-[186px] cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-3 text-center transition-colors',
                       'border-danger!' => $errorFor('image'),
                   ])>
                <span class="flex size-10 items-center justify-center rounded-full bg-tint text-primary">
                    <x-icon name="upload" class="size-5" />
                </span>
                <span class="text-[15px] font-medium text-ink" x-text="previewUrl ? 'Change image' : 'Upload image'">{{ $product->imageUrl() ? 'Change image' : 'Upload image' }}</span>
                <span class="text-xs text-muted">Drag or click</span>
                <span x-show="fileName" x-text="fileName" x-cloak class="line-clamp-2 text-[11px] font-medium break-all text-primary-dark"></span>
            </label>
            <input id="{{ $id('image') }}" x-ref="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only"
                   @change="previewImage($event.target.files[0])">
        </div>

        <div class="mt-2 flex flex-wrap items-center gap-3">
            <button type="button" x-show="previewUrl" x-cloak @click="clearImage()"
                    class="flex items-center gap-1.5 rounded-lg px-2 py-1 text-[13px] font-medium text-danger hover:bg-danger-soft">
                <x-icon name="trash" class="size-3.5" />
                Remove image
            </button>
            <button type="button" x-show="removeImage" x-cloak @click="undoRemoveImage()"
                    class="rounded-lg px-2 py-1 text-[13px] font-medium text-primary hover:bg-primary-soft">
                Undo remove
            </button>
        </div>
        <p x-show="fileError" x-text="fileError" x-cloak class="{{ $error }}"></p>
        @if ($errorFor('image')) <p class="{{ $error }}">{{ $errorFor('image') }}</p> @endif
    </div>
</div>
