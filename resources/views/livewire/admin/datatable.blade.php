@php
    $formFields = $datatable->formFields();
    $hasForm = !empty($formFields);
    $resourceLabel = $datatable->resourceLabel();
    $filterDefinitions = $filterDefinitions ?? [];
    $hasFilters = !empty($filterDefinitions);
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $title }}</h1>
            @if ($description)
                <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
        @if ($hasForm)
            <div class="flex justify-end">
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    {{ __('New :resource', ['resource' => $resourceLabel]) }}
                </button>
            </div>
        @endif
    </div>

    @if ($hasFilters)
        <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-neutral-700 dark:text-neutral-200">{{ __('Filters') }}</p>
                <button
                    type="button"
                    wire:click="resetFilters"
                    class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-900"
                >
                    {{ __('Reset filters') }}
                </button>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($filterDefinitions as $key => $filter)
                    @php
                        $type = $filter['type'] ?? 'text';
                        $label = $filter['label'] ?? \Illuminate\Support\Str::headline($key);
                        $placeholder = $filter['placeholder'] ?? '';
                        $model = "filters.$key";
                        $binding = 'wire:model.live';
                        if (!empty($filter['lazy'])) {
                            $binding = 'wire:model.lazy';
                        } elseif (!empty($filter['debounce'])) {
                            $binding = 'wire:model.live.debounce.'.$filter['debounce'].'ms';
                        }
                    @endphp
                    <div class="space-y-2" wire:key="filter-{{ $key }}">
                        @unless($type === 'checkbox')
                            <label class="text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                {{ $label }}
                            </label>
                        @endunless

                        @switch($type)
                            @case('select')
                                <select
                                    class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                    {{ $binding }}="{{ $model }}"
                                >
                                    @if ($placeholder)
                                        <option value="">{{ $placeholder }}</option>
                                    @endif
                                    @foreach (($filter['options'] ?? []) as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                                @break

                            @case('checkbox')
                                <label class="flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300">
                                    <input
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                                        {{ $binding }}="{{ $model }}"
                                    >
                                    <span>{{ $placeholder ?: $label }}</span>
                                </label>
                                @break

                            @default
                                <input
                                    type="{{ in_array($type, ['number', 'email']) ? $type : 'text' }}"
                                    class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                    placeholder="{{ $placeholder }}"
                                    {{ $binding }}="{{ $model }}"
                                >
                                @break
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-800">
            <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                <tr>
                    @foreach ($columns as $column)
                        @php
                            $align = $column['align'] ?? 'left';
                        @endphp
                        <th scope="col" class="{{ $column['th_class'] ?? 'px-6 py-3' }} {{ $align === 'right' ? 'text-right' : '' }}">
                            {{ $column['label'] }}
                        </th>
                    @endforeach
                    @if ($datatable->showActionColumn())
                        <th scope="col" class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-200 bg-white text-sm dark:divide-neutral-800 dark:bg-neutral-900">
                @forelse ($items as $item)
                    <tr>
                        @foreach ($columns as $column)
                            @php
                                $align = $column['align'] ?? 'left';
                                $tdClass = $column['td_class'] ?? 'px-6 py-4';
                                $cell = $datatable->renderCell($item, $column);
                            @endphp
                            <td class="{{ $tdClass }} {{ $align === 'right' ? 'text-right' : '' }}">
                                @if ($cell['html'])
                                    {!! $cell['value'] !!}
                                @else
                                    {{ $cell['value'] }}
                                @endif
                            </td>
                        @endforeach
                        @if ($datatable->showActionColumn())
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button
                                        type="button"
                                        wire:click="openEditModal({{ $item->getKey() }})"
                                        class="rounded-lg border border-neutral-200 px-3 py-1 text-sm text-neutral-700 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                                    >
                                        {{ __('Edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="confirmDelete({{ $item->getKey() }})"
                                        class="rounded-lg border border-rose-200 px-3 py-1 text-sm text-rose-700 hover:bg-rose-50 dark:border-rose-500/30 dark:text-rose-200 dark:hover:bg-rose-500/10"
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="px-6 py-6 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $emptyMessage }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $items->links() }}
    </div>

    @if ($hasForm && $showFormModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">
                    {{ $formMode === 'create' ? __('New :resource', ['resource' => $resourceLabel]) : __('Edit :resource', ['resource' => $resourceLabel]) }}
                </h2>
                <form class="mt-6 grid gap-4 md:grid-cols-2" wire:submit.prevent="saveRecord" enctype="multipart/form-data">
                    @foreach ($formFields as $field => $config)
                        @php
                            $type = $config['type'] ?? 'text';
                            $label = $config['label'] ?? \Illuminate\Support\Str::headline($field);
                            $placeholder = $config['placeholder'] ?? '';
                            $options = $config['options'] ?? [];
                            $fullWidth = $config['full_width'] ?? in_array($type, ['textarea']);
                            $currentValue = $formData[$field] ?? null;
                            $preview = $config['preview'] ?? null;
                            $binding = $config['binding'] ?? 'formData.'.$field;
                            $errorKey = $config['error_key'] ?? $binding;
                        @endphp
                        <div class="space-y-1 {{ $fullWidth ? 'md:col-span-2' : '' }}">
                            <label for="form-{{ $field }}" class="text-sm font-medium text-neutral-700 dark:text-neutral-200">
                                {{ $label }}
                            </label>
                            @switch($type)
                                @case('textarea')
                                    <textarea
                                        id="form-{{ $field }}"
                                        wire:model.defer="{{ $binding }}"
                                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                        rows="3"
                                        placeholder="{{ $placeholder }}"
                                    ></textarea>
                                    @break

                                @case('number')
                                    <input
                                        type="number"
                                        id="form-{{ $field }}"
                                        wire:model.defer="{{ $binding }}"
                                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                        placeholder="{{ $placeholder }}"
                                    >
                                    @break

                                @case('checkbox')
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            id="form-{{ $field }}"
                                            wire:model.defer="{{ $binding }}"
                                            class="h-4 w-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                                        >
                                        <span class="text-sm text-neutral-700 dark:text-neutral-200">{{ $placeholder ?: $label }}</span>
                                    </div>
                                    @break

                                @case('select')
                                    <select
                                        id="form-{{ $field }}"
                                        wire:model.defer="{{ $binding }}"
                                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                    >
                                        <option value="">{{ $placeholder ?: __('Select an option') }}</option>
                                        @foreach ($options as $optionValue => $optionLabel)
                                            <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('file')
                                    <input
                                        type="file"
                                        id="form-{{ $field }}"
                                        wire:model.live="{{ $binding }}"
                                        class="w-full rounded-lg border border-dashed border-neutral-300 px-3 py-4 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                    >
                                    @if ($preview)
                                        <div class="mt-3 flex items-center gap-3 rounded-lg border border-neutral-200 px-3 py-2 text-xs text-neutral-600 dark:border-neutral-700 dark:text-neutral-300">
                                            <img src="{{ $preview }}" alt="{{ $label }}" class="h-12 w-12 rounded object-cover" onerror="this.style.display='none';">
                                            <a href="{{ $preview }}" target="_blank" class="font-semibold text-primary-600 hover:underline">
                                                {{ __('View current file') }}
                                            </a>
                                        </div>
                                    @endif
                                    @break

                                @default
                                    <input
                                        type="{{ in_array($type, ['email', 'password']) ? $type : 'text' }}"
                                        id="form-{{ $field }}"
                                        wire:model.defer="{{ $binding }}"
                                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                                        placeholder="{{ $placeholder }}"
                                    >
                            @endswitch

                            @if (!empty($config['help']))
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $config['help'] }}</p>
                            @endif

                            @error($errorKey)
                                <p class="text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="md:col-span-2 flex justify-end gap-3">
                        <button
                            type="button"
                            wire:click="$set('showFormModal', false)"
                            class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button
                            type="submit"
                            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                        >
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
                <h2 class="text-xl font-semibold text-neutral-900 dark:text-white">{{ __('Delete record') }}</h2>
                <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                    {{ __('Are you sure you want to delete this record? This action cannot be undone.') }}
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="$set('showDeleteModal', false)"
                        class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 focus:outline-none focus:ring-2 focus:ring-neutral-500 focus:ring-offset-2 dark:border-neutral-700 dark:text-neutral-200 dark:hover:bg-neutral-800"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button
                        type="button"
                        wire:click="deleteRecord"
                        class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                    >
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
