<?php

namespace App\Mail;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PatientUpdated extends Mailable
{
    use Queueable, SerializesModels;

    protected Patient $patient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('robaraneda@gmail.com', 'Roberto Araneda')
            ->subject('LABISUR - Aviso modificación de datos personales')
            ->markdown('emails.patient.edited')
            ->with([
                'patient' => $this->patient->humanNames,
            ]);
    }
}
