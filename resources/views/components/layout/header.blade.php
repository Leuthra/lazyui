<header class="lazy-header">
    <nav>
        <div class="lg:hidden mr-3">
            <x-lazy::button
                variant="ghost"
                size="icon"
                class="rounded-full"
                aria-label="Open menu"
                x-data
                x-on:click="$store.lazySidebar.toggle()"
            >
                <svg class="size-5 text-cat-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" opacity=".4" d="M64 256c0-17.7 14.3-32 32-32H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H96c-17.7 0-32-14.3-32-32z"/><path fill="currentColor" d="M448 96c0-17.7-14.3-32-32-32H32C14.3 64 0 78.3 0 96s14.3 32 32 32H416c17.7 0 32-14.3 32-32zm0 320c0-17.7-14.3-32-32-32H32c-17.7 0-32 14.3-32 32s14.3 32 32 32H416c17.7 0 32-14.3 32-32z"/></svg>
            </x-lazy::button>
        </div>

        <div class="lazy-header-icon">
            <div>{{ $start ?? '' }}</div>
            <div class="flex gap-1 items-center">
                {{ $slot }}
                <x-lazy::settings-button />
                {{ $user ?? '' }}
            </div>
        </div>
    </nav>
</header>
