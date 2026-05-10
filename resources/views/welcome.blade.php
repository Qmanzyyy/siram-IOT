<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head', ['title' => 'Welcome'])
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-2xl flex-col gap-8">
                <!-- Logo & Brand -->
                <div class="flex flex-col items-center gap-4">
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium">
                        <span class="flex h-16 w-16 items-center justify-center rounded-lg bg-zinc-900 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                            <x-app-logo-icon class="size-10 fill-current text-zinc-900 dark:text-zinc-100" />
                        </span>
                    </a>

                    <div class="flex flex-col items-center gap-2 text-center">
                        <flux:heading size="2xl" class="text-zinc-900 dark:text-zinc-100">Smart Farm</flux:heading>
                        <flux:subheading class="text-zinc-600 dark:text-zinc-400">
                            Sistem Manajemen Pertanian Cerdas
                        </flux:subheading>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="flex flex-col gap-6 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 shadow-sm">
                    <!-- Features Grid -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">Kontrol Perangkat IoT</flux:heading>
                                <flux:subheading class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Kendalikan perangkat pertanian secara real-time
                                </flux:subheading>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">Penjadwalan Otomatis</flux:heading>
                                <flux:subheading class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Atur jadwal penyiraman dan pemeliharaan
                                </flux:subheading>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">Monitoring Real-time</flux:heading>
                                <flux:subheading class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Pantau kondisi lahan secara langsung
                                </flux:subheading>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-zinc-100 dark:bg-zinc-800">
                                <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <flux:heading size="sm" class="text-zinc-900 dark:text-zinc-100">Dashboard Interaktif</flux:heading>
                                <flux:subheading class="text-sm text-zinc-600 dark:text-zinc-400">
                                    Visualisasi data yang mudah dipahami
                                </flux:subheading>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        @auth
                            <flux:button variant="primary" href="{{ route('dashboard') }}" wire:navigate class="w-full">
                                {{ __('Go to Dashboard') }}
                            </flux:button>
                        @else
                            <flux:button variant="primary" href="{{ route('login') }}" wire:navigate class="w-full">
                                {{ __('Log in') }}
                            </flux:button>

                            @if (Route::has('register'))
                                <flux:button variant="ghost" href="{{ route('register') }}" wire:navigate class="w-full">
                                    {{ __('Create an account') }}
                                </flux:button>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Footer -->
