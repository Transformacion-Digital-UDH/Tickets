<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $baseUrl = config('app.url');
        $url = "{$baseUrl}/reset-password/{$this->token}?email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Su enlace de restablecimiento de contraseña')
            ->greeting('Hola!')
            ->line('Has recibido este mensaje porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.')
            ->action('Restablecer Contraseña', $url)
            ->line('Si no solicitaste un restablecimiento de contraseña, ignora este mensaje.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
