{{--
    Success toast (top right), styled like the Figma design.
    The controller sets it with ->with('toast', ['title' => ..., 'message' => ...]).
    "Flash" session data only exists for the next page load, so it shows once.
--}}
@if (session('toast'))
    <div x-data="toast" x-show="visible" x-cloak
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="-translate-y-3 opacity-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 left-4 z-[70] sm:left-auto sm:w-[491px]"
         role="status" aria-live="polite">
        <div class="flex items-center gap-3 rounded-xl border border-line/70 bg-white py-3 pr-4 pl-4 shadow-lg">
            <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-mint text-success" aria-hidden="true">
                <svg class="size-3" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.5 6.2l2.3 2.3 4.7-5" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-sm leading-5 font-semibold tracking-[0.07px] text-ink">✓ {{ session('toast.title') }}</p>
                @if (session('toast.message'))
                    <p class="text-[11px] leading-[14px] text-muted">{{ session('toast.message') }}</p>
                @endif
            </div>
            <button type="button" class="rounded p-1 text-muted hover:bg-page hover:text-ink" @click="visible = false" aria-label="Dismiss">
                <svg class="size-3.5" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                    <path d="M3.5 3.5l7 7M10.5 3.5l-7 7" />
                </svg>
            </button>
        </div>
    </div>
@endif
