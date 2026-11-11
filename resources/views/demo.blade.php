{{--
    LazyUI playground: every component on one page. Used by the test suite and handy while
    developing the package. Render it from any route: Route::view('/lazyui', 'lazy::demo');
--}}
@php($admin = rescue(fn () => url(filament()->getDefaultPanel()->getPath()), null, report: false))

<x-lazy::layout title="Components">
    <x-slot:menu>
        <x-lazy::nav.section label="Get started">
            <x-lazy::nav.item href="{{ url()->current() }}" :is-active="true">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M2 6.5c0-2.121 0-3.182.659-3.841S4.379 2 6.5 2s3.182 0 3.841.659S11 4.379 11 6.5s0 3.182-.659 3.841S8.621 11 6.5 11s-3.182 0-3.841-.659S2 8.621 2 6.5m11 11c0-2.121 0-3.182.659-3.841S15.379 13 17.5 13s3.182 0 3.841.659S22 15.379 22 17.5s0 3.182-.659 3.841S19.621 22 17.5 22s-3.182 0-3.841-.659S13 19.621 13 17.5"/><path fill="currentColor" d="M2 17.5c0-2.121 0-3.182.659-3.841S4.379 13 6.5 13s3.182 0 3.841.659S11 15.379 11 17.5s0 3.182-.659 3.841S8.621 22 6.5 22s-3.182 0-3.841-.659S2 19.621 2 17.5m11-11c0-2.121 0-3.182.659-3.841S15.379 2 17.5 2s3.182 0 3.841.659S22 4.379 22 6.5s0 3.182-.659 3.841S19.621 11 17.5 11s-3.182 0-3.841-.659S13 8.621 13 6.5" opacity="0.5"/></svg>
                </x-slot:icon>
                <x-slot:title>Components</x-slot:title>
            </x-lazy::nav.item>

            @if ($admin)
            <x-lazy::nav.item href="{{ $admin }}">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M3.464 20.536C4.93 22 7.286 22 12 22s7.071 0 8.535-1.465C22 19.072 22 16.714 22 12s0-7.071-1.465-8.536C19.072 2 16.714 2 12 2S4.929 2 3.464 3.464C2 4.93 2 7.286 2 12s0 7.071 1.464 8.535" opacity="0.5"/><path fill="currentColor" d="M7 17.75a.75.75 0 0 1-.75-.75v-5a.75.75 0 0 1 1.5 0v5a.75.75 0 0 1-.75.75m5 0a.75.75 0 0 1-.75-.75V7a.75.75 0 0 1 1.5 0v10a.75.75 0 0 1-.75.75m5 0a.75.75 0 0 1-.75-.75v-3a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-.75.75"/></svg>
                </x-slot:icon>
                <x-slot:title>Admin panel</x-slot:title>
            </x-lazy::nav.item>
            @endif
        </x-lazy::nav.section>

        <x-lazy::nav.section label="Examples">
            <x-lazy::nav.item-collapse>
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M3 10c0-3.771 0-5.657 1.172-6.828S7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172S21 6.229 21 10v4c0 3.771 0 5.657-1.172 6.828S16.771 22 13 22h-2c-3.771 0-5.657 0-6.828-1.172S3 17.771 3 14z" opacity="0.5"/><path fill="currentColor" fill-rule="evenodd" d="M7.25 12a.75.75 0 0 1 .75-.75h8a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75m0-4A.75.75 0 0 1 8 7.25h8a.75.75 0 0 1 0 1.5H8A.75.75 0 0 1 7.25 8m0 8a.75.75 0 0 1 .75-.75h5a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75" clip-rule="evenodd"/></svg>
                </x-slot:icon>
                <x-slot:title>Forms</x-slot:title>
                <x-slot:sub>
                    <x-lazy::nav.sub-item href="#inputs"><x-slot:title>Inputs</x-slot:title></x-lazy::nav.sub-item>
                    <x-lazy::nav.sub-item href="#choices"><x-slot:title>Choices</x-slot:title></x-lazy::nav.sub-item>
                </x-slot:sub>
            </x-lazy::nav.item-collapse>
        </x-lazy::nav.section>
    </x-slot:menu>

    <x-slot:user>
        <x-lazy::dropdown class="ml-2">
            <x-lazy::dropdown.trigger>
                <x-lazy::avatar class="size-9 cursor-pointer">
                    <x-lazy::avatar.image src="{{ asset('vendor/lazyui/image/avatar.webp') }}" alt="Avatar" />
                    <x-lazy::avatar.fallback>LU</x-lazy::avatar.fallback>
                </x-lazy::avatar>
            </x-lazy::dropdown.trigger>
            <x-lazy::dropdown.content class="w-44" align="end">
                <x-lazy::dropdown.label>My account</x-lazy::dropdown.label>
                <x-lazy::dropdown.separator />
                @if ($admin)
                    <x-lazy::dropdown.link href="{{ $admin }}">Admin panel</x-lazy::dropdown.link>
                @endif
                <x-lazy::dropdown.item x-on:click="$appearance.setTheme('toggle')">
                    Toggle dark mode
                    <x-lazy::dropdown.shortcut>Ctrl D</x-lazy::dropdown.shortcut>
                </x-lazy::dropdown.item>
            </x-lazy::dropdown.content>
        </x-lazy::dropdown>
    </x-slot:user>

    <div class="lazy-container-sm space-y-6">
        <div class="space-y-2">
            <x-lazy::breadcrumb>
                <x-lazy::breadcrumb.item><x-lazy::breadcrumb.link href="{{ url('/') }}">Home</x-lazy::breadcrumb.link></x-lazy::breadcrumb.item>
                <x-lazy::breadcrumb.separator />
                <x-lazy::breadcrumb.item>Components</x-lazy::breadcrumb.item>
            </x-lazy::breadcrumb>
            <h1 class="text-2xl font-semibold tracking-tight">LazyUI components</h1>
            <p class="text-sm text-cat-500 max-w-2xl">Blade + Alpine components for Laravel 13, Livewire 4 and Filament 5. Change dark mode, accent and font from the gear in the header. @if ($admin) The <a class="underline" href="{{ $admin }}">admin panel</a> uses the same settings. @endif</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 [&>*]:min-w-0">
            <x-lazy::card class="p-5 space-y-4">
                <h2 class="text-sm font-semibold">Buttons & badges</h2>
                <div class="flex flex-wrap gap-2">
                    <x-lazy::button>Default</x-lazy::button>
                    <x-lazy::button variant="secondary">Secondary</x-lazy::button>
                    <x-lazy::button variant="outline">Outline</x-lazy::button>
                    <x-lazy::button variant="ghost">Ghost</x-lazy::button>
                    <x-lazy::button variant="destructive">Destructive</x-lazy::button>
                    <x-lazy::button variant="link">Link</x-lazy::button>
                    <x-lazy::button class="bg-accent-500 dark:bg-accent-500 text-white dark:text-white hover:bg-accent-600 dark:hover:bg-accent-600">Accent</x-lazy::button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-lazy::badge>Default</x-lazy::badge>
                    <x-lazy::badge variant="secondary">Secondary</x-lazy::badge>
                    <x-lazy::badge variant="outline">Outline</x-lazy::badge>
                    <x-lazy::badge variant="destructive">Destructive</x-lazy::badge>
                </div>
            </x-lazy::card>

            <x-lazy::card class="p-5 space-y-4">
                <h2 class="text-sm font-semibold">Overlays & feedback</h2>
                <div class="flex flex-wrap gap-2" x-data>
                    <x-lazy::button variant="outline" x-dialog:toggle="demoDialog">Dialog</x-lazy::button>
                    <x-lazy::button variant="outline" x-sheet:toggle="demoSheet">Sheet</x-lazy::button>
                    <x-lazy::button variant="outline" x-on:click="toast.success({ title: 'Saved', message: 'Your changes are stored.' })">Toast</x-lazy::button>
                    <x-lazy::button variant="outline" x-on:click="zalert({ type: 'warning', title: 'Delete project?', text: 'This cannot be undone.', confirmText: 'Delete', cancelText: 'Cancel' })">Alert</x-lazy::button>
                    <x-lazy::popover placement="bottom-start">
                        <x-slot:toggle><x-lazy::button variant="outline">Popover</x-lazy::button></x-slot:toggle>
                        <x-slot:content><div class="p-4 text-sm w-60">Popovers anchor to their trigger and close on outside click.</div></x-slot:content>
                    </x-lazy::popover>
                </div>

                <x-lazy::dropdown>
                    <x-lazy::dropdown.trigger><x-lazy::button variant="secondary">Dropdown</x-lazy::button></x-lazy::dropdown.trigger>
                    <x-lazy::dropdown.content class="w-48" align="start">
                        <x-lazy::dropdown.label>Project</x-lazy::dropdown.label>
                        <x-lazy::dropdown.separator />
                        <x-lazy::dropdown.item>Rename <x-lazy::dropdown.shortcut>F2</x-lazy::dropdown.shortcut></x-lazy::dropdown.item>
                        <x-lazy::dropdown.sub>
                            <x-lazy::dropdown.sub.trigger>Share</x-lazy::dropdown.sub.trigger>
                            <x-lazy::dropdown.sub.content class="w-40">
                                <x-lazy::dropdown.item>Email</x-lazy::dropdown.item>
                                <x-lazy::dropdown.item>Copy link</x-lazy::dropdown.item>
                            </x-lazy::dropdown.sub.content>
                        </x-lazy::dropdown.sub>
                        <x-lazy::dropdown.item disabled>Archive</x-lazy::dropdown.item>
                    </x-lazy::dropdown.content>
                </x-lazy::dropdown>
            </x-lazy::card>

            <x-lazy::card class="p-5 space-y-4" id="inputs">
                <h2 class="text-sm font-semibold">Inputs</h2>
                <div class="grid gap-2">
                    <x-lazy::label for="name">Name</x-lazy::label>
                    <x-lazy::input id="name" placeholder="Ilsya" />
                </div>
                <div class="grid gap-2">
                    <x-lazy::label for="framework">Framework</x-lazy::label>
                    <x-lazy::select id="framework" name="framework">
                        <x-lazy::select.option value="laravel"><x-slot:label>Laravel</x-slot:label><x-slot:description>PHP framework</x-slot:description></x-lazy::select.option>
                        <x-lazy::select.option value="filament"><x-slot:label>Filament</x-slot:label><x-slot:description>Admin panels</x-slot:description></x-lazy::select.option>
                        <x-lazy::select.option value="livewire"><x-slot:label>Livewire</x-slot:label><x-slot:description>Reactive components</x-slot:description></x-lazy::select.option>
                    </x-lazy::select>
                </div>
                <div class="grid gap-2">
                    <x-lazy::label>Tags</x-lazy::label>
                    <x-lazy::autocomplete.multiple :suggestions="['laravel', 'filament', 'livewire', 'alpine', 'tailwind']" checkbox name="tags" />
                </div>
                <div class="grid gap-2">
                    <x-lazy::label for="bio">Bio</x-lazy::label>
                    <x-lazy::textarea id="bio" rows="3" class="w-full" placeholder="Tell us a little about yourself" />
                </div>
            </x-lazy::card>

            <x-lazy::card class="p-5 space-y-5" id="choices">
                <h2 class="text-sm font-semibold">Choices</h2>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <label class="flex items-center gap-2 text-sm"><x-lazy::checkbox checked /> Default</label>
                    <label class="flex items-center gap-2 text-sm"><x-lazy::checkbox variant="accent" checked /> Accent</label>
                    <div class="flex items-center gap-2 text-sm"><x-lazy::switch checked /> Switch</div>
                    <div class="flex items-center gap-2 text-sm"><x-lazy::switch variant="accent" checked /> Accent</div>
                </div>
                <x-lazy::radio.group name="plan" default-value="pro" class="grid-cols-1 gap-3 sm:grid-cols-3">
                    <label class="flex items-center gap-2 text-sm"><x-lazy::radio.item value="free" /> Free</label>
                    <label class="flex items-center gap-2 text-sm"><x-lazy::radio.item value="pro" variant="accent" /> Pro</label>
                    <label class="flex items-center gap-2 text-sm"><x-lazy::radio.item value="team" /> Team</label>
                </x-lazy::radio.group>

                <x-lazy::tabs>
                    <x-lazy::tabs.buttons>
                        <x-lazy::tabs.button>Account</x-lazy::tabs.button>
                        <x-lazy::tabs.button>Password</x-lazy::tabs.button>
                    </x-lazy::tabs.buttons>
                    <x-lazy::tabs.contents class="p-0 pt-3 text-sm text-cat-500">
                        <x-lazy::tabs.content>Change your account details here.</x-lazy::tabs.content>
                        <x-lazy::tabs.content>Change your password here.</x-lazy::tabs.content>
                    </x-lazy::tabs.contents>
                </x-lazy::tabs>

                <x-lazy::accordion type="multiple">
                    <x-lazy::accordion.item value="a">
                        <x-lazy::accordion.trigger>Is it accessible?</x-lazy::accordion.trigger>
                        <x-lazy::accordion.content>Yes. Keyboard navigation works for dropdowns, selects and dialogs.</x-lazy::accordion.content>
                    </x-lazy::accordion.item>
                    <x-lazy::accordion.item value="b">
                        <x-lazy::accordion.trigger>Can I edit a component?</x-lazy::accordion.trigger>
                        <x-lazy::accordion.content>Run <code>php artisan lazy:publish button</code> and edit the copy in resources/views/vendor/lazy.</x-lazy::accordion.content>
                    </x-lazy::accordion.item>
                </x-lazy::accordion>
            </x-lazy::card>
        </div>
    </div>

    <x-lazy::dialog id="demoDialog">
        <x-lazy::dialog.header />
        <x-lazy::dialog.body class="px-6 pb-6">
            <h3 class="text-lg font-semibold">Edit profile</h3>
            <p class="text-sm text-cat-500 mt-1">Make changes to your profile here.</p>
            <div class="grid gap-2 mt-4">
                <x-lazy::label for="dialog-name">Name</x-lazy::label>
                <x-lazy::input id="dialog-name" value="Ilsya" />
            </div>
            <div class="flex justify-end mt-5">
                <x-lazy::button x-dialog:close>Save changes</x-lazy::button>
            </div>
        </x-lazy::dialog.body>
    </x-lazy::dialog>

    <x-lazy::sheet id="demoSheet" placement="right">
        <x-lazy::sheet.header title="Notifications" />
        <x-lazy::sheet.body class="p-4 text-sm text-cat-500">You're all caught up.</x-lazy::sheet.body>
    </x-lazy::sheet>
</x-lazy::layout>
