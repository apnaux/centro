<?php

namespace App\Notifications;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestNotification extends Notification
{
    use Queueable;

    public string $request_id;
    public Friend $friend_request;
    public User $to_friend;


    /**
     * Create a new notification instance.
     */
    public function __construct(string $request_id, Friend $friend_request, User $to_friend)
    {   
        $this->request_id = $request_id;
        $this->friend_request = $friend_request;
        $this->to_friend = $to_friend;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request_id,
            'friend_request' => $this->friend_request,
            'to_friend'=> $this->to_friend
        ];
    }
}
