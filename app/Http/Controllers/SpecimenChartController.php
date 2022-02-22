<?php

namespace App\Http\Controllers;

use App\Enums\ServiceRequestStatusEnum;
use App\Enums\SpecimenStatusEnum;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestStatus;
use App\Models\Specimen;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SpecimenChartController extends Controller
{
    public function samplingLineChart(Request $request, $day)
    {

        //return $day . " 00:00:00";

        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 00:00:00");
        $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 23:59:59");

        $specimenStatus = SpecimenStatus::where('code', SpecimenStatusEnum::AVAILABLE)->first();
        $specimens = Specimen::where('specimen_status_id', $specimenStatus->id)
            ->whereBetween('collected_at', [$day . " 00:00:00", $day . " 23:59:59"])
            ->get();

        $sorterByValidatedDate = $specimens->sortBy(function ($date, $key) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date->collected_at, 'America/Santiago')->timestamp;
        });

        $data = [];

        $collection = $sorterByValidatedDate->values()->groupBy(function ($item, $key) {
            return substr($item['collected_at'], 0, -6);
        });
        $dateStart->addHours(6);
        for ($i = 6; $i < 22; $i++) {
            if (isset($collection[$dateStart->format('Y-m-d H')])) {
                $data[] = [
                    "x" => $dateStart->format('H'),
                    "y" => (int)count($collection[$dateStart->format('Y-m-d H')])
                ];
            } else {
                $data[] = [
                    "x" => $dateStart->format('H'),
                    "y" => 0
                ];
            }
            $dateStart->addHour();
        }

        return response()->json([
            'dataSet' => $data,
            'detail' =>[
                'quantity' => $specimens->count(),
                'status' => 'TOMA DE MUESTRA LABISUR',
                'title' => "Número de mestras tomadas el ". $dateStart->format('d-m-Y'),
                'label' => 'Estado']
        ]);
    }

    public function patientTotalDay(Request $request, $day){
        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 00:00:00");
        $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 23:59:59");

        $serviceRequestStatus = ServiceRequestStatus::where('code', ServiceRequestStatusEnum::COMPLETED)->first();
        $serviceRequest = ServiceRequest::where('service_request_status_id', $serviceRequestStatus->id)
            ->whereBetween('updated_at', [$day . " 00:00:00", $day . " 23:59:59"])
            ->get();

        return $serviceRequest->count();
    }

    public function schedulePatientTotalDay(Request $request, $day){
        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 00:00:00");
        $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 23:59:59");

        $serviceRequestStatusActive = ServiceRequestStatus::where('code', ServiceRequestStatusEnum::ACTIVE)->first();
        $serviceRequestStatusComplete = ServiceRequestStatus::where('code', ServiceRequestStatusEnum::COMPLETED)->first();

        $serviceRequest = ServiceRequest::whereIn('service_request_status_id', [$serviceRequestStatusActive->id, $serviceRequestStatusComplete->id])
            ->whereBetween('updated_at', [$day . " 00:00:00", $day . " 23:59:59"])
            ->get();

        return $serviceRequest->count();
    }
}
