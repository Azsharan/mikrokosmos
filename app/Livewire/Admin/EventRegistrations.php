<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\EventRegistration as EventRegistrationModel;
use App\Models\ShopUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class EventRegistrations extends Datatable
{
    protected ?string $recordName = 'Event Registration';

    public array $eventOptions = [];

    public array $shopUserOptions = [];

    protected array $statusOptions = [
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'pending' => 'Pending',
    ];

    public function mount(): void
    {
        $this->eventOptions = Event::query()
            ->orderByDesc('start_at')
            ->pluck('name', 'id')
            ->toArray();

        $this->shopUserOptions = ShopUser::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function title(): string
    {
        return __('Event Registrations');
    }

    protected function description(): ?string
    {
        return __('Monitor attendees registered for each event');
    }

    protected function query(): Builder
    {
        return EventRegistrationModel::query()
            ->with(['event', 'shopUser'])
            ->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Event'),
                'priority' => 1,
                'format' => fn (EventRegistrationModel $registration) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($registration->event?->name ?? __('Deleted event')),
                    optional($registration->event?->start_at)->format('Y-m-d H:i')
                ),
                'html' => true,
            ],
            [
                'label' => __('Attendee'),
                'priority' => 1,
                'format' => fn (EventRegistrationModel $registration) => sprintf(
                    '<div>
                        <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                        <p class="text-xs text-neutral-500">%s</p>
                    </div>',
                    e($registration->shopUser?->name ?? __('Unknown customer')),
                    e($registration->shopUser?->email ?? '')
                ),
                'html' => true,
            ],
            [
                'label' => __('Status'),
                'type' => 'badge',
                'field' => 'status',
                'priority' => 1,
                'options' => [
                    'confirmed' => [
                        'label' => __('Confirmed'),
                        'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
                    ],
                    'cancelled' => [
                        'label' => __('Cancelled'),
                        'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100',
                    ],
                    'pending' => [
                        'label' => __('Pending'),
                        'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100',
                    ],
                ],
                'default' => __('Pending'),
            ],
            [
                'label' => __('Registered at'),
                'type' => 'datetime',
                'field' => 'created_at',
                'format_string' => 'Y-m-d H:i',
                'priority' => 2,
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'event_id' => [
                'type' => 'select',
                'label' => __('Event'),
                'options' => $this->eventOptions,
                'placeholder' => __('Select an event'),
            ],
            'shop_user_id' => [
                'type' => 'select',
                'label' => __('Attendee'),
                'options' => $this->shopUserOptions,
                'placeholder' => __('Select a customer'),
            ],
            'status' => [
                'type' => 'select',
                'label' => __('Status'),
                'options' => $this->statusOptions,
                'placeholder' => __('Select status'),
                'default' => 'confirmed',
            ],
            'notes' => [
                'type' => 'textarea',
                'label' => __('Notes'),
                'default' => '',
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.event_id' => ['required', 'integer', Rule::exists('events', 'id')],
            'formData.shop_user_id' => [
                'required',
                'integer',
                Rule::exists('shop_users', 'id'),
                Rule::unique('event_registrations', 'shop_user_id')
                    ->ignore($recordId)
                    ->where(fn ($query) => $query->where('event_id', $this->formData['event_id'] ?? null)),
            ],
            'formData.status' => ['required', Rule::in(array_keys($this->statusOptions))],
            'formData.notes' => ['nullable', 'string'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.event_id' => __('Event'),
            'formData.shop_user_id' => __('Attendee'),
            'formData.status' => __('Status'),
            'formData.notes' => __('Notes'),
        ];
    }
}
