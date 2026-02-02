<?php

namespace App\Livewire\Admin;

use App\Models\TableReservation;
use Illuminate\Database\Eloquent\Builder;

class TableReservations extends Datatable
{
    protected ?string $recordName = 'Table Reservation';
    protected bool $showActionButtons = false;

    protected function title(): string
    {
        return __('Table Reservations');
    }

    protected function description(): ?string
    {
        return __('Gestiona las mesas de la zona de juego y confirma su asistencia.');
    }

    protected function query(): Builder
    {
        return TableReservation::query()
            ->with('shopUser')
            ->latest('reserved_for');
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Código'),
                'type' => 'text',
                'field' => 'code',
                'text_class' => 'font-mono text-sm',
            ],
            [
                'label' => __('Mesa'),
                'format' => fn (TableReservation $reservation) => __('Mesa :number', [
                    'number' => $reservation->table_number,
                ]),
            ],
            [
                'label' => __('Cliente'),
                'format' => function (TableReservation $reservation) {
                    return sprintf(
                        '<div><p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p><p class="text-xs text-neutral-500">%s</p></div>',
                        e($reservation->shopUser?->name ?? __('Cuenta eliminada')),
                        e($reservation->shopUser?->email ?? '')
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Jugadores'),
                'type' => 'integer',
                'field' => 'party_size',
                'align' => 'right',
            ],
            [
                'label' => __('Horario'),
                'format' => function (TableReservation $reservation) {
                    return sprintf(
                        '<div><p class="font-semibold text-neutral-900 dark:text-neutral-50">%s</p><p class="text-xs text-neutral-500">%s - %s</p></div>',
                        e($reservation->reserved_for->format('Y-m-d')),
                        e($reservation->reserved_for->format('H:i')),
                        e($reservation->reserved_until->format('H:i'))
                    );
                },
                'html' => true,
            ],
            [
                'label' => __('Estado'),
                'type' => 'badge',
                'field' => 'status',
                'options' => [
                    TableReservation::STATUS_PENDING => ['label' => __('Pendiente'), 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-100'],
                    TableReservation::STATUS_CONFIRMED => ['label' => __('Confirmada'), 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100'],
                    TableReservation::STATUS_CANCELLED => ['label' => __('Cancelada'), 'class' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-100'],
                ],
                'default' => __('Pendiente'),
            ],
            [
                'label' => __('Acciones'),
                'format' => function (TableReservation $reservation) {
                    if ($reservation->status === TableReservation::STATUS_CONFIRMED) {
                        return sprintf(
                            '<span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-100">%s</span>',
                            e(__('Confirmada'))
                        );
                    }

                    if ($reservation->status === TableReservation::STATUS_CANCELLED) {
                        return sprintf(
                            '<span class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-500/20 dark:text-rose-100">%s</span>',
                            e(__('Cancelada'))
                        );
                    }

                    $buttons = [];

                    $buttons[] = sprintf(
                        '<button type="button" wire:click="confirmReservation(%1$d)" wire:loading.attr="disabled" wire:target="confirmReservation" class="inline-flex items-center rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-500">%2$s</button>',
                        $reservation->getKey(),
                        e(__('Confirmar asistencia'))
                    );

                    $buttons[] = sprintf(
                        '<button type="button" wire:click="cancelReservation(%1$d)" wire:loading.attr="disabled" wire:target="cancelReservation" class="inline-flex items-center rounded-lg border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-50 dark:border-rose-500/40 dark:text-rose-200 dark:hover:bg-rose-500/10">%2$s</button>',
                        $reservation->getKey(),
                        e(__('Cancelar'))
                    );

                    return implode(' ', $buttons);
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
                'label' => __('Estado'),
                'options' => [
                    TableReservation::STATUS_PENDING => __('Pendiente'),
                    TableReservation::STATUS_CONFIRMED => __('Confirmada'),
                    TableReservation::STATUS_CANCELLED => __('Cancelada'),
                ],
                'field' => 'status',
                'placeholder' => __('Todos los estados'),
            ],
            'table_number' => [
                'type' => 'select',
                'label' => __('Mesa'),
                'options' => collect(range(1, TableReservation::TOTAL_TABLES))
                    ->mapWithKeys(fn ($number) => [$number => __('Mesa :number', ['number' => $number])])
                    ->all(),
                'field' => 'table_number',
                'placeholder' => __('Todas las mesas'),
            ],
        ];
    }

    public function confirmReservation(int $reservationId): void
    {
        $reservation = TableReservation::query()->findOrFail($reservationId);

        if ($reservation->status === TableReservation::STATUS_CANCELLED) {
            return;
        }

        $reservation->forceFill(['status' => TableReservation::STATUS_CONFIRMED])->save();
        $this->dispatch('$refresh');
    }

    public function cancelReservation(int $reservationId): void
    {
        $reservation = TableReservation::query()->findOrFail($reservationId);

        if ($reservation->status === TableReservation::STATUS_CANCELLED) {
            return;
        }

        $reservation->forceFill(['status' => TableReservation::STATUS_CANCELLED])->save();
        $this->dispatch('$refresh');
    }
}
