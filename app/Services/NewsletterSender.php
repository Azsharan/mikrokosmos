<?php

namespace App\Services;

use App\Mail\NewsletterBroadcastMail;
use App\Models\Newsletter;
use App\Models\ShopUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class NewsletterSender
{
    public function send(Newsletter $newsletter): array
    {
        $recipients = ShopUser::query()->where('newsletter_opt_in', true)->pluck('email');

        if ($recipients->isEmpty()) {
            $newsletter->markAsSent();

            return ['status' => 'no_recipients'];
        }

        if (! $this->mailerConfigured()) {
            Log::warning('Newsletter mail skipped due to missing configuration');

            return ['status' => 'missing_mailer'];
        }

        try {
            foreach ($recipients as $email) {
                Mail::to($email)->send(new NewsletterBroadcastMail($newsletter));
            }

            $newsletter->markAsSent();

            return ['status' => 'sent'];
        } catch (Throwable $exception) {
            Log::error('Newsletter sending failed', [
                'newsletter_id' => $newsletter->id,
                'error' => $exception->getMessage(),
            ]);

            return [
                'status' => 'error',
                'error' => $exception->getMessage(),
            ];
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
}
