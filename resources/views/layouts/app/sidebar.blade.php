<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body
    class="min-h-screen
    bg-[radial-gradient(circle_at_top_left,_#ffd6e7_0%,_transparent_35%),radial-gradient(circle_at_bottom_right,_#ffc2d1_0%,_transparent_30%),linear-gradient(135deg,#fff0f6_0%,#ffe5ec_40%,#ffd6e7_100%)]
    bg-fixed">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-pink-200 bg-gradient-to-b from-pink-100 via-pink-50 to-rose-100 backdrop-blur-xl shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if (auth()->user()?->role === 'admin')
                        <flux:sidebar.item icon="rectangle-group" :href="route('framecategories.index')" :current="request()->routeIs('framecategories.index')" wire:navigate>
                            {{ __('Frame Categories') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="swatch" :href="route('photoframes.index')" :current="request()->routeIs('photoframes.index')" wire:navigate>
                            {{ __('Photo Frames') }}
                        </flux:sidebar.item>
                    @endif
                    <flux:sidebar.item icon="camera" :href="route('userphotos.index')" :current="request()->routeIs('userphotos.index')" wire:navigate>
                        {{ __('User Photos') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="sparkles" :href="route('photoboxresults.index')" :current="request()->routeIs('photoboxresults.index')" wire:navigate>
                        {{ __('Photobox Results') }}
                    </flux:sidebar.item>

                    <flux:sidebar.item icon="arrow-down-tray" :href="route('downloads.index')" :current="request()->routeIs('downloads.index')" wire:navigate>
                        {{ __('Downloads') }}
                    </flux:sidebar.item>
                    
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>