{{--
    Add / Edit product modal. The SAME partial is used for both:
      - Add:  $product is a new, empty Product  -> POST /products
      - Edit: $product is an existing Product   -> PUT  /products/{id}

    Variables:
      $product   Product being created or edited
      $open      bool – show the modal when the page loads
      $closeUrl  string|null – where "close" goes (null = just hide the modal)
--}}
@php
    $isEdit = $product->exists;

    // Each form gets a key, sent back as a hidden field. After a failed validation
    // Laravel returns the old input + errors; the key tells us which form they belong to,
    // so an error in the Edit form never shows up in the Add form (and vice versa).
    $formKey = $isEdit ? 'edit-' . $product->id : 'create';
    $isMine = old('_form') === $formKey;
    $errorFor = fn (string $field) => $isMine ? $errors->first($field) : null;
    $value = fn (string $field, $default = null) => $isMine ? old($field, $default) : $default;

    // Keep the list's filters in the URL, so after saving you land on the same page.
    $listQuery = \Illuminate\Support\Arr::only(request()->query(), ['search', 'category', 'status', 'sort', 'per_page', 'page']);
    $action = $isEdit
        ? route('products.update', [$product] + $listQuery)
        : route('products.store', $listQuery);

    $id = fn (string $field) => "{$formKey}-{$field}";

    $label = 'block text-[13px] leading-5 font-medium text-ink';
    $input = 'h-9 w-full rounded-lg border bg-white px-3 text-[13px] text-ink placeholder:text-subtle focus:ring-2 focus:outline-none';
    $inputState = fn (string $field) => $errorFor($field)
        ? 'border-danger focus:border-danger focus:ring-danger/20'
        : 'border-line focus:border-primary focus:ring-primary/20';
    $hint = 'mt-1 text-[11px] leading-[14px] text-muted';
    $error = 'mt-1 text-[11px] leading-[14px] font-medium text-danger';

    $statusOptions = [
        'active' => ['label' => 'Active', 'hint' => 'Visible in storefront & POS', 'dot' => 'bg-success'],
        'inactive' => ['label' => 'Inactive', 'hint' => 'Hidden from customer views', 'dot' => 'bg-danger'],
    ];
    $currentStatus = $value('status', $product->status ?? 'active');
@endphp

