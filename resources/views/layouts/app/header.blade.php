<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-[#2b1a52] via-[#4b2b7f] to-[#120b1f] text-white">
        <flux:header container class="border-b border-[#e6c45c]/50 bg-gradient-to-r from-[#2c1d54] via-[#5a38a6] to-[#bda8ff] text-white shadow-2xl shadow-[#2b1a52]/60">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden text-white">
                <flux:navbar.item class="text-white/80 hover:text-[#ffe599] transition" icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item class="text-white/80 hover:text-[#ffe599] transition" icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>
                    {{ __('Categories') }}
                </flux:navbar.item>
                <flux:navbar.item class="text-white/80 hover:text-[#ffe599] transition" icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>
                    {{ __('Products') }}
                </flux:navbar.item>
                <flux:navbar.dropdown label="{{ __('Events') }}" icon="calendar" :current="request()->routeIs('admin.events.*') || request()->routeIs('admin.event-registrations.*') || request()->routeIs('admin.event-types.*')">
                    <flux:navbar.item :href="route('admin.events.index')" :current="request()->routeIs('admin.events.*')" wire:navigate>
                        {{ __('Events') }}
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('admin.event-registrations.index')" :current="request()->routeIs('admin.event-registrations.*')" wire:navigate>
                        {{ __('Event Registrations') }}
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('admin.event-types.index')" :current="request()->routeIs('admin.event-types.*')" wire:navigate>
                        {{ __('Event Types') }}
                    </flux:navbar.item>
                </flux:navbar.dropdown>
                <flux:navbar.item class="text-white/80 hover:text-[#ffe599] transition" icon="users" :href="route('admin.shop-users.index')" :current="request()->routeIs('admin.shop-users.*')" wire:navigate>
                    {{ __('Shop Users') }}
                </flux:navbar.item>
                <flux:navbar.item class="text-white/80 hover:text-[#ffe599] transition" icon="shield-check" :href="route('admin.staff.index')" :current="request()->routeIs('admin.staff.*')" wire:navigate>
                    {{ __('Staff Users') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0! text-white">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5 text-white/80 hover:text-[#ffe599]" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
                <flux:tooltip :content="__('Repository')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5 text-white/80 hover:text-[#ffe599]"
                        icon="folder-git-2"
                        href="https://github.com/laravel/livewire-starter-kit"
                        target="_blank"
                        :label="__('Repository')"
                    />
                </flux:tooltip>
                <flux:tooltip :content="__('Documentation')" position="bottom">
                    <flux:navbar.item
                        class="h-10 max-lg:hidden [&>div>svg]:size-5 text-white/80 hover:text-[#ffe599]"
                        icon="book-open-text"
                        href="https://laravel.com/docs/starter-kits#livewire"
                        target="_blank"
                        label="Documentation"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="lg:hidden border-e border-[#e6c45c]/40 bg-gradient-to-b from-[#2c1b58] via-[#4c2d83] to-[#c9b8ff] text-white">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="text-[#f6d98f]">
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard')  }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>
                        {{ __('Categories') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>
                        {{ __('Products') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="calendar" :href="route('admin.events.index')" :current="request()->routeIs('admin.events.*')" wire:navigate>
                        {{ __('Events') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="clipboard-document-check" :href="route('admin.event-registrations.index')" :current="request()->routeIs('admin.event-registrations.*')" wire:navigate>
                        {{ __('Event Registrations') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="bookmark-square" :href="route('admin.event-types.index')" :current="request()->routeIs('admin.event-types.*')" wire:navigate>
                        {{ __('Event Types') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="users" :href="route('admin.shop-users.index')" :current="request()->routeIs('admin.shop-users.*')" wire:navigate>
                        {{ __('Shop Users') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item class="text-white/80 hover:text-[#ffe599] hover:bg-white/10" icon="shield-check" :href="route('admin.staff.index')" :current="request()->routeIs('admin.staff.*')" wire:navigate>
                        {{ __('Staff Users') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item class="text-white/80 hover:text-[#7ab44c] hover:bg-white/10" icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    {{ __('Repository') }}
                </flux:sidebar.item>
                <flux:sidebar.item class="text-white/80 hover:text-[#7ab44c] hover:bg-white/10" icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    {{ __('Documentation') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
