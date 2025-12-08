<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignedTicketNotification extends Notification
{
    use Queueable;

    public $post;
    public $assigner_name;

    public function __construct(Post $post, $assigner_name)
    {
        $this->post = $post;
        $this->assigner_name = $assigner_name;
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
            'user_name' => $this->assigner_name, 
            'type' => 'assigned', 
        ];
    }
}