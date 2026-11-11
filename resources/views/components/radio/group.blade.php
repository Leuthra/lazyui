@props([
    'name' => '',
    'defaultValue' => '',
])

<div
    x-data="{
        __selected: @js($defaultValue ?: null),
        name: @js($name),
    }"
    x-modelable="__selected"
    {{ $attributes->twMerge('grid gap-2') }}
>
    {{ $slot }}
</div>
