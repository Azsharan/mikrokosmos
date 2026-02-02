<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#28164d] via-[#3f2372] to-[#0f081c] text-white">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#e6c45c]/50 bg-gradient-to-b from-[#2b1855] via-[#4a2980] to-[#c5b2ff] text-white shadow-lg shadow-[#2b1855]/60">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            @php($eventsMenuOpen = request()->routeIs('admin.events.*') || request()->routeIs('admin.event-registrations.*') || request()->routeIs('admin.event-types.*'))
            @php($usersMenuOpen = request()->routeIs('admin.shop-users.*') || request()->routeIs('admin.staff.*'))

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid text-[#f6d98f]">
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>
                        {{ __('Categories') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>
                        {{ __('Products') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="clipboard-document-check" :href="route('admin.reservations.index')" :current="request()->routeIs('admin.reservations.*')" wire:navigate>
                        {{ __('Reservations') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="queue-list" :href="route('admin.table-reservations.index')" :current="request()->routeIs('admin.table-reservations.*')" wire:navigate>
                        {{ __('Table Reservations') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="envelope" :href="route('admin.newsletters.index')" :current="request()->routeIs('admin.newsletters.*')" wire:navigate>
                        {{ __('Newsletters') }}
                    </flux:sidebar.item>
                    <details class="group text-[#f6d98f] pl-3" {{ $eventsMenuOpen ? 'open' : '' }}>
                        <summary class="flex cursor-pointer items-center justify-between rounded-lg px-2 py-2 text-white/80 hover:text-[#ffe599] hover:bg-white/10 [&::-webkit-details-marker]:hidden">
                            <span class="flex items-center gap-2 font-medium">
                                <flux:icon.calendar class="size-4" />
                                {{ __('Events') }}
                            </span>
                            <flux:icon.chevron-down class="size-4 text-white/70 transition duration-150 group-open:rotate-180" />
                        </summary>
                        <div class="mt-2 space-y-1 pl-4">
                            <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="calendar" :href="route('admin.events.index')" :current="request()->routeIs('admin.events.*')" wire:navigate>
                                {{ __('Events') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="clipboard-document-check" :href="route('admin.event-registrations.index')" :current="request()->routeIs('admin.event-registrations.*')" wire:navigate>
                                {{ __('Event Registrations') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="bookmark-square" :href="route('admin.event-types.index')" :current="request()->routeIs('admin.event-types.*')" wire:navigate>
                                {{ __('Event Types') }}
                            </flux:sidebar.item>
                        </div>
                    </details>
                    <details class="group text-[#f6d98f] pl-3" {{ $usersMenuOpen ? 'open' : '' }}>
                        <summary class="flex cursor-pointer items-center justify-between rounded-lg px-2 py-2 text-white/80 hover:text-[#ffe599] hover:bg-white/10 [&::-webkit-details-marker]:hidden">
                            <span class="flex items-center gap-2 font-medium">
                                <flux:icon.users class="size-4" />
                                {{ __('User Management') }}
                            </span>
                            <flux:icon.chevron-down class="size-4 text-white/70 transition duration-150 group-open:rotate-180" />
                        </summary>
                        <div class="mt-2 space-y-1 pl-4">
                            <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="users" :href="route('admin.shop-users.index')" :current="request()->routeIs('admin.shop-users.*')" wire:navigate>
                                {{ __('Shop Users') }}
                            </flux:sidebar.item>
                            <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="shield-check" :href="route('admin.staff.index')" :current="request()->routeIs('admin.staff.*')" wire:navigate>
                                {{ __('Staff Users') }}
                            </flux:sidebar.item>
                        </div>
                    </details>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block text-white" :name="auth()->user()->name" />
        </flux:sidebar>


        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-gradient-to-r from-[#2b194e] via-[#5b3aa7] to-[#c7b3ff] text-white border-t border-[#e6c45c]/40">
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
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}
        @fluxScripts
    </body>
</html>
