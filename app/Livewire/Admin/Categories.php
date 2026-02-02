<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Categories extends Datatable
{
    protected ?string $recordName = 'Category';

    protected function title(): string
    {
        return __('Categories');
    }

    protected function description(): ?string
    {
        return __('Manage the categories available in the catalog');
    }

    protected function query(): Builder
    {
        return Category::query()
            ->with('parent')
            ->orderBy('order');
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Name'),
                'priority' => 1,
                'format' => function (Category $category) {
                    return sprintf(
                        '<div>
                            <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                            <p class="text-xs text-neutral-500">%s</p>
                        </div>',
                        e($category->name),
                        e($category->slug)
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Parent'),
                'type' => 'text',
                'field' => 'parent.name',
                'default' => __('None'),
                'priority' => 2,
            ],
            [
                'label' => __('Status'),
                'type' => 'badge',
                'field' => 'is_active',
                'priority' => 1,
                'options' => [
                    true => [
                        'label' => __('Active'),
                        'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
                    ],
                    false => [
                        'label' => __('Inactive'),
                        'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100',
                    ],
                ],
            ],
            [
                'label' => __('Order'),
                'type' => 'integer',
                'field' => 'order',
                'priority' => 3,
            ],
            [
                'label' => __('Updated'),
                'type' => 'datetime',
                'field' => 'updated_at',
                'format_string' => 'Y-m-d H:i',
                'align' => 'right',
                'priority' => 4,
            ],
        ];
    }

    protected function availableFilters(): array
    {
        return [
            'search' => [
                'type' => 'text',
                'label' => __('Search'),
                'placeholder' => __('Search by name or slug'),
                'debounce' => 500,
                'apply' => function (Builder $query, string $value): void {
                    $query->where(function (Builder $inner) use ($value) {
                        $inner->where('name', 'like', '%'.$value.'%')
                            ->orWhere('slug', 'like', '%'.$value.'%');
                    });
                },
            ],
            'status' => [
                'type' => 'select',
                'label' => __('Status'),
                'placeholder' => __('All statuses'),
                'options' => [
                    'active' => __('Active'),
                    'inactive' => __('Inactive'),
                ],
                'apply' => function (Builder $query, string $value): void {
                    if ($value === 'active') {
                        $query->where('is_active', true);
                    } elseif ($value === 'inactive') {
                        $query->where('is_active', false);
                    }
                },
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => __('Name'),
                'placeholder' => __('Category name'),
            ],
            'slug' => [
                'type' => 'text',
                'label' => __('Slug'),
                'placeholder' => __('url-friendly-slug'),
            ],
            'description' => [
                'type' => 'textarea',
                'label' => __('Description'),
                'default' => '',
            ],
            'order' => [
                'type' => 'number',
                'label' => __('Order'),
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'checkbox',
                'label' => __('Active'),
                'default' => true,
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($recordId),
            ],
            'formData.description' => ['nullable', 'string'],
            'formData.order' => ['nullable', 'integer', 'min:0'],
            'formData.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.slug' => __('Slug'),
            'formData.description' => __('Description'),
            'formData.order' => __('Order'),
            'formData.is_active' => __('Active'),
        ];
    }

    protected function createRecord(array $data): Model
    {
        $data = $this->ensureSlug($data);

        return parent::createRecord($data);
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        $data = $this->ensureSlug($data);

        return parent::updateRecord($record, $data);
    }

    protected function ensureSlug(array $data): array
    {
        if (empty($data['slug']) && ! empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $data;
    }
}
