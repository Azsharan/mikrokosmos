<?php

namespace App\Livewire\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsBell extends Component
{
    public Collection $notifications;
    public int $unreadCount = 0;
    public bool $open = false;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function render(): View
    {
        return view('livewire.admin.notifications-bell');
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->loadNotifications();
        }
    }

    public function refreshNotifications(): void
    {
        $this->loadNotifications();
    }

    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();

        if ($notification instanceof DatabaseNotification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    protected function loadNotifications(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->notifications = collect();
            $this->unreadCount = 0;

            return;
        }

        $this->notifications = $user->notifications()->latest()->take(10)->get();
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function openNotification(string $notificationId)
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        /** @var \Illuminate\Notifications\DatabaseNotification|null $notification */
        $notification = $user->notifications()->whereKey($notificationId)->first();

        if (! $notification) {
            return;
        }

        if ($notification->read_at === null) {
            $notification->markAsRead();
            $this->loadNotifications();
        }

        $url = $notification->data['url'] ?? null;

        if ($url) {
            return $this->redirect($url, navigate: true);
        }
    }
}
