<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Datatable;
use App\Mail\NewsletterBroadcastMail;
use App\Models\Newsletter;
use App\Models\ShopUser;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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
                'type' => 'text',
                'label' => __('Programar para (opcional)'),
                'placeholder' => '2026-02-01 10:00:00',
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

        if ($newsletter->status === 'sent') {
            return;
        }

        $recipients = ShopUser::query()->where('newsletter_opt_in', true)->pluck('email');

        if ($recipients->isEmpty()) {
            $newsletter->markAsSent();
            $this->setStatusMessage(__('No hay clientes suscritos para enviar este boletín.'), 'warning');
            return;
        }

        if (! $this->mailerConfigured()) {
            Log::warning('Newsletter mail skipped due to missing configuration');
            $this->setStatusMessage(__('No se pudo enviar el boletín porque no hay un servicio de correo configurado.'), 'warning');
            return;
        }

        try {
            foreach ($recipients as $email) {
                Mail::to($email)->send(new NewsletterBroadcastMail($newsletter));
            }

            $newsletter->markAsSent();
            $this->setStatusMessage(__('El boletín se envió correctamente.'), 'success');
        } catch (Throwable $exception) {
            Log::error('Newsletter sending failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $exception->getMessage(),
            ]);
            $this->setStatusMessage(__('Ocurrió un error al enviar el boletín.'), 'error');
        }
    }

    protected function mailerConfigured(): bool
    {
        $defaultMailer = config('mail.default');

        if (! $defaultMailer) {
            return false;
        }

        $mailerConfig = config("mail.mailers.{$defaultMailer}");

        return is_array($mailerConfig) && ! empty($mailerConfig);
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
}
