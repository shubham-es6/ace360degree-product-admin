{{-- overflow-x-auto: on narrow screens the table scrolls sideways instead of squashing. --}}
<div class="overflow-x-auto">
    <table class="w-full min-w-[974px] border-collapse text-left">
        <thead class="border-y border-line bg-page">
            <tr class="text-[11px] leading-[14px] font-semibold tracking-[0.55px] text-muted uppercase">
                <th scope="col" class="h-10 w-12 pl-4">
                    <input type="checkbox" class="size-4 cursor-pointer rounded accent-primary" aria-label="Select all products on this page"
                           :checked="ids.length > 0 && selected.length === ids.length"
                           @change="selected = $event.target.checked ? [...ids] : []">
                </th>
                <th scope="col" class="w-24 px-3">Thumbnail</th>
                <th scope="col" class="px-4">Product Name &amp; SKU</th>
                <th scope="col" class="w-[131px] px-4">Category</th>
                <th scope="col" class="w-[77px] px-4 text-right">Price</th>
                <th scope="col" class="w-48 px-4 text-right">Inventory / Stock</th>
                <th scope="col" class="w-[109px] px-4 text-center">Status</th>
                <th scope="col" class="w-[118px] px-4 text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($products as $product)
                <tr class="border-b border-tint transition-colors last:border-b-0 hover:bg-page"
                    :class="selected.includes('{{ $product->id }}') && 'bg-primary-soft/60 hover:bg-primary-soft/60'">

                    <td class="py-3 pl-4">
                        <input type="checkbox" value="{{ $product->id }}" x-model="selected"
                               class="size-4 cursor-pointer rounded accent-primary" aria-label="Select {{ $product->name }}">
                    </td>

                    <td class="px-3 py-3">
                        <span class="flex size-10 items-center justify-center overflow-hidden rounded-lg border border-line bg-page text-subtle shadow-sm">
                            @if ($product->imageUrl())
                                <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="size-full object-cover" loading="lazy">
                            @else
                                <x-icon name="image" class="size-5" />
                            @endif
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <a href="{{ route('products.show', $product) }}" class="block max-w-[171px] text-sm leading-5 font-semibold tracking-[0.07px] text-ink hover:text-primary">
                            {{ $product->name }}
                        </a>
                        <span class="block font-mono text-[11px] leading-[14px] text-muted">SKU: {{ $product->sku }}</span>
                    </td>

                    <td class="px-4 py-3">
                        <span class="inline-block max-w-[99px] rounded-md border border-line/50 bg-tint px-2 py-0.5 text-xs leading-4 text-muted">
                            {{ $product->category }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-right text-sm leading-[18px] font-semibold whitespace-nowrap text-ink">
                        ${{ number_format($product->price, 2) }}
                    </td>

                    <td class="px-4 py-3">
                        <div class="ml-auto w-28">
                            <p @class([
                                'text-right text-[13px] leading-[18px] font-medium whitespace-nowrap',
                                'text-danger' => $product->isLowStock(),
                                'text-subtle' => $product->isOutOfStock(),
                                'text-ink' => ! $product->isLowStock() && ! $product->isOutOfStock(),
                            ])>{{ number_format($product->stock_quantity) }} in stock</p>
                            <div class="mt-1 h-1.5 w-28 overflow-hidden rounded-full bg-tint"
                                 role="meter" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $product->stockPercent() }}"
                                 aria-label="Stock level">
                                <div @class(['h-full rounded-full', 'bg-danger' => $product->isLowStock(), 'bg-success' => ! $product->isLowStock()])
                                     style="width: {{ $product->stockPercent() }}%"></div>
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if ($product->isActive())
                            <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-success/15 bg-success-pill px-2.5 text-[11px] leading-[14px] font-medium text-success">
                                <span class="size-1.5 rounded-full bg-success" aria-hidden="true"></span>Active
                            </span>
                        @else
                            <span class="inline-flex h-5 items-center gap-1.5 rounded-full border border-line bg-inactive-pill px-2.5 text-[11px] leading-[14px] font-medium text-subtle">
                                <span class="size-1.5 rounded-full bg-subtle" aria-hidden="true"></span>Inactive
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('products.show', $product) }}" class="rounded p-1 text-muted hover:bg-tint hover:text-ink"
                               title="View" aria-label="View {{ $product->name }}">
                                <x-icon name="eye" class="size-4" />
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="rounded p-1 text-muted hover:bg-tint hover:text-primary"
                               title="Edit" aria-label="Edit {{ $product->name }}">
                                <x-icon name="pencil" class="size-4" />
                            </a>
                            {{-- Real DELETE request (HTML forms only do GET/POST, so @method fakes DELETE). Confirmation comes in Step 6. --}}
                            <form method="POST" action="{{ route('products.destroy', $product) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded p-1 text-muted hover:bg-danger-soft hover:text-danger"
                                        title="Delete" aria-label="Delete {{ $product->name }}">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