<div x-data="productModal({ open: @js($open), closeUrl: @js($closeUrl), imageUrl: @js($product->imageUrl()) })"
     @if (! $isEdit) @open-create-product.window="show()" @endif
     @keydown.escape.window="open && close()">

    <div x-cloak x-show="open" x-transition.opacity
         class="fixed inset-0 z-[60] flex items-start justify-center overflow-y-auto bg-ink/40 p-4 backdrop-blur-[2px] sm:py-8"
         @click.self="close()">

        <div x-show="open" x-transition
             class="w-full max-w-[803px] overflow-hidden rounded-xl bg-white shadow-2xl"
             role="dialog" aria-modal="true" aria-labelledby="{{ $id('title') }}">

            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4 sm:px-[22px]">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 id="{{ $id('title') }}" class="text-lg leading-6 font-semibold tracking-[-0.2px] text-ink">
                            {{ $isEdit ? 'Edit Product' : 'Add New Product' }}
                        </h2>
                        @if ($isEdit)
                            <span class="rounded-md bg-tint px-2 py-0.5 font-mono text-[11px] leading-4 font-medium text-muted">{{ $product->sku }}</span>
                        @endif
                    </div>
                    <p class="mt-0.5 text-[13px] leading-[18px] text-muted">
                        {{ $isEdit ? 'Update the product details below.' : 'Fill in the product details below.' }}
                        Starred fields (<span class="text-danger">*</span>) are mandatory for MySQL catalog sync.
                    </p>
                </div>
                <button type="button" class="-mr-1 rounded-lg p-1.5 text-muted hover:bg-page hover:text-ink" @click="close()" aria-label="Close">
                    <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true">
                        <path d="M5 5l10 10M15 5L5 15" />
                    </svg>
                </button>
            </div>

            {{--
                enctype="multipart/form-data" is required for file uploads.
                novalidate: let the server (ProductRequest) do all the validation,
                so errors always appear in the same style under each field.
            --}}
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" novalidate>
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                <input type="hidden" name="_form" value="{{ $formKey }}">

                <div class="grid grid-cols-1 gap-x-[18px] gap-y-4 px-5 py-5 sm:grid-cols-2 sm:px-[22px]">

                    {{-- Product name --}}
                    <div class="sm:col-span-2">
                        <label for="{{ $id('name') }}" class="{{ $label }}">Product Name <span class="text-danger">*</span></label>
                        <input id="{{ $id('name') }}" x-ref="name" type="text" name="name" maxlength="255" autocomplete="off"
                               value="{{ $value('name', $product->name) }}" placeholder="e.g. Wireless Noise-Cancelling Headphones"
                               class="mt-1.5 {{ $input }} {{ $inputState('name') }}">
                        @if ($errorFor('name')) <p class="{{ $error }}">{{ $errorFor('name') }}</p> @endif
                    </div>

                    {{-- SKU + Generate --}}
                    <div>
                        <label for="{{ $id('sku') }}" class="{{ $label }}">SKU (Stock Keeping Unit) <span class="text-danger">*</span></label>
                        <div class="mt-1.5 flex gap-2">
                            <input id="{{ $id('sku') }}" x-ref="sku" type="text" name="sku" maxlength="64" autocomplete="off"
                                   value="{{ $value('sku', $product->sku) }}" placeholder="e.g. ELE-HPH-204"
                                   class="{{ $input }} {{ $inputState('sku') }} uppercase placeholder:normal-case">
                            <button type="button" @click="generateSku()"
                                    class="flex h-9 shrink-0 items-center gap-1.5 rounded-lg border border-line bg-page px-3 text-[13px] font-medium text-muted hover:bg-tint hover:text-ink">
                                <x-icon name="reset" class="size-3.5" />
                                Generate
                            </button>
                        </div>
                        @if ($errorFor('sku'))
                            <p class="{{ $error }}">{{ $errorFor('sku') }}</p>
                        @else
                            <p class="{{ $hint }}" :class="skuHint && 'text-danger! font-medium'" x-text="skuHint || 'Unique alphanumeric identifier used across warehouses.'">Unique alphanumeric identifier used across warehouses.</p>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="{{ $id('category') }}" class="{{ $label }}">Category <span class="text-danger">*</span></label>
                        <div class="relative mt-1.5">
                            <select id="{{ $id('category') }}" x-ref="category" name="category"
                                    class="{{ $input }} {{ $inputState('category') }} appearance-none pr-9">
                                <option value="">Select a category</option>
                                @foreach (\App\Models\Product::CATEGORIES as $category)
                                    <option value="{{ $category }}" @selected($value('category', $product->category) === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            <x-icon name="chevron-down" class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-muted" />
                        </div>
                        @if ($errorFor('category'))
                            <p class="{{ $error }}">{{ $errorFor('category') }}</p>
                        @else
                            <p class="{{ $hint }}">Classifies tax rates and marketplace grouping.</p>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="{{ $id('price') }}" class="{{ $label }}">Regular Price ($) <span class="text-danger">*</span></label>
                        <div class="relative mt-1.5">
                            <span class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-[13px] text-muted">$</span>
                            <input id="{{ $id('price') }}" type="number" name="price" step="0.01" min="0" inputmode="decimal"
                                   value="{{ $value('price', $product->price) }}" placeholder="0.00"
                                   class="{{ $input }} {{ $inputState('price') }} pl-7">
                        </div>
                        @if ($errorFor('price')) <p class="{{ $error }}">{{ $errorFor('price') }}</p> @endif
                    </div>

                    {{-- Stock --}}
                    <div>
                        <label for="{{ $id('stock_quantity') }}" class="{{ $label }}">Stock Quantity <span class="text-danger">*</span></label>
                        <input id="{{ $id('stock_quantity') }}" type="number" name="stock_quantity" step="1" min="0" inputmode="numeric"
                               value="{{ $value('stock_quantity', $product->stock_quantity) }}" placeholder="0"
                               class="mt-1.5 {{ $input }} {{ $inputState('stock_quantity') }}">
                        @if ($errorFor('stock_quantity')) <p class="{{ $error }}">{{ $errorFor('stock_quantity') }}</p> @endif
                    </div>

                    {{-- Status --}}
                    <fieldset class="sm:col-span-2">
                        <legend class="{{ $label }}">Product Publication Status <span class="text-danger">*</span></legend>
                        <div class="mt-1.5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($statusOptions as $status => $option)
                                <label class="group flex cursor-pointer items-center gap-3 rounded-lg border border-line bg-white px-4 py-2.5 transition-colors hover:bg-page has-checked:border-primary has-checked:bg-primary-soft has-checked:hover:bg-primary-soft">
                                    <input type="radio" name="status" value="{{ $status }}" class="size-4 shrink-0 cursor-pointer accent-primary"
                                           @checked($currentStatus === $status)>
                                    <span class="flex flex-col">
                                        <span class="flex items-center gap-1.5 text-[13px] leading-[18px] font-medium text-ink group-has-checked:text-primary-dark">
                                            <span class="size-1.5 rounded-full {{ $option['dot'] }}" aria-hidden="true"></span>
                                            {{ $option['label'] }}
                                        </span>
                                        <span class="text-[11px] leading-[14px] text-muted">{{ $option['hint'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @if ($errorFor('status')) <p class="{{ $error }}">{{ $errorFor('status') }}</p> @endif
                    </fieldset>

                    {{-- Image upload with preview --}}
                    <div class="sm:col-span-2">
                        <span class="{{ $label }}">Product Image <span class="text-[11px] font-normal text-muted">(Optional)</span></span>
                        <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">

                        <div class="mt-1.5 flex flex-col gap-3 sm:flex-row sm:items-center">
                            {{-- Preview --}}
                            <div class="flex size-[72px] shrink-0 items-center justify-center overflow-hidden rounded-lg border border-line bg-page text-subtle">
                                <img x-show="previewUrl" :src="previewUrl" alt="Product image preview" class="size-full object-cover"
                                     @if ($product->imageUrl()) src="{{ $product->imageUrl() }}" @else x-cloak @endif>
                                <x-icon name="image" class="size-7" x-show="!previewUrl" />
                            </div>

                            {{-- Drop zone: the whole box is a <label>, so clicking it opens the file picker --}}
                            <label for="{{ $id('image') }}"
                                   x-data="{ dragging: false }"
                                   @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                   @drop.prevent="dragging = false; dropImage($event)"
                                   :class="dragging ? 'border-primary bg-primary-soft' : 'border-line bg-white hover:bg-page'"
                                   @class([
                                       'flex flex-1 cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed px-4 py-3 text-center transition-colors',
                                       'border-danger!' => $errorFor('image'),
                                   ])>
                                <span class="text-[13px] leading-[18px] text-ink"><span class="font-medium text-primary">Click to upload</span> or drag and drop</span>
                                <span class="text-[11px] leading-[14px] text-muted">JPG, PNG or WEBP · max 2 MB</span>
                                <span x-show="fileName" x-text="fileName" x-cloak class="mt-1 text-[11px] leading-[14px] font-medium text-primary-dark"></span>
                            </label>
                            <input id="{{ $id('image') }}" x-ref="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only"
                                   @change="previewImage($event.target.files[0])">

                            <button type="button" x-show="previewUrl" x-cloak @click="clearImage()"
                                    class="flex h-9 items-center gap-1.5 self-start rounded-lg px-3 text-[13px] font-medium text-danger hover:bg-danger-soft sm:self-center">
                                <x-icon name="trash" class="size-3.5" />
                                Remove
                            </button>
                        </div>
                        <p x-show="fileError" x-text="fileError" x-cloak class="{{ $error }}"></p>
                        @if ($errorFor('image')) <p class="{{ $error }}">{{ $errorFor('image') }}</p> @endif
                    </div>

                    {{-- Description: plain text only (no HTML / rich text) --}}
                    <div class="sm:col-span-2">
                        <div class="flex items-end justify-between gap-2">
                            <label for="{{ $id('description') }}" class="{{ $label }}">Description / Short Summary</label>
                            <span class="text-[11px] leading-[14px] text-muted" aria-live="polite">
                                <span x-text="descriptionLength">{{ mb_strlen((string) $value('description', $product->description)) }}</span> / 2000 chars
                            </span>
                        </div>
                        <textarea id="{{ $id('description') }}" x-ref="description" name="description" rows="3" maxlength="2000"
                                  @input="descriptionLength = $event.target.value.length"
                                  placeholder="A short plain-text summary of the product."
                                  class="mt-1.5 block w-full resize-y rounded-lg border bg-white px-3 py-2.5 text-[13px] leading-5 text-ink placeholder:text-subtle focus:ring-2 focus:outline-none {{ $inputState('description') }}">{{ $value('description', $product->description) }}</textarea>
                        @if ($errorFor('description')) <p class="{{ $error }}">{{ $errorFor('description') }}</p> @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex flex-col-reverse gap-2 border-t border-line bg-page px-5 py-3.5 sm:flex-row sm:justify-end sm:px-[22px]">
                    <button type="button" @click="close()"
                            class="h-9 rounded-lg border border-line bg-white px-4 text-sm font-medium text-ink hover:bg-page">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex h-9 items-center justify-center gap-1.5 rounded-lg bg-primary px-4 text-sm font-medium text-white shadow-sm hover:bg-primary-dark">
                        {{ $isEdit ? 'Save Changes' : 'Save Product' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
