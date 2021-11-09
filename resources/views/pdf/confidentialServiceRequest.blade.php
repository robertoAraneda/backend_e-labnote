@php($patient = (object) $serviceRequest['patient'])
@php($request = (object) $serviceRequest)
@php($observations = (object) $serviceRequest['observations']['collection'])
@php($practitioner = (object) $serviceRequest['performer'])

<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
    html {
        margin: 0;
    }

    body {
        font-family: helvetica;
        margin: 340px 20px 160px 20px;
    }

    hr {
        page-break-after: always;
        border: 0;
        margin: 0;
        padding: 0;
    }

    header {
        position: fixed;
        left: 20px;
        top: 5px;
        right: 20px;
    }

    footer {
        position: fixed;
        left: 20px;
        bottom: 80px;
        right: 20px;
        height: 40px;
    }

    footer .page:after {
        content: counter(page);
    }

    footer table {
        width: 100%;
    }

    footer p {
        text-align: right;
    }

    footer .izq {
        text-align: left;
    }

    footer .cen {
        text-align: center;
    }

    header .page_header {
        height: 60px;
        width: 680px;
        margin-left: 40px;
        margin-top: 30px;
        position: relative;
    }


    header .header_left {
        position: absolute;
        top: 10px;
        left: 0;
    }

    header .header_center {
        position: absolute;
        text-align: center;
        top: 20px;
        right: 240px;
    }

    header .header_right {
        position: absolute;
        top: 20px;
        right: 25px;
    }


    header .titulo {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        padding-top: 10px;

    }

    header .table {
        width: 600px !important;
        text-align: left;
        /*padding-top: 20px;*/
        font-family: helvetica;


    }

    .table_4 th {
        font-size: 12px !important;
        text-align: left;
    }

    .table_4 td {
        font-size: 12px !important;
        text-align: left;
    }

    .table_5 th {
        font-size: 12px !important;
        text-align: left;
    }

    .table_5 td {
        font-size: 12px !important;
        text-align: left;
    }

    footer .table_4 th {
        font-size: 12px !important;
        text-align: left;
    }

    footer .table_4 td {
        font-size: 12px !important;
        text-align: left;
    }

    header .table_3 th {
        font-size: 12px;
        background-color: black;
        color: white;
        text-align: left;
    }

    header .table_3 tr td {
        width: 230px;
    }

    header .table_2 th {
        font-size: 12px;
        background-color: black;
        color: white;
        text-align: left;
    }

    header .table_2 tr td {
        width: 352.5px;
    }

    header .table_1 th {
        font-size: 12px;
        background-color: black;
        color: white;
        text-align: left;
    }

    header .table_1 tr td {
        width: 720px;
    }

    header .cabecera_left {
        font-family: helvetica;
        font-size: 14px;
        line-height: 14px;
        margin-top: 20px;
        width: 200px;
        height: 20px;
        position: absolute;

    }

    header .cabecera_right {
        font-family: helvetica;
        font-size: 14px;
        margin-left: 300px;
        line-height: 14px;
        margin-top: 20px;
        width: 340px;
        height: 20px;
        position: absolute;

    }

    body #planMejora tr th {
        font-size: 16px !important;
    }
</style>

<style>
    .page_break {
        page-break-before: always;
    }
</style>

<body>
<header>
    <div class="header_left">
        <div style="margin-top: 10px;">
            <img width="160" src="{{ public_path('assets/img/logo_labisur.jpg') }}">
        </div>
    </div>
    <div class="header_center"  style="font-family: Helvetica,serif; font-weight: bold; font-size: 20px;">
        <span>LABORATORIO INMUNOLÓGICO</span><br>
        <span>DEL SUR</span>
    </div>

    <div class="header_right">
        <span style="font-size: 16px; margin-left: 25px; margin-bottom: 10px;">N° SOLICITUD</span><br>
        <span><img width="160" src="{{$barcode}}"></span>
    </div>
    <div style="text-align: center; margin-bottom: 10px;top: 150px; position: absolute;">
        <table style="width: 100%; border-bottom: black 1px solid;">
            <tr>
                <th></th>
                <th></th>
                <th style="font-size: 18px; font-weight: bold">SOLICITUD DE EXAMEN {{ $patient->confidential_identifier[0]['type'] }}. </th>
                <td style="font-size: 18px; font-weight: bold">CÓDIGO: {{ $patient->confidential_identifier[0]['value'] }}</td>
                <th></th>
                <td></td>
            </tr>
        </table>
    </div>
    <table style="position: absolute; top: 190px;" class="table_4">
        <tr>
            <th style="width: 120px; font-size: 16px !important;">&nbsp;</th>
            <th></th>
            <th style="width: 356px; font-size: 16px !important;">&nbsp;</th>
            <th style="width: 128px; font-size: 16px !important;">Fecha</th>
            <th>:</th>
            <th style="width: 120px !important; font-size: 16px !important;">{{\Carbon\Carbon::createFromFormat('d/m/Y H:i:d',$serviceRequest['occurrence'])->format('d/m/Y')}}</th>
        </tr>
    </table>
    <div style="padding: 10px;position: absolute; top: 165px;">
        <h4>Datos paciente</h4>

        <table class="table_4" border="0" cellpadding="1" cellspacing="0">
            <tr>
                <th style="width: 100px;">Paciente</th>
                <th>:</th>
                <td style="width: 370px">{{$patient->name['given']}} {{$patient->name['father_family']}} {{$patient->name['mother_family']}}</td>
                <th style="width: 130px;">Previsi&oacute;n</th>
                <th>:</th>
                <td>previsión</td>
            </tr>
            <tr>
                <th>{{ $patient->identifier[0]['type'] }}</th>
                <th>:</th>
                <td style="">{{ $patient->identifier[0]['value'] }}</td>
                <th>Fecha de nacimiento:</th>
                <th>:</th>
                <td>{{ $patient->birthdate }}</td>
            </tr>
            <tr>
                <th>Edad</th>
                <th>:</th>
                <td>edad</td>
                <th>Tel&eacute;fono</th>
                <th>:</th>
                <td>teléfono</td>
            </tr>
            <tr>
                <th>Sexo</th>
                <th>:</th>
                <td>{{ $patient->administrative_gender }}</td>
                <th></th>
                <th></th>
                <td></td>
            </tr>
            <tr>
                <th>Direcci&oacute;n</th>
                <th>:</th>
                <td>dirección</td>
                <th>Servicio</th>
                <th>:</th>
                <td>servicio</td>
            </tr>
            <tr>
                <th>Di&aacute;gnostico</th>
                <th>:</th>
                <td>diagnóstico</td>
                <th>Observacion</th>
                <th>:</th>
                <td>{{$request->note }}</td>
            </tr>
        </table>
    </div>
    <br>
