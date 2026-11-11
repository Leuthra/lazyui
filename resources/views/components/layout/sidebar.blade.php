<aside class="lazy-sidebar" x-data>
    <div class="lazy-sidebar-ghost-layout"></div>

    <button
        type="button"
        aria-label="Toggle sidebar"
        x-on:click="$appearance.setAside('toggle')"
        class="lazy-sidebar-toggle"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M41.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 256 246.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg>
    </button>

    <div
        x-trap.inert.noscroll="$store.lazySidebar.isOpen"
        x-on:keydown.escape.window="$store.lazySidebar.close()"
        x-bind:class="{ 'translate-x-0': $store.lazySidebar.isOpen }"
        class="lazy-sidebar-wrapper lazy-gradient"
    >
        <div class="lazy-sidebar-body">
            <div class="lazy-sidebar-header">
                @isset($brand)
                    {{ $brand }}
                @else
                    <a class="lazy-sidebar-brand" href="{{ url('/') }}" aria-label="Brand">{{ config('app.name') }}</a>
                @endisset
                <x-lazy::button
                    variant="ghost"
                    size="icon"
                    class="lg:hidden rounded-full size-8 -mr-2 text-cat-500"
                    aria-label="Close menu"
                    x-on:click="$store.lazySidebar.close()"
                >
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M345 137c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-119 119L73 103c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l119 119L39 375c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l119-119L311 409c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-119-119L345 137z"/></svg>
                </x-lazy::button>
            </div>
            <div class="lazy-sidebar-scroll-content">
                <nav>
                    {{ $slot }}
                </nav>
            </div>
        </div>
    </div>

    <div
        x-show="$store.lazySidebar.isOpen"
        x-transition.opacity
        x-cloak
        x-on:click="$store.lazySidebar.close()"
        class="lazy-sidebar-backdrop"
    ></div>
</aside>
