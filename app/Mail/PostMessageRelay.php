<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostMessageRelay extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Post $post,
        public User $sender,
        public string $messageContent
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Message regarding your post: '.$this->post->title,
            replyTo: [
                new Address($this->sender->email, $this->sender->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.post-message-relay',
        );
    }
}
