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
        $months = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        $daysOfWeeks = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        $date = Carbon::now();
        $month = $months[($date->format('n')) -1];
        $day = $daysOfWeeks[$date->dayOfWeek - 1];
        $formatDate = $day." ".$date->format('d')." de ". $month. ", ". $date->format('Y');


        return $this
            ->from('robaraneda@gmail.com', 'Roberto Araneda')
            ->subject('LABISUR - Aviso modificación de datos personales')
            ->markdown('emails.patient.edited')
            ->with([
                'date' => $formatDate,
                'patient' => $this->patient->humanNames,
            ]);
    }
}
