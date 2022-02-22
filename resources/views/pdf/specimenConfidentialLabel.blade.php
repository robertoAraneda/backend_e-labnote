@php($patient = (object) $serviceRequest['patient'])
@php($request = (object) $serviceRequest)
@php($observations = (object) $serviceRequest['observations']['collection'])
@php($specimens = (object) $serviceRequest['specimens']['collection'])
@php($practitioner = (object) $serviceRequest['performer'])


<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        @page {
            margin: 1px 1px 1px 15px;
            font-family: helvetica;
        }

        .page-break {
            page-break-after: always;
        }
        .mini{
            font-size: 8px;
        }
        .rotated_vertical {
            /*-webkit-transform:rotate(270deg);
            -moz-transform:rotate(270deg);
            -ms-transform:rotate(270deg);
            -o-transform:rotate(270deg);*/
            transform:rotate(90deg);
            transform-origin: 0%;
            font-family: "Nunito", sans-serif;
            font-weight: bold;
            width: 100px;
        }
    </style>
</head>
<body>
<div>
    @foreach($specimens as $specimen)
        <div style="border: 1px black solid; padding: 10px 20px; width: 190px; margin-bottom: 20px;">
            <div>
                <div class="mini" style="display: block;"><span>CONFIDENCIAL</span></div>
                <div class="mini" style=" font-size: 12px; display: block;"><span style="background-color: black; color: white; font-weight: bold; padding:5px 10px;">{{$patient->confidential_identifier[0]['value']}}</span> </div>
                <div class="rotated_vertical mini" style="font-size: 10px; position: absolute; left: 215px;"><span style="border-bottom: 1px black solid; margin-bottom: 3px;">{{$specimen['container']['shortname']}}</span></div>
                <div class="mini">{{ $specimen['specimen_code']['display'] }}</div>
                <div class="mini" style="font-size: 10px;" >{{ \Carbon\Carbon::parse($specimen['specimen']['created_at'])->format('d/m/Y H:i') }}</div>
                <div><img width="160" src="{{$specimen['barcode']}}"> </div>
                <div class="mini" style="font-size: 14px; font-weight: bold; font-family: 'Nunito', sans-serif; letter-spacing: 2px; margin-left: 20px;" >{{$specimen['specimen']['accession_identifier'] }}</div>
            </div>
            <div></div>

        </div>
    @endforeach
</div>
</body>
</html>
