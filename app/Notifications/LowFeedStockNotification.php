<?php

namespace App\Notifications;

use App\Models\FeedItem;
use Illuminate\Notifications\Notification;

class LowFeedStockNotification extends Notification
{
    public function __construct(public FeedItem $feedItem)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "{$this->feedItem->name} is low on stock (".number_format($this->feedItem->currentStock(), 1).' kg left).',
            'url' => route('feed-items.index'),
        ];
    }
}
