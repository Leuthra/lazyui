{{--
    Appearance sheet (dark mode, accent, font), rendered on every page of every panel.
    Plain markup on purpose: no nested Blade components and no twMerge, so it costs ~1 ms.
    Accents and fonts come from config/lazyui.php (see LazyUI\Support\Appearance).
--}}
@php($appearance = \LazyUI\Support\Appearance::class)

<svg class="hidden" aria-hidden="true"><symbol id="lazy-icon-font" viewBox="0 0 24 24"><path opacity="0.4" d="M17.1229 18.16C16.5754 18.16 16.0833 18.0577 15.6464 17.8531C15.2096 17.6485 14.864 17.3526 14.6096 16.9656C14.3552 16.5729 14.228 16.0974 14.228 15.5389C14.228 14.5988 14.5792 13.8855 15.2814 13.3989C15.9837 12.9067 17.0924 12.6523 18.6076 12.6357L19.6776 12.6192V12.1049C19.6776 11.7123 19.5615 11.4109 19.3292 11.2008C19.1025 10.9851 18.7486 10.88 18.2675 10.8856C17.9136 10.8911 17.5763 10.974 17.2556 11.1344C16.9404 11.2948 16.7247 11.574 16.6086 11.9722H14.593C14.6262 11.3528 14.8059 10.8441 15.1321 10.446C15.4584 10.0423 15.9008 9.74368 16.4593 9.55013C17.0233 9.35106 17.662 9.25153 18.3754 9.25153C19.2546 9.25153 19.9596 9.36212 20.4905 9.58331C21.0214 9.79897 21.4057 10.1114 21.6435 10.5206C21.8812 10.9243 22.0001 11.4054 22.0001 11.9639V17.9941H19.9762L19.7772 16.5176C19.4841 17.137 19.1163 17.5655 18.674 17.8033C18.2371 18.0411 17.7201 18.16 17.1229 18.16ZM17.9357 16.5674C18.1569 16.5674 18.3698 16.5287 18.5744 16.4513C18.7846 16.3683 18.9698 16.2605 19.1302 16.1278C19.2961 15.9896 19.4288 15.8347 19.5283 15.6633C19.6278 15.4919 19.6776 15.3149 19.6776 15.1324V13.8634L18.8067 13.8799C18.403 13.8855 18.027 13.9352 17.6786 14.0292C17.3302 14.1177 17.0482 14.2643 16.8326 14.4689C16.6224 14.6735 16.5174 14.95 16.5174 15.2983C16.5174 15.6965 16.6556 16.0089 16.9321 16.2356C17.2086 16.4568 17.5431 16.5674 17.9357 16.5674Z" fill="currentColor"></path><path d="M2 17.9941L6.29663 6H8.85139L13.1397 17.9941H10.726L9.74721 15.2154H5.43399L4.43033 17.9941H2ZM5.97314 13.1002H9.16658L7.5823 8.33909L5.97314 13.1002Z" fill="currentColor"></path></symbol></svg>
<div
    id="lazySettings"
    x-data="$store.sheet.create({ id: 'lazySettings', noscroll: true })"
    x-cloak
    x-show="$store.sheet.isOpen('lazySettings')"
    x-on:keydown.window.escape="$store.sheet.isOpen('lazySettings') && $store.sheet.close('lazySettings')"
    class="lazy-sheet-wrapper fixed inset-0 z-[61]"
