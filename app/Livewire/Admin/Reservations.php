<?php

namespace App\Livewire\Admin;

use App\Mail\ReservationCancelledMail;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
                'label' => __('Reservation Code'),
                'type' => 'text',
                'field' => 'code',
                'text_class' => 'font-mono text-sm',
                'priority' => 1,
            ],
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
                'priority' => 1,
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
                'priority' => 1,
            ],
            [
                'label' => __('Quantity'),
                'type' => 'integer',
                'field' => 'quantity',
                'align' => 'right',
                'priority' => 3,
            ],
            [
                'label' => __('Status'),
                'type' => 'badge',
                'field' => 'status',
                'priority' => 1,
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
                'priority' => 3,
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

                    $buttons = [];

                    if ($reservation->status !== 'cancelled') {
                        $buttons[] = sprintf(
                            '<button type="button" wire:click="confirmReservation(%1$d)" wire:loading.attr="disabled" wire:target="confirmReservation" class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-500">%2$s</button>',
                            $reservation->getKey(),
                            e(__('Mark as collected'))
                        );

                        $buttons[] = sprintf(
                            '<button type="button" wire:click="cancelReservation(%1$d)" wire:loading.attr="disabled" wire:target="cancelReservation" class="inline-flex items-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-200 dark:hover:bg-rose-500/10">%2$s</button>',
                            $reservation->getKey(),
                            e(__('Cancel'))
                        );
                    } else {
                        $buttons[] = sprintf(
                            '<span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/20 dark:text-rose-100">%s</span>',
                            e(__('Cancelled'))
                        );
                    }

                    return implode(' ', $buttons);
                },
                'html' => true,
                'th_class' => 'px-6 py-3 text-right',
                'td_class' => 'px-6 py-4 text-right',
                'priority' => 1,
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
            'code' => [
                'type' => 'text',
                'label' => __('Reservation Code'),
                'placeholder' => __('Search by code'),
                'apply' => function (Builder $query, $value) {
                    $query->where('code', 'like', '%'.$value.'%');
                },
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

    public function cancelReservation(int $reservationId): void
    {
        $reservation = Reservation::query()->findOrFail($reservationId);

        if ($reservation->status === 'cancelled') {
            return;
        }

        $reservation->forceFill(['status' => 'cancelled'])->save();

        if ($this->shouldSendReservationNotification()) {
            try {
                Mail::to($reservation->email)->send(new ReservationCancelledMail($reservation));
            } catch (Throwable $exception) {
                Log::warning('Failed to send reservation cancellation email', [
                    'reservation_id' => $reservation->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $this->dispatch('$refresh');
    }

    protected function shouldSendReservationNotification(): bool
    {
        $defaultMailer = config('mail.default');

        if (! $defaultMailer) {
            return false;
        }

        $mailerConfig = config("mail.mailers.{$defaultMailer}");

        return is_array($mailerConfig) && ! empty($mailerConfig);
    }
}
