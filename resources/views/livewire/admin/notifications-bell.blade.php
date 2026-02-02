<div class="relative" wire:poll.60s="refreshNotifications">
    <button
        type="button"
        class="relative flex items-center justify-center rounded-full border border-white/20 bg-white/5 p-2 text-white transition hover:bg-white/10"
        wire:click="toggle"
    >
        <flux:icon.bell class="h-5 w-5" />
        @if ($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 inline-flex h-3 w-3 items-center justify-center rounded-full bg-rose-500 text-[10px] font-semibold text-white"></span>
        @endif
    </button>

    @if ($open)
        <div class="absolute right-0 z-20 mt-3 w-72 rounded-xl border border-neutral-200 bg-white p-3 text-sm shadow-2xl dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-2 flex items-center justify-between text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                <span>{{ __('Reservas recientes') }}</span>
                <button type="button" wire:click="markAllAsRead" class="text-primary-600 hover:underline">
                    {{ __('Marcar todo como leído') }}
                </button>
            </div>

            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse ($notifications as $notification)
                    @php($notificationUrl = $notification->data['url'] ?? null)
                    <a
                        href="{{ $notificationUrl ?: '#' }}"
                        class="block rounded-lg border px-3 py-2 text-sm transition hover:border-primary-300 hover:bg-primary-50/40 dark:hover:border-primary-500/40 dark:hover:bg-primary-500/5 @if(is_null($notification->read_at)) border-primary-200 bg-primary-50/60 dark:border-primary-500/30 dark:bg-primary-500/10 @else border-neutral-200 dark:border-neutral-700 @endif"
                        wire:click.prevent="openNotification('{{ $notification->id }}')"
                    >
                        <p class="font-semibold text-neutral-900 dark:text-white">
                            {{ $notification->data['title'] ?? __('Nueva reserva') }}
                        </p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                        <p class="mt-1 text-neutral-700 dark:text-neutral-200">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        <div class="mt-2 flex items-center justify-between text-xs text-neutral-500">
                            <span class="font-mono text-xs text-neutral-600 dark:text-neutral-300">#{{ $notification->data['code'] ?? '' }}</span>
                            @if (is_null($notification->read_at))
                                <span class="text-primary-600">{{ __('Ver detalle') }}</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="text-center text-xs text-neutral-500 dark:text-neutral-400">
                        {{ __('Sin notificaciones pendientes') }}
                    </p>
                @endforelse
            </div>
        </div>
    @endif
</div>
