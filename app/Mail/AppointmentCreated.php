<?php

namespace App\Mail;

use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated extends Mailable
{
    use Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $months = array("enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre");
        $daysOfWeeks = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $date = Carbon::parse($this->appointment->slot->start);
        $month = $months[($date->format('n')) -1];
        $day = $daysOfWeeks[$date->dayOfWeek];
        $formatDate = $day." ".$date->format('d')." de ". $month. ", ". $date->format('Y');


        return $this
            ->from('soporte@elabnote.cl', 'e-LabNote')
            ->subject('LABISUR - Aviso reserva de hora agendada')
            ->markdown('emails.appointment.created')
            ->with([
                'date' => $formatDate,
                'slot'=> $this->appointment->slot,
                'patient'=> $this->appointment->patient->humanNames,
                'appointment' => $this->appointment
            ]);
    }
}
