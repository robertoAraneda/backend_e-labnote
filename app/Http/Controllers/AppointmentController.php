<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Http\Requests\AppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\collections\AppointmentResourceCollection;
use App\Jobs\SendMailAppointmentCreated;
use App\Mail\AppointmentCreated;
use App\Models\Appointment;
use App\Models\AppointmentStatus;
use App\Models\AppointmentType;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    public function index(AppointmentRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Appointment::class);

        $page = $request->input('page');


        if (isset($page)) {
            $items = Appointment::select(
                'id',
                'start',
                'end',
                'description',
                'appointment_status_id',
                'patient_id',
                'service_request_id',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else if ($request->input('date')) {
            $date = $request->input('date');
            $items = Appointment::select(
                'id',
                'start',
                'end',
                'description',
                'appointment_status_id',
                'patient_id',
                'service_request_id',
            )->whereBetween('start', [$date."-01", $date."-31"])
                ->orderBy('id')
                ->get();
        } else if($request->input('type')){
            $type = $request->input('type');

            switch ($type){
                case 'day':
                case 'week':
                case 'month':
                case '4day':
                case 'category':
                    $dateStart = $request->input('dateStart');
                    $dateEnd = $request->input('dateEnd');

                    $items = Appointment::leftJoin('slots', 'slots.id', '=', 'appointments.slot_id')
                        ->select(
                            'appointments.id',
                            'appointments.start',
                            'appointments.end',
                            'appointments.description',
                            'appointments.appointment_status_id',
                            'appointments.appointment_type_id',
                            'appointments.patient_id',
                            'appointments.slot_id',
                            'appointments.service_request_id',
                        )
                        ->whereBetween('slots.start', [$dateStart." 00:00:00", $dateEnd." 23:59:00"])
                        ->orderBy('appointments.id')
                        ->get();
                    break;

                default:
                $items = [];
            }

        } else {
            $items = Appointment::select(
                'id',
                'start',
                'end',
                'description',
                'appointment_status_id',
                'patient_id',
                'service_request_id',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AppointmentResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    public function store(AppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        $data = array_merge($request->validated(),
            [
                'appointment_status_id' => AppointmentStatus::where('code', AppointmentStatusEnum::PENDING)->first()->id,
                'appointment_type_id' => AppointmentType::where('code', AppointmentTypeEnum::ROUTINE)->first()->id,
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Appointment::create($data);

            //se actualiza estado de slot
            $slot= Slot::find($model->slot_id);

            //TODO agregar ENUM de slot_status_id
            $slot->update(['slot_status_id' => 2, 'updated_user_id' => auth()->id(), 'updated_user_ip' => $request->ip()]);

            $appointmentResource = new AppointmentResource($model);

            SendMailAppointmentCreated::dispatch($model)->delay(now()->addMinutes(1));

            return response()->json($appointmentResource, Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        return response()->json(new AppointmentResource($appointment), Response::HTTP_OK);
    }


    public function update(AppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('update', $appointment);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $appointment->update($data);

            return response()->json(new AppointmentResource($appointment), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('delete', $appointment);

        try {
            $appointment->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $appointment->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createWithRangeDates(AppointmentRequest $request)
    {

        $diff = Carbon::parse($request['dates']['start'])->diffInDays(Carbon::parse($request['dates']['end']));

        $arrayDates = [];
        $arrayDates[] = Carbon::parse($request['dates']['start'])->format('Y-m-d');

        for ($i = 1; $i <= $diff; $i++) {
            $arrayDates[] = Carbon::parse($request['dates']['start'])->addDays($i)->format('Y-m-d');
        }

        $appointmentCreated = [];
        foreach ($arrayDates as $date) {
            $startDateParams = $date . $request['rangeTimeAppointment']['start'];
            $endDateParams = $date . $request['rangeTimeAppointment']['end'];
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDateParams);
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateParams);

            $diffInMinutes = $startDate->diffInMinutes($endDate);
            $count = 0;
            $flag = 0;

            while ($count < $diffInMinutes) {
                $flag++;
                $count += $request['slotTime'];

                $appointment = Appointment::create([
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $startDate->copy()->addMinutes($request['slotTime'])->format('Y-m-d H:i:s'),
                    'slot_status_id' => 1,
                    'comment' => 'Comentario',
                    'overbooked' => false,
                    'created_user_id' => auth()->id(),
                    'created_user_ip' => $request->ip()
                ]);

                $appointmentCreated[] = $appointment;

                $startDate->addMinutes($request['slotTime']);
            }
        }
        return response()->json($appointmentCreated);
    }
}
