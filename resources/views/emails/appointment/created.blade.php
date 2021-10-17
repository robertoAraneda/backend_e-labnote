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

## Concidiones generales para la toma de muestra:
<div align="justify" style="border: 1px #3999BF solid; padding: 10px 20px; border-radius: 10px; font-size: 12px">
    Usted deber&aacute; presentarse en la Toma de Muestra del Laboratorio durante la mañana, COMPLETAMENTE EN AYUNA, es decir, sin haber ingerido alimentos y l&iacute;quidos durante las &uacute;ltimas 10 a 12 horas y portando su C&Eacute;DULA DE IDENTIDAD la que ser&aacute; solicitada por el personal de Toma de Muestra.
    <ul>
        <li>NO INGERIR BEBIDAS ALCOH&Oacute;LICAS, tres d&iacute;as antes de su examen.</li>
        <li>NO PUEDE FUMAR NI REALIZAR EJERCICIO F&Iacute;SICO, antes de sus ex&aacute;menes.</li>
        <li>Si toma alg&uacute;n MEDICAMENTO, informar al personal de Toma de Muestra.</li>
        <li>Si se realizo alg&uacute;n examen de radiolog&iacute;a con medio de contraste, NO se realice NING&Uacute;N EXAMEN DE LABORATORIO hasta desp&uacute;es de tres d&iacute;as.</li>
    </ul>
</div>


@component('mail::panel')
    ### Recuerda llegar 15 minutos antes.
@endcomponent


Saludos cordiales,
@endcomponent
