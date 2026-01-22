<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;

class Reservations extends Datatable
{
    protected ?string $recordName = 'Reservation';

    protected function title(): string
    {
        return __('Reservations');
    }

    protected function description(): ?string
    {
        return __('Track reservation requests and confirm them once processed');
    }

    protected function query(): Builder
    {
        return Reservation::query()
            ->with('product')
            ->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Product'),
                'format' => function (Reservation $reservation) {
                    return sprintf(
                        '<div>
                            <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                            <p class="text-xs text-neutral-500">%s</p>
                        </div>',
                        e($reservation->product?->name ?? __('Deleted product')),
                        e($reservation->product?->slug ?? '')
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Customer'),
                'format' => function (Reservation $reservation) {
                    return sprintf(
                        '<div>
                            <p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p>
                            <p class="text-xs text-neutral-500">%s %s</p>
                        </div>',
                        e($reservation->name),
                        e($reservation->email),
                        $reservation->phone ? '· '.e($reservation->phone) : ''
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Quantity'),
                'type' => 'integer',
                'field' => 'quantity',
                'align' => 'right',
            ],
            [
                'label' => __('Status'),
                'type' => 'badge',
                'field' => 'status',
                'options' => [
                    'pending' => [
                        'label' => __('Pending'),
                        'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100',
                    ],
                    'confirmed' => [
                        'label' => __('Collected'),
                        'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100',
                    ],
                    'cancelled' => [
                        'label' => __('Cancelled'),
                        'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100',
                    ],
                ],
                'default' => __('Pending'),
            ],
            [
                'label' => __('Requested at'),
                'type' => 'datetime',
                'field' => 'created_at',
                'format_string' => 'Y-m-d H:i',
            ],
            [
                'label' => __('Actions'),
                'format' => function (Reservation $reservation) {
                    if ($reservation->status === 'confirmed') {
                        return sprintf(
                            '<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100">%s</span>',
                            e(__('Collected'))
                        );
                    }

                    if ($reservation->status === 'cancelled') {
                        return sprintf(
                            '<span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/20 dark:text-rose-100">%s</span>',
                            e(__('Cancelled'))
                        );
                    }

                    return sprintf(
                        '<button type="button" wire:click="confirmReservation(%1$d)" wire:loading.attr="disabled" wire:target="confirmReservation" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-xs font-semibold text-white transition %3$s" %2$s>%4$s</button>',
                        $reservation->getKey(),
                        '',
                        'hover:bg-primary-500',
                        e(__('Mark as collected'))
                    );
                },
                'html' => true,
                'th_class' => 'px-6 py-3 text-right',
                'td_class' => 'px-6 py-4 text-right',
            ],
        ];
    }

    protected function availableFilters(): array
    {
        return [
            'status' => [
                'type' => 'select',
                'label' => __('Status'),
                'options' => [
                    'pending' => __('Pending'),
                    'confirmed' => __('Collected'),
                    'cancelled' => __('Cancelled'),
                ],
                'field' => 'status',
                'placeholder' => __('All statuses'),
            ],
        ];
    }

    public function confirmReservation(int $reservationId): void
    {
        $reservation = Reservation::query()->findOrFail($reservationId);

        if ($reservation->status === 'confirmed') {
            return;
        }

        if ($reservation->status === 'cancelled') {
            return;
        }

        $reservation->forceFill(['status' => 'confirmed'])->save();

        $this->dispatch('$refresh');
    }
}
