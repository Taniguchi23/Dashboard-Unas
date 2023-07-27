<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReporteEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    public $vista;
    public $asunto;
    public $remitente;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData, $vista, $asunto, $remitente)
    {
        $this->mailData = $mailData;
        $this->vista = $vista;
        $this->asunto =  $asunto;
        $this->remitente = $remitente;
    }
    public function build()
    {
        return $this->subject($this->asunto)
            ->view($this->vista);
    }

}
