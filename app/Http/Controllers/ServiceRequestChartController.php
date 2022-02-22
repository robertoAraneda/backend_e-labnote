<?php

namespace App\Http\Controllers;

use App\Enums\ServiceRequestStatusEnum;
use App\Enums\SpecimenStatusEnum;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestObservation;
use App\Models\ServiceRequestStatus;
use App\Models\Specimen;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceRequestChartController extends Controller
{
    public function serviceRequestDayLineChart(Request $request, $day)
    {
        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 00:00:00");
        $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $day . " 23:59:59");

        $serviceRequests = ServiceRequest::whereBetween('authored_on', [$day . " 00:00:00", $day . " 23:59:59"])
            ->get();

        $sorterByValidatedDate = $serviceRequests->sortBy(function ($date, $key) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date->authored_on, 'America/Santiago')->timestamp;
        });

        $data = [];

        $collection = $sorterByValidatedDate->values()->groupBy(function ($item, $key) {
            return substr($item['authored_on'], 0, -6);
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
            'quantity'=> $serviceRequests->count()
        ]);
    }

    public function serviceRequestMonthLineChart(Request $request, $day)
    {
        $date =  Carbon::createFromFormat('Y-m-d', $day)->format('Y-m');

        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . "-01 00:00:00");
        $dateEnd = $dateStart->clone()->addMonth()->subDay();

        $serviceRequests = ServiceRequest::whereBetween('authored_on', [$dateStart->format('Y-m-d H:i:s'), $dateEnd->format('Y-m-d H:i:s')])
            ->get();

        $sorterByValidatedDate = $serviceRequests->sortBy(function ($date, $key) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date->authored_on, 'America/Santiago')->timestamp;
        });

        $data = [];

        $collection = $sorterByValidatedDate->values()->groupBy(function ($item, $key) {
            return  Carbon::createFromFormat('Y-m-d H:i:s',$item['authored_on'] )->format('d');
        });

        $month = $dateStart->daysInMonth;

        for ($i = 0; $i < $month; $i++) {
            if (isset($collection[$dateStart->format('d')])) {
                $data[] = [
                    "x" => $dateStart->format('d'),
                    "y" => (int)count($collection[$dateStart->format('d')])
                ];
            } else {
                $data[] = [
                    "x" => $dateStart->format('d'),
                    "y" => 0
                ];
            }
            $dateStart->addDay();
        }

        return response()->json([
            'dataSet' => $data,
            'quantity'=> $serviceRequests->count()
        ]);
    }

    public function analytesSelectedByDay(Request $request, $date){
        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . " 00:00:00");
        $dateEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date . " 23:59:59");

        $analytes = ServiceRequestObservation::whereBetween('created_at', [$dateStart->format('Y-m-d H:i:s'), $dateEnd->format('Y-m-d H:i:s')])
            ->with('code.specimenCode')
            ->get();


        $collection = $analytes->groupBy(function ($item) {
            return $item->code->name;
        });

       $mapped =  $collection->map(function($item, $key){
            return  ['count' => $item->count(),
                'name' => $key];
        });

        $collection_ = collect($mapped);

        $sorted = $collection_->sortByDesc('count');

        return response()->json(['quantity' =>$analytes->count(), 'dataSet' => $sorted->values()->all()]) ;


       // return $collection;
    }

    public function analytesSelectedByMonth(Request $request, $day){

        $date =  Carbon::createFromFormat('Y-m-d', $day)->format('Y-m');

        $dateStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . "-01 00:00:00");
        $dateEnd = $dateStart->clone()->addMonth()->subDay();

        $analytes = ServiceRequestObservation::whereBetween('created_at', [$dateStart->format('Y-m-d H:i:s'), $dateEnd->format('Y-m-d H:i:s')])
            ->with('code.specimenCode')
            ->get();


        $collection = $analytes->groupBy(function ($item) {
            return $item->code->name;
        });

        $mapped =  $collection->map(function($item, $key){
            return  ['count' => $item->count(),
                'name' => $key];
        });

        $collection_ = collect($mapped);

        $sorted = $collection_->sortByDesc('count');

        return response()->json(['quantity' =>$analytes->count(), 'dataSet' => $sorted->values()->all()]) ;


        // return $collection;
    }
}
