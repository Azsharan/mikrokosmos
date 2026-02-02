<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

abstract class Datatable extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected int $perPage = 10;
    protected bool $showActionButtons = true;
    protected ?string $recordName = null;
    protected int $mobilePerPage = 5;

    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    public string $formMode = 'create';
    public array $formData = [];
    public ?int $editingId = null;
    public ?int $deleteId = null;
    public array $filters = [];

    abstract protected function query(): Builder;

    abstract protected function columns(): array;

    abstract protected function title(): string;

    protected function description(): ?string
    {
        return null;
    }

    protected function emptyMessage(): string
    {
        return __('No records found');
    }

    public function mount(): void
    {
        $this->filters = $this->defaultFilterValues();
    }

    public function render(): View
    {
        $items = $this->buildPaginator();

        return view('livewire.admin.datatable', [
            'title' => $this->title(),
            'description' => $this->description(),
            'columns' => $this->columns(),
            'items' => $items,
            'emptyMessage' => $this->emptyMessage(),
            'datatable' => $this,
            'filterDefinitions' => $this->availableFilters(),
        ]);
    }

    protected function buildPaginator(): LengthAwarePaginator
    {
        $query = $this->applyFiltersToQuery($this->query());

        return $query->paginate($this->getPerPage());
    }

    public function hasForm(): bool
    {
        return ! empty($this->formFields());
    }

    public function showActionColumn(): bool
    {
        return $this->showActionButtons && $this->hasForm();
    }

    public function resourceLabel(): string
    {
        if ($this->recordName) {
            return $this->recordName;
        }

        $model = $this->query()->getModel();

        return Str::headline(class_basename($model));
    }

    protected function getPerPage(): int
    {
        return $this->isMobileRequest() ? $this->mobilePerPage : $this->perPage;
    }

    protected function isMobileRequest(): bool
    {
        $agent = request()->userAgent();

        if (! $agent) {
            return false;
        }

        return (bool) preg_match('/Mobile|Android|iP(ad|hone)|IEMobile|Kindle|Silk/i', $agent);
    }

    public function openCreateModal(): void
    {
        if (! $this->hasForm()) {
            return;
        }

        $this->formMode = 'create';
        $this->editingId = null;
        $this->formData = $this->newFormData();
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function openEditModal(int $recordId): void
    {
        if (! $this->hasForm()) {
            return;
        }

        $record = $this->findRecord($recordId);
        $this->formMode = 'edit';
        $this->editingId = $recordId;
        $this->formData = $this->formDataFromRecord($record);
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function saveRecord(): void
    {
        if (! $this->hasForm()) {
            return;
        }

        $recordId = $this->formMode === 'edit' ? $this->editingId : null;
        $data = $this->validatedFormData($recordId);

        if ($this->formMode === 'edit' && $recordId) {
            $record = $this->findRecord($recordId);
            $this->updateRecord($record, $data);
        } else {
            $this->createRecord($data);
        }

        $this->showFormModal = false;
        $this->editingId = null;
        $this->formData = $this->newFormData();
        $this->resetValidation();
        $this->resetPage();
    }

    public function confirmDelete(int $recordId): void
    {
        $this->deleteId = $recordId;
        $this->showDeleteModal = true;
    }

    public function deleteRecord(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $record = $this->findRecord($this->deleteId);
        $this->deleteRecordInstance($record);
        $this->deleteId = null;
        $this->showDeleteModal = false;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->filters = $this->defaultFilterValues();
        $this->resetPage();
    }

    public function updated($name, $value): void
    {
        if (str_starts_with($name, 'filters.')) {
            $this->resetPage();
            $this->dispatch('$refresh');
        }
    }

    public function renderCell($item, array $column): array
    {
        if (isset($column['format']) && is_callable($column['format'])) {
            return [
                'value' => call_user_func($column['format'], $item, $column),
                'html' => $column['html'] ?? true,
            ];
        }

        if (isset($column['view'])) {
            return [
                'value' => view($column['view'], ['item' => $item, 'column' => $column])->render(),
                'html' => true,
            ];
        }

        $type = $column['type'] ?? 'text';
        $value = array_key_exists('field', $column) ? data_get($item, $column['field']) : null;

        return match ($type) {
            'badge' => $this->renderBadgeCell($value, $column),
            'boolean' => $this->renderBooleanCell($value, $column),
            'datetime' => $this->renderDatetimeCell($value, $column),
            'date' => $this->renderDateCell($value, $column),
            'text' => $this->renderTextCell($value, $column),
            'integer' => $this->renderIntegerCell($value, $column),
            'decimal', 'number' => $this->renderDecimalCell($value, $column),
            'checkbox' => $this->renderCheckboxCell($value, $column),
            'html' => ['value' => $value ?? '', 'html' => true],
            default => [
                'value' => $value ?? ($column['default'] ?? ''),
                'html' => $column['html'] ?? false,
            ],
        };
    }

    protected function formFields(): array
    {
        return [];
    }

    protected function availableFilters(): array
    {
        return [];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [];
    }

    protected function validationAttributes(): array
    {
        return [];
    }

    protected function createRecord(array $data): Model
    {
        $record = $this->newModelInstance();
        $record->fill($data);
        $record->save();

        return $record;
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        $record->fill($data);
        $record->save();

        return $record;
    }

    protected function deleteRecordInstance(Model $record): void
    {
        $record->delete();
    }

    protected function newModelInstance(): Model
    {
        return $this->query()->getModel()->newInstance();
    }

    protected function findRecord(int $recordId): Model
    {
        return (clone $this->query())->whereKey($recordId)->firstOrFail();
    }

    protected function newFormData(): array
    {
        $defaults = [];

        foreach ($this->formFields() as $field => $config) {
            $defaults[$field] = $config['default'] ?? null;
        }

        return $defaults;
    }

    protected function formDataFromRecord(Model $record): array
    {
        return Arr::only($record->toArray(), array_keys($this->formFields()));
    }

    protected function validatedFormData(?int $recordId = null): array
    {
        $rules = $this->validationRules($recordId);

        if (! empty($rules)) {
            $validated = $this->validate($rules, [], $this->validationAttributes());
            if (array_key_exists('formData', $validated)) {
                return $validated['formData'];
            }

            return $validated;
        }

        return Arr::only($this->formData, array_keys($this->formFields()));
    }

    protected function defaultFilterValues(): array
    {
        $defaults = [];

        foreach ($this->availableFilters() as $key => $definition) {
            $defaults[$key] = $definition['default'] ?? null;
        }

        return $defaults;
    }

    protected function applyFiltersToQuery(Builder $query): Builder
    {
        foreach ($this->availableFilters() as $key => $definition) {
            $value = $this->filters[$key] ?? null;

            if ($this->filterValueIsEmpty($value)) {
                continue;
            }

            if (isset($definition['apply']) && is_callable($definition['apply'])) {
                $definition['apply']($query, $value, $definition);

                continue;
            }

            if (isset($definition['field'])) {
                $operator = $definition['operator'] ?? '=';
                $query->where($definition['field'], $operator, $value);
            }
        }

        return $query;
    }

    protected function filterValueIsEmpty($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (! $this->filterValueIsEmpty($item)) {
                    return false;
                }
            }

            return true;
        }

        return $value === null || $value === '';
    }

    protected function renderBadgeCell($value, array $column): array
    {
        $option = $this->matchOption($value, $column['options'] ?? []);

        if ($option) {
            $label = $option['label'] ?? $value;
            $class = $option['class'] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700/50 dark:text-neutral-300';
        } else {
            $label = $value ?? ($column['default'] ?? '');
            $class = $column['class'] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700/50 dark:text-neutral-300';
        }

        return [
            'value' => sprintf('<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold %s">%s</span>', $class, e($label)),
            'html' => true,
        ];
    }

    protected function renderBooleanCell($value, array $column): array
    {
        $isTrue = (bool) $value;
        $label = $isTrue ? ($column['true_label'] ?? __('Yes')) : ($column['false_label'] ?? __('No'));
        $class = $isTrue
            ? ($column['true_class'] ?? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100')
            : ($column['false_class'] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700/50 dark:text-neutral-300');

        return [
            'value' => sprintf('<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold %s">%s</span>', $class, e($label)),
            'html' => true,
        ];
    }

    protected function renderDatetimeCell($value, array $column): array
    {
        $format = $column['format_string'] ?? 'Y-m-d H:i';

        return [
            'value' => view('livewire.fields.datetime', [
                'value' => $value,
                'format' => $format,
                'default' => $column['default'] ?? '',
                'class' => $column['text_class'] ?? null,
            ])->render(),
            'html' => true,
        ];
    }

    protected function renderDateCell($value, array $column): array
    {
        return [
            'value' => view('livewire.fields.date', [
                'value' => $value,
                'format' => $column['format_string'] ?? 'Y-m-d',
                'default' => $column['default'] ?? '',
                'class' => $column['text_class'] ?? null,
            ])->render(),
            'html' => true,
        ];
    }

    protected function matchOption($value, array $options): ?array
    {
        foreach ($options as $key => $option) {
            if ((string) $key === (string) $value) {
                return $option;
            }
        }

        return null;
    }

    protected function renderTextCell($value, array $column): array
    {
        return [
            'value' => view('livewire.fields.text', [
                'value' => $value ?? ($column['default'] ?? ''),
                'limit' => $column['limit'] ?? null,
                'class' => $column['text_class'] ?? null,
            ])->render(),
            'html' => true,
        ];
    }

    protected function renderIntegerCell($value, array $column): array
    {
        return [
            'value' => view('livewire.fields.integer', [
                'value' => $value,
                'default' => $column['default'] ?? '',
                'class' => $column['text_class'] ?? null,
                'prefix' => $column['prefix'] ?? '',
                'suffix' => $column['suffix'] ?? '',
                'decimal_separator' => $column['decimal_separator'] ?? '.',
                'thousand_separator' => $column['thousand_separator'] ?? ',',
            ])->render(),
            'html' => true,
        ];
    }

    protected function renderDecimalCell($value, array $column): array
    {
        return [
            'value' => view('livewire.fields.decimal', [
                'value' => $value,
                'default' => $column['default'] ?? '',
                'class' => $column['text_class'] ?? null,
                'prefix' => $column['prefix'] ?? '',
                'suffix' => $column['suffix'] ?? '',
                'decimals' => $column['decimals'] ?? 2,
                'decimal_separator' => $column['decimal_separator'] ?? '.',
                'thousand_separator' => $column['thousand_separator'] ?? ',',
            ])->render(),
            'html' => true,
        ];
    }

    protected function renderCheckboxCell($value, array $column): array
    {
        $checked = (bool) $value;
        $label = $checked ? ($column['checked_label'] ?? __('Yes')) : ($column['unchecked_label'] ?? __('No'));

        return [
            'value' => view('livewire.fields.checkbox', [
                'checked' => $checked,
                'label' => $label,
                'class' => $column['text_class'] ?? null,
            ])->render(),
            'html' => true,
        ];
    }
}
