<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Datatable;
use App\Models\Newsletter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Services\NewsletterSender;

class Newsletters extends Datatable
{
    protected ?string $recordName = 'Newsletter';
    protected bool $showActionButtons = false;
    public ?string $statusMessage = null;
    public string $statusType = 'success';
    protected ?int $statusMessageId = null;

    protected function title(): string
    {
        return __('Newsletters');
    }

    protected function description(): ?string
    {
        return __('Crea anuncios y envíalos a los clientes suscritos al boletín');
    }

    protected function query(): Builder
    {
        return Newsletter::query()->latest();
    }

    protected function columns(): array
    {
        return [
            [
                'label' => __('Título'),
                'field' => 'title',
                'type' => 'text',
            ],
            [
                'label' => __('Estado'),
                'type' => 'badge',
                'field' => 'status',
                'options' => [
                    'draft' => ['label' => __('Borrador'), 'class' => 'bg-zinc-100 text-zinc-700'],
                    'scheduled' => ['label' => __('Programado'), 'class' => 'bg-amber-100 text-amber-700'],
                    'sent' => ['label' => __('Enviado'), 'class' => 'bg-emerald-100 text-emerald-700'],
                ],
                'default' => __('Borrador'),
            ],
            [
                'label' => __('Enviada el'),
                'type' => 'datetime',
                'field' => 'sent_at',
                'default' => __('Pendiente'),
            ],
            [
                'label' => __('Programada para'),
                'type' => 'datetime',
                'field' => 'scheduled_at',
                'default' => __('No programada'),
            ],
            [
                'label' => __('Acciones'),
                'html' => true,
                'align' => 'right',
                'th_class' => 'px-6 py-3 text-right',
                'td_class' => 'px-6 py-4 text-right',
                'format' => function (Newsletter $newsletter, array $column) {
                    return view('livewire.admin.newsletters.actions', [
                        'newsletter' => $newsletter,
                    ])->render();
                },
            ],
        ];
    }

    protected function formFields(): array
    {
        return [
            'title' => [
                'type' => 'text',
                'label' => __('Título'),
                'placeholder' => __('Campaña de verano'),
            ],
            'content' => [
                'type' => 'textarea',
                'label' => __('Contenido'),
                'default' => '',
                'full_width' => true,
            ],
            'scheduled_at' => [
                'type' => 'datetime-local',
                'label' => __('Programar para (opcional)'),
                'placeholder' => now()->format('Y-m-d\TH:i'),
                'help' => __('Selecciona fecha y hora para enviar este boletín automáticamente.'),
            ],
        ];
    }

    protected function validationRules(?int $recordId = null): array
    {
        return [
            'formData.title' => ['required', 'string', 'max:255'],
            'formData.content' => ['required', 'string'],
            'formData.scheduled_at' => ['nullable', 'date'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'formData.title' => __('Título'),
            'formData.content' => __('Contenido'),
            'formData.scheduled_at' => __('Programada para'),
        ];
    }

    public function sendNow(int $newsletterId): void
    {
        $newsletter = Newsletter::query()->findOrFail($newsletterId);

        $result = app(NewsletterSender::class)->send($newsletter);

        $messages = [
            'sent' => [__('El boletín se envió correctamente.'), 'success'],
            'no_recipients' => [__('No hay clientes suscritos para enviar este boletín.'), 'warning'],
            'missing_mailer' => [__('No se pudo enviar el boletín porque no hay un servicio de correo configurado.'), 'warning'],
            'error' => [__('Ocurrió un error al enviar el boletín.'), 'error'],
        ];

        [$message, $type] = $messages[$result['status']] ?? [__('Ocurrió un error al enviar el boletín.'), 'error'];
        $this->setStatusMessage($message, $type);
    }

    protected function setStatusMessage(string $message, string $type = 'success'): void
    {
        $this->statusMessage = $message;
        $this->statusType = $type;
        $this->statusMessageId = $this->statusMessageId ? $this->statusMessageId + 1 : 1;

        $this->dispatch('flash-message', [
            'message' => $message,
            'type' => $type,
            'id' => $this->statusMessageId,
        ]);

        $this->dispatch('reset-flash-timer', $this->statusMessageId);
    }

    protected function createRecord(array $data): Model
    {
        return parent::createRecord($this->prepareFormData($data));
    }

    protected function updateRecord(Model $record, array $data): Model
    {
        return parent::updateRecord($record, $this->prepareFormData($data));
    }

    protected function formDataFromRecord(Model $record): array
    {
        $data = parent::formDataFromRecord($record);

        if (! empty($record->scheduled_at)) {
            $data['scheduled_at'] = $record->scheduled_at->format('Y-m-d\TH:i');
        }

        return $data;
    }

    protected function prepareFormData(array $data): array
    {
        $scheduledAt = isset($data['scheduled_at']) && $data['scheduled_at']
            ? Carbon::parse($data['scheduled_at'])
            : null;

        $data['scheduled_at'] = $scheduledAt;

        if (! isset($data['status']) || $data['status'] !== 'sent') {
            if ($scheduledAt && $scheduledAt->isFuture()) {
                $data['status'] = 'scheduled';
            } else {
                $data['status'] = 'draft';
            }
        }

        return $data;
    }
}
