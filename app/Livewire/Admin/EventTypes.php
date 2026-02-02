<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventTypes extends Datatable
{
    protected ?string $recordName = 'Event Type';

    public array $categoryOptions = [];

    public function mount(): void
    {
        $this->categoryOptions = Category::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function title(): string
    {
        return __('Event Types');
    }

    protected function description(): ?string
    {
        return __('Manage types that classify your events');
    }

    protected function query(): Builder
    {
        return EventType::query()
            ->with('category')
            ->withCount('events')
            ->orderBy('name');
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Name'),
                'priority' => 1,
                'format' => fn (EventType $eventType) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($eventType->name),
                    e($eventType->slug)
                ),
                'html' => true,
            ],
            [
                'label' => __('Category'),
                'type' => 'text',
                'field' => 'category.name',
                'default' => __('None'),
                'priority' => 1,
            ],
            [
                'label' => __('Events'),
                'type' => 'integer',
                'field' => 'events_count',
                'align' => 'right',
                'priority' => 2,
            ],
            [
                'label' => __('Description'),
                'type' => 'text',
                'field' => 'description',
                'default' => __('Not specified'),
                'limit' => 80,
                'priority' => 3,
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'name' => [
                'type' => 'text',
                'label' => __('Name'),
                'placeholder' => __('Event type name'),
            ],
            'category_id' => [
                'type' => 'select',
                'label' => __('Category'),
                'options' => $this->categoryOptions,
                'placeholder' => __('Select a category'),
            ],
            'description' => [
                'type' => 'textarea',
                'label' => __('Description'),
                'default' => '',
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.category_id' => ['nullable', Rule::exists('categories', 'id')],
            'formData.description' => ['nullable', 'string'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.category_id' => __('Category'),
            'formData.description' => __('Description'),
        ];
    }

    protected function createRecord(array $data): Model
    {
        $data['slug'] = $this->generateSlug($data['name']);

        return parent::createRecord($data);
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        $data['slug'] = $this->generateSlug($data['name'], $record->id);

        return parent::updateRecord($record, $data);
    }

    protected function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = EventType::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
