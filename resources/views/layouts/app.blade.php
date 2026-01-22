<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="space-y-6">
        @auth
            <div class="flex justify-end">
                @livewire('admin.notifications-bell')
            </div>
        @endauth
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
