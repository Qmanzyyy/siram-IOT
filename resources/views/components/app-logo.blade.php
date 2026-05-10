@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Smart Farm" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-green-600 text-white">
            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L4 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-8-5zm0 2.18l6 3.75v7.07c0 4.45-2.93 8.6-6 9.8-3.07-1.2-6-5.35-6-9.8V7.93l6-3.75z"/>
            </svg>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Smart Farm" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-green-600 text-white">
            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L4 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-8-5zm0 2.18l6 3.75v7.07c0 4.45-2.93 8.6-6 9.8-3.07-1.2-6-5.35-6-9.8V7.93l6-3.75z"/>
            </svg>
        </x-slot>
    </flux:brand>
@endif
