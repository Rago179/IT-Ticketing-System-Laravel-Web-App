<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'user_name' => $this->comment->user->name,
            'post_title' => $this->comment->post->title,
            'post_id' => $this->comment->post_id,
        ];
    }
}