<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class Products extends Datatable
{
    protected ?string $recordName = 'Product';

    public array $categoryOptions = [];

    public function mount(): void
    {
        $this->categoryOptions = Category::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function title(): string
    {
        return __('Products');
    }

    protected function description(): ?string
    {
        return __('Manage the products offered in the store');
    }

    protected function query(): Builder
    {
        return Product::query()
            ->with('category')
            ->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Product'),
                'format' => function (Product $product) {
                    return sprintf(
                        '<div>
                            <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                            <p class="text-xs text-neutral-500">%s</p>
                        </div>',
                        e($product->name),
                        e($product->slug)
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Category'),
                'type' => 'text',
                'field' => 'category.name',
                'default' => __('None'),
            ],
            [
                'label' => __('Price'),
                'type' => 'decimal',
                'field' => 'price',
                'align' => 'right',
                'decimals' => 2,
            ],
            [
                'label' => __('Cost'),
                'type' => 'decimal',
                'field' => 'cost_price',
                'default' => __('Not specified'),
                'decimals' => 2,
                'align' => 'right',
            ],
            [
                'label' => __('Stock'),
                'type' => 'integer',
                'field' => 'stock',
                'align' => 'right',
            ],
            [
                'label' => __('Featured'),
                'type' => 'checkbox',
                'field' => 'is_featured',
                'checked_label' => __('Yes'),
                'unchecked_label' => __('No'),
            ],
            [
                'label' => __('Status'),
                'type' => 'badge',
                'field' => 'is_active',
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
        ];
    }

    protected function formFields(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => __('Name'),
                'placeholder' => __('Product name'),
            ],
            'slug' => [
                'type' => 'text',
                'label' => __('Slug'),
                'placeholder' => __('product-slug'),
            ],
            'description' => [
                'type' => 'textarea',
                'label' => __('Description'),
                'default' => '',
            ],
            'price' => [
                'type' => 'number',
                'label' => __('Price'),
                'placeholder' => '0.00',
            ],
            'cost_price' => [
                'type' => 'number',
                'label' => __('Cost'),
                'placeholder' => '0.00',
            ],
            'stock' => [
                'type' => 'number',
                'label' => __('Stock'),
                'default' => 0,
            ],
            'category_id' => [
                'type' => 'select',
                'label' => __('Category'),
                'options' => $this->categoryOptions,
                'placeholder' => __('Select a category'),
            ],
            'is_featured' => [
                'type' => 'checkbox',
                'label' => __('Featured'),
                'default' => false,
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
                Rule::unique('products', 'slug')->ignore($recordId),
            ],
            'formData.description' => ['nullable', 'string'],
            'formData.price' => ['required', 'numeric', 'min:0'],
            'formData.cost_price' => ['nullable', 'numeric', 'min:0'],
            'formData.stock' => ['nullable', 'integer', 'min:0'],
            'formData.category_id' => ['nullable', Rule::exists('categories', 'id')],
            'formData.is_featured' => ['boolean'],
            'formData.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.slug' => __('Slug'),
            'formData.description' => __('Description'),
            'formData.price' => __('Price'),
            'formData.cost_price' => __('Cost'),
            'formData.stock' => __('Stock'),
            'formData.category_id' => __('Category'),
            'formData.is_featured' => __('Featured'),
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