</header>
<footer>
    <table class="table_footer" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding-top: 10px;text-align: center; font-size: 9px; padding-bottom: 10px;">Direcci&oacute;n
                LABISUR
            </td>
            <td style="padding-top: 10px;text-align: center; font-size: 9px;">Tel&eacute;fono: LABISUR</td>
            <td style="padding-top: 10px;text-align: center; font-size: 9px;">Email: LABISUR</td>
        </tr>
        <tr>
            <td colspan="3"
                style="font-size: 10px;border-top:1px solid;border-bottom:1px solid; height: 20px; text-align: center;">
                Este laboratorio se encuentra adscrito al Programa de Evaluacion Externa de Calidad (PEEC) del Instituto
                de Salud P&uacute;blica de Chile. <b style="text-align: right;"></b>
            </td>
        </tr>
    </table>
</footer>
<script type="text/php">
     if ( isset($pdf) ) {
       $font = $fontMetrics->getFont("Arial", "bold");
       $pdf->page_text(530, 815, "Página {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
     }



</script>
<div style="padding: 10px; position: absolute; top: -15px;">
    <h4>Exámenes solicitados</h4>
    <table style="width: 100%" class="table_4" border="0" cellpadding="1" cellspacing="1">
        <tr style="text-align: center; background-color: #3999BF; color: #ffffff;">
            <th style="width: 200px;border-top:1px solid;border-bottom:1px solid; height: 25px; padding-left: 10px;">C&Oacute;DIGO</th>
            <th style="border-top:1px solid;border-bottom:1px solid; height: 25px;">EXAMEN</th>
        </tr>
        @foreach($observations as $observation)
            <tr>
                <td style="height: 25px; padding-left: 10px;">{{$observation->loinc_num}}</td>
                <td style="height: 25px">{{$observation->name}}</td>
            </tr>
        @endforeach
        <tr>
            <td style="border-bottom:1px solid; height: 20px;"></td>
            <td style="border-bottom:1px solid; height: 20px;"></td>
        </tr>
    </table>
</div>

<div
    style="border: black 2px solid; width: 750px; text-align: justify; font-size: 12px;position: fixed; left:20px; bottom: 300px; right: 20px; height: 40px; font-family: helvetica,serif;">
    <br>
    <div style="font-weight: bold; text-align: center; padding-bottom: 20px;">CONSENTIMIENTO INFORMADO</div>
    <br>
    <div style="padding-bottom: 60px;">Doy mi consentimiento por escrito para que se me extraiga una muestra de sangre
        para estudiar la presencia de anticuerpos al Virus de Inmunodeficiencia Adquirida (VIH) causante del Sindrome de
        Inmunodeficiencia Adquirida. He sido informado sobre el VIH y su acci&oacute;n en el organismo, la implicancia
        de ser portador de este virus, sus formas de infecci&oacute;n, medios de prevenci&oacute;n y tratamiento.
    </div>
    <table style="width: 100%" border="0" cellpadding="0" cellspacing="10">
        <tr>
            <td style="border-bottom: black 1px solid; text-align: center; font-size: 9px;">{{$patient->name['given']}} {{$patient->name['father_family']}} {{$patient->name['mother_family']}}</td>
            <td style="border-bottom: black 1px solid;"></td>
            <td style="text-align: center; font-size: 9px; border-bottom: black 1px solid;">{{$practitioner->given}} {{$practitioner->family}}</td>
        </tr>
        <tr>
            <td style="text-align: center; font-size: 10px;"><b>FIRMA PACIENTE</b></td>
            <td style="text-align: center; font-size: 10px;"><b>FIRMA FAMILIAR O ACOMPAÑANTE</b></td>
            <td style="text-align: center; font-size: 10px;"><b>FIRMA PROFESIONAL</b></td>
        </tr>
    </table>

</div>


</body>
</html>
