<?php

namespace App\Notifications;

use App\Models\Animal;
use Illuminate\Notifications\Notification;

class AnimalReadyToSellNotification extends Notification
{
    public function __construct(public Animal $animal)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "{$this->animal->tag_id} has reached its target weight and is ready to sell.",
            'url' => route('animals.show', $this->animal),
        ];
    }
}
