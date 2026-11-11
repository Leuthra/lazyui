{{-- Filament already loads Inter, so only other configured font stylesheets are added. --}}
<x-lazy::head :assets="false" :except-fonts="['inter']" :tone="$tone ?? null" />
