<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use App\Models\User;

class NewCommentNotification extends Notification
{
    use Queueable;

    public $comment;
    public $commenter;

    public function __construct(Comment $comment, User $commenter)
    {
        $this->comment = $comment;
        $this->commenter = $commenter;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in the database
    }

    public function toArray($notifiable)
    {
        return [
            'post_id' => $this->comment->post_id,
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->commenter->name,
            'comment_content' => \Illuminate\Support\Str::limit($this->comment->content, 50),
            'message' => $this->commenter->name . ' commented on your post.',
        ];
    }
}