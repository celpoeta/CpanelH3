<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\SlackMessage as MessagesSlackMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SlackNotification extends Notification
{
    use Queueable;
    public $message;
    public $route;
    public $f_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message , $f_name)
    {
        $this->message = $message;
        $this->f_name = $f_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    // routeNotificationForSlack

    public function toSlack($notification)
    {

        return (new MessagesSlackMessage)
            ->content($this->message)
            // ->attachment($this->route , $this->f_name)
            ->to(config('services.slack.webhook_url'));

    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
