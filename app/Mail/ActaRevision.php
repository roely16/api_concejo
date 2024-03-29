<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActaRevision extends Mailable
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

        $location = storage_path("app/actas/" . $this->data->unique_id_file);

        return $this->view('mails.acta_revision')->subject('Revisión de Acta '. $this->data->acta->no_acta . ' - ' . $this->data->acta->year)->attach($location, [
            'as' => $this->data->file_name,
            'mime' => 'application/pdf',
        ]);
    }
}