>
    <div class="fixed inset-0" x-on:click="$store.sheet.close('lazySettings')"></div>

    <div
        x-show="$store.sheet.isOpen('lazySettings')"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="absolute top-0 right-0 flex h-full max-w-full flex-col overflow-hidden bg-white shadow-xl lazy-gradient dark:bg-cat-900"
        style="width: min(350px, 100vw)"
        role="dialog"
        aria-modal="true"
        aria-labelledby="lazySettingsTitle"
    >
        <div class="flex items-center justify-between px-4 py-2">
            <h3 id="lazySettingsTitle" class="py-2 font-semibold text-cat-800 dark:text-white">{{ __('Settings') }}</h3>
            <button type="button" class="inline-flex size-8 cursor-pointer items-center justify-center rounded-full text-cat-500 hover:bg-cat-300/50 dark:hover:bg-cat-750" aria-label="{{ __('Close') }}" x-on:click="$store.sheet.close('lazySettings')">
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M345 137c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-119 119L73 103c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l119 119L39 375c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l119-119L311 409c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-119-119L345 137z"/></svg>
            </button>
        </div>

        <div class="h-full grow overflow-y-auto p-5">
            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    id="appearance-toggle"
                    class="block aspect-[7/5] h-full w-full rounded-xl bg-white px-5 py-6 shadow dark:bg-cat-800"
                    x-on:click="$appearance.setTheme($appearance.state.mode === 'dark' ? 'light' : 'dark')"
                >
                    <span class="pointer-events-none flex h-full w-full flex-col justify-between text-start">
                        <span class="flex items-center justify-between">
                            <svg class="size-6 text-cat-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M22 12c0 5.523-4.477 10-10 10a10 10 0 0 1-3.321-.564A9 9 0 0 1 8 18a8.97 8.97 0 0 1 2.138-5.824A6.5 6.5 0 0 0 15.5 15a6.5 6.5 0 0 0 5.567-3.143c.24-.396.933-.32.933.143" clip-rule="evenodd" opacity="0.5"/><path fill="currentColor" d="M2 12c0 4.359 2.789 8.066 6.679 9.435A9 9 0 0 1 8 18c0-2.221.805-4.254 2.138-5.824A6.47 6.47 0 0 1 9 8.5a6.5 6.5 0 0 1 3.143-5.567C12.54 2.693 12.463 2 12 2C6.477 2 2 6.477 2 12"/></svg>
                            <span class="inline-block">
                                <input type="checkbox" tabindex="-1" class="peer sr-only" x-bind:checked="$appearance.state.mode === 'dark'" />
                                <span class="block relative rounded-full min-w-[26px] min-h-[15px] bg-cat-300 dark:bg-cat-700 peer-checked:bg-cat-800 dark:peer-checked:bg-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:size-[11px] after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-full dark:peer-checked:after:bg-cat-800"></span>
                            </span>
                        </span>
                        <span class="text-xs font-semibold">{{ __('Dark mode') }}</span>
                    </span>
                </button>

                <button
                    type="button"
                    id="appearance-auto"
                    class="block aspect-[7/5] h-full w-full rounded-xl bg-white px-5 py-6 shadow dark:bg-cat-800"
                    x-on:click="$appearance.setTheme($appearance.state.mode === 'auto' ? ($appearance.isDark() ? 'dark' : 'light') : 'auto')"
                >
                    <span class="pointer-events-none flex h-full w-full flex-col justify-between text-start">
                        <span class="flex items-center justify-between">
                            <svg class="size-6 text-cat-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2c4.714 0 7.071 0 8.535 1.464c1.08 1.08 1.364 2.647 1.439 5.286L22 9.5H2.026v-.75c.075-2.64.358-4.205 1.438-5.286C4.93 2 7.286 2 12 2" opacity="0.5"/><path fill="currentColor" d="M13 6a1 1 0 1 1-2 0a1 1 0 0 1 2 0m-3 0a1 1 0 1 1-2 0a1 1 0 0 1 2 0M7 6a1 1 0 1 1-2 0a1 1 0 0 1 2 0"/><path fill="currentColor" d="M2 12c0 4.714 0 7.071 1.464 8.535c1.01 1.01 2.446 1.324 4.786 1.421L9 22V9.5H2.026l-.023.75Q2 11.066 2 12" opacity="0.7"/><path fill="currentColor" d="M22 12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22c-.819 0-2.316 0-3-.008V9.5h13l-.003.75Q22 11.066 22 12"/></svg>
                            <span class="inline-block">
                                <input type="checkbox" tabindex="-1" class="peer sr-only" x-bind:checked="$appearance.state.mode === 'auto'" />
                                <span class="block relative rounded-full min-w-[26px] min-h-[15px] bg-cat-300 dark:bg-cat-700 peer-checked:bg-cat-800 dark:peer-checked:bg-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:size-[11px] after:rounded-full after:bg-white after:transition-all peer-checked:after:translate-x-full dark:peer-checked:after:bg-cat-800"></span>
                            </span>
                        </span>
                        <span class="text-xs font-semibold">{{ __('Auto mode') }}</span>
                    </span>
                </button>
            </div>

            @if (count($appearance::accents()) > 1)
                <fieldset class="relative mt-12 rounded-xl border border-cat-300/50 dark:border-cat-750">
                    <legend class="absolute -top-3 left-4 z-[1] rounded-xl bg-cat-800 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-cat-800">{{ __('Accent') }}</legend>
                    <div class="grid grid-cols-3 justify-items-center gap-3 p-5 pt-7">
                        @foreach (array_keys($appearance::accents()) as $accent)
                            <button
                                type="button"
                                title="{{ \Illuminate\Support\Str::headline($accent) }}"
                                aria-label="{{ \Illuminate\Support\Str::headline($accent) }}"
                                class="flex aspect-square h-16 cursor-pointer items-center justify-center rounded-xl hover:bg-cat-500/10 [&.active]:bg-accent-500/10"
                                x-on:click="$appearance.setAccent(@js($accent))"
                                x-bind:class="{ 'active': $appearance.state.accent === @js($accent) }"
                            >
                                <span class="size-4 rounded-full transition-transform duration-300 ease-in-out [.active_&]:scale-150" style="background-color: {{ $appearance::swatch($accent) }}"></span>
                            </button>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            @if (count($appearance::fonts()) > 1)
                <fieldset class="relative mt-12 rounded-xl border border-cat-300/50 dark:border-cat-750">
                    <legend class="absolute -top-3 left-4 z-[1] rounded-xl bg-cat-800 px-3 py-1 text-xs font-semibold text-white dark:bg-white dark:text-cat-800">{{ __('Font') }}</legend>
                    <div class="grid grid-cols-2 justify-items-center gap-3 p-5 pt-7">
                        @foreach ($appearance::fonts() as $key => $font)
                            <button
                                type="button"
                                class="flex h-20 w-full cursor-pointer flex-col items-center justify-center gap-y-3 rounded-xl text-cat-500 hover:bg-cat-500/10 [&.active]:bg-white [&.active]:text-cat-900 [&.active]:shadow dark:[&.active]:bg-cat-500/10 dark:[&.active]:text-white"
                                style="font-family: {{ str_replace(['{', '}', ';', '<', '"'], '', $font['family']) }}"
                                x-on:click="$appearance.setFont(@js($key))"
                                x-bind:class="{ 'active': $appearance.state.font === @js($key) }"
                            >
                                <svg class="size-6 [.active_&]:text-accent-500" aria-hidden="true"><use href="#lazy-icon-font" /></svg>
                                <span class="text-xs font-medium">{{ $font['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </fieldset>
            @endif
        </div>

        <div class="p-3">
            <button type="button" class="inline-flex h-9 w-full cursor-pointer items-center justify-center gap-1.5 rounded-md bg-cat-900 px-4 text-sm font-medium text-white shadow hover:bg-cat-800/90 dark:bg-white dark:text-cat-900 dark:hover:bg-white/90" x-on:click="$appearance.resetAppearance()">
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0,0,512,512"><path d="M472 224c13.3 0 24-10.7 24-24l0-144c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 80.1-20-23.5C387 63.4 325.1 32 256 32C132.3 32 32 132.3 32 256s100.3 224 224 224c50.4 0 97-16.7 134.4-44.8c10.6-8 12.7-23 4.8-33.6s-23-12.7-33.6-4.8C332.2 418.9 295.7 432 256 432c-97.2 0-176-78.8-176-176s78.8-176 176-176c54.3 0 102.9 24.6 135.2 63.4l.1 .2s0 0 0 0L418.9 176 328 176c-13.3 0-24 10.7-24 24s10.7 24 24 24l144 0z" fill="currentColor" style="opacity: 1;"></path></svg>
                {{ __('Reset settings') }}
            </button>
        </div>
    </div>
</div>
