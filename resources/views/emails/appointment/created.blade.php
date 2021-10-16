@component('mail::message')
# Reserva de hora agendada

# Hola {{  $patient[0]['given'] }} {{  $patient[0]['father_family'] }} {{  $patient[0]['mother_family'] }},

## Estos son los datos de la reserva

@component('mail::table')
|           |   |                                                           |
| :---------|:-:|:----------------------------------------------------------|
| Día       | : | {{ $date }}                                               |
| Hora      | : | {{ \Carbon\Carbon::parse($slot['start'])->format('H:i') }}|
| Ubicación | : | Recepción laboratorio                                     |
| Dirección | : | Dirección LABISUR                                         |
@endcomponent

@component('mail::panel')
    ### Recuerda llegar 15 minutos antes.
@endcomponent


Saludos cordiales,
@endcomponent
