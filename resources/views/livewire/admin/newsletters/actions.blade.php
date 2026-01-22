<div class="flex justify-end gap-2">
    <button
        type="button"
        wire:click="sendNow({{ $newsletter->getKey() }})"
        class="inline-flex items-center rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-700/20"
    >
        {{ __('Enviar ahora') }}
    </button>
    <button
        type="button"
        wire:click="openEditModal({{ $newsletter->getKey() }})"
        class="inline-flex items-center rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-700/20"
    >
        {{ __('Editar') }}
    </button>
    <button
        type="button"
        wire:click="confirmDelete({{ $newsletter->getKey() }})"
        class="inline-flex items-center rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-200 dark:hover:bg-rose-500/10"
    >
        {{ __('Eliminar') }}
    </button>
</div>
