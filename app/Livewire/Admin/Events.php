<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class Events extends Datatable
{
    protected ?string $recordName = 'Event';

    public array $categoryOptions = [];
    public array $eventTypeOptions = [];
    public array $typeOptions = [
        'casual' => 'Casual',
        'tournament' => 'Tournament',
        'workshop' => 'Workshop',
        'community' => 'Community',
        'online' => 'Online',
    ];

    public function mount(): void
    {
        $this->categoryOptions = Category::query()->orderBy('name')->pluck('name', 'id')->toArray();
        $this->eventTypeOptions = EventType::query()->orderBy('name')->pluck('name', 'id')->toArray();
    }

    protected function title(): string
    {
        return __('Events');
    }

    protected function description(): ?string
    {
        return __('Manage scheduled events across your community');
    }

    protected function query(): Builder
    {
        return Event::query()
            ->with(['eventType', 'category'])
            ->orderByDesc('start_at');
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Event'),
                'priority' => 1,
                'format' => fn (Event $event) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($event->name),
                    e($event->slug)
                ),
                'html' => true,
            ],
            [
                'label' => __('Type'),
                'priority' => 4,
                'format' => function (Event $event) {
                    $type = e(Str::headline($event->type));
                    $extra = $event->eventType ? sprintf('<p class="text-xs text-neutral-500">%s</p>', e($event->eventType->name)) : '';

                    return sprintf('<div>%s%s</div>', $type, $extra);
                },
                'html' => true,
            ],
            [
                'label' => __('Category'),
                'type' => 'text',
                'field' => 'category.name',
                'default' => __('None'),
                'priority' => 4,
            ],
            [
                'label' => __('Start'),
                'type' => 'datetime',
                'field' => 'start_at',
                'format_string' => 'Y-m-d H:i',
                'priority' => 1,
            ],
            [
                'label' => __('End'),
                'type' => 'datetime',
                'field' => 'end_at',
                'format_string' => 'Y-m-d H:i',
                'default' => __('Not specified'),
                'priority' => 2,
            ],
            [
                'label' => __('Location'),
                'type' => 'text',
                'field' => 'location',
                'default' => __('Not specified'),
                'priority' => 3,
            ],
            [
                'label' => __('Online'),
                'type' => 'badge',
                'field' => 'is_online',
                'priority' => 4,
                'options' => [
                    true => [
                        'label' => __('Yes'),
                        'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
                    ],
                    false => [
                        'label' => __('No'),
                        'class' => 'bg-neutral-100 text-neutral-600 dark:bg-neutral-700/50 dark:text-neutral-300',
                    ],
                ],
            ],
            [
                'label' => __('Published'),
                'type' => 'badge',
                'field' => 'is_published',
                'priority' => 1,
                'options' => [
                    true => [
                        'label' => __('Published'),
                        'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
                    ],
                    false => [
                        'label' => __('Draft'),
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
                'placeholder' => __('Event name'),
            ],
            'slug' => [
                'type' => 'text',
                'label' => __('Slug'),
                'placeholder' => __('event-slug'),
            ],
            'description' => [
                'type' => 'textarea',
                'label' => __('Description'),
                'default' => '',
            ],
            'type' => [
                'type' => 'select',
                'label' => __('Type'),
                'options' => $this->typeOptions,
                'placeholder' => __('Select a type'),
            ],
            'event_type_id' => [
                'type' => 'select',
                'label' => __('Event Type'),
                'options' => $this->eventTypeOptions,
                'placeholder' => __('Select an event type'),
            ],
            'category_id' => [
                'type' => 'select',
                'label' => __('Category'),
                'options' => $this->categoryOptions,
                'placeholder' => __('Select a category'),
            ],
            'start_at' => [
                'type' => 'text',
                'label' => __('Start'),
                'placeholder' => '2026-01-20 10:00',
            ],
            'end_at' => [
                'type' => 'text',
                'label' => __('End'),
                'placeholder' => '2026-01-20 12:00',
            ],
            'location' => [
                'type' => 'text',
                'label' => __('Location'),
            ],
            'is_online' => [
                'type' => 'checkbox',
                'label' => __('Online'),
                'default' => false,
            ],
            'capacity' => [
                'type' => 'number',
                'label' => __('Capacity'),
                'default' => 10,
            ],
            'is_published' => [
                'type' => 'checkbox',
                'label' => __('Published'),
                'default' => false,
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
                Rule::unique('events', 'slug')->ignore($recordId),
            ],
            'formData.description' => ['nullable', 'string'],
            'formData.type' => ['required', Rule::in(array_keys($this->typeOptions))],
            'formData.event_type_id' => ['nullable', Rule::exists('event_types', 'id')],
            'formData.category_id' => ['nullable', Rule::exists('categories', 'id')],
            'formData.start_at' => ['required', 'date'],
            'formData.end_at' => ['nullable', 'date', 'after_or_equal:formData.start_at'],
            'formData.location' => ['nullable', 'string', 'max:255'],
            'formData.is_online' => ['boolean'],
            'formData.capacity' => ['required', 'integer', 'min:1'],
            'formData.is_published' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.name' => __('Name'),
            'formData.slug' => __('Slug'),
            'formData.type' => __('Type'),
            'formData.event_type_id' => __('Event Type'),
            'formData.category_id' => __('Category'),
            'formData.start_at' => __('Start'),
            'formData.end_at' => __('End'),
            'formData.location' => __('Location'),
            'formData.capacity' => __('Capacity'),
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
