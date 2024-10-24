<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusChanged extends Notification
{
    use Queueable;

    private $ticket;
    private $estado;
    private $personName;

    public function __construct($ticket, $estado, $personName)
    {
        $this->ticket = $ticket;
        $this->estado = $estado;
        $this->personName = $personName;
    }

    public function via()
    {
        return ['database'];
    }

    public function toArray()
    {
        return [
            'tic_id' => $this->ticket->id,
            'tic_title' => $this->ticket->tic_titulo,
            'new_status' => $this->estado,
            'person_name' => $this->personName,
            'created_at' => now(),
        ];
    }
}
