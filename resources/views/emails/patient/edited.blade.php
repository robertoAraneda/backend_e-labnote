@component('mail::message')

# Modificación de datos personales

# Hola {{  $patient[0]['given'] }} {{  $patient[0]['father_family'] }} {{  $patient[0]['mother_family'] }},

## Sus datos personales han sido modificados.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
