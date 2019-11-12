<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AgendaConcejo extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        $location = storage_path("app/agendas/" . $this->data->nombre_archivo);

        return $this->view('mails.agenda_concejo')->subject('Agenda de Sesión '. $this->data->agenda->tipo_agenda->nombre . ' ' . $this->data->agenda->fecha)->attach($location, [
            'as' => $this->data->etiqueta_archivo,
            'mime' => 'application/pdf',
        ]);
    }
}
