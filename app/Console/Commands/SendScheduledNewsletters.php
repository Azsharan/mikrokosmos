<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Services\NewsletterSender;
use Illuminate\Console\Command;

class SendScheduledNewsletters extends Command
{
    protected $signature = 'newsletters:send-scheduled';

    protected $description = 'Send newsletters whose scheduled time has arrived';

    public function handle(NewsletterSender $sender): int
    {
        $newsletters = Newsletter::query()->
            whereNotNull('scheduled_at')->
            where('status', 'scheduled')->
            where('scheduled_at', '<', now()->startOfMinute())->
            get();

        if ($newsletters->isEmpty()) {
            $this->info('No newsletters ready to send.');

            return self::SUCCESS;
        }

        foreach ($newsletters as $newsletter) {
            $result = $sender->send($newsletter);

            match ($result['status']) {
                'sent' => $this->info("Newsletter {$newsletter->id} sent."),
                'no_recipients' => $this->warn("Newsletter {$newsletter->id} skipped: no recipients."),
                'missing_mailer' => $this->warn("Newsletter {$newsletter->id} skipped: mailer not configured."),
                'error' => $this->error("Newsletter {$newsletter->id} failed: {$result['error']}"),
                default => $this->warn("Newsletter {$newsletter->id} result: {$result['status']}"),
            };

            if ($result['status'] !== 'sent' && $newsletter->status !== 'sent') {
                $newsletter->forceFill(['status' => 'draft'])->save();
            }
        }

        return self::SUCCESS;
    }
}
