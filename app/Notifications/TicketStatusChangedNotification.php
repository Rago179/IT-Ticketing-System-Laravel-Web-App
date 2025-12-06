<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
    use Queueable;

    public $post;
    public $changer_name;
    public $new_status;

    public function __construct(Post $post, $changer_name, $new_status)
    {
        $this->post = $post;
        $this->changer_name = $changer_name;
        $this->new_status = $new_status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'user_name' => $this->changer_name,
            'new_status' => $this->new_status,
            'type' => 'status_changed',
        ];
    }
}