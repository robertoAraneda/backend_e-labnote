<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlotRequest;
use App\Http\Resources\collections\SlotResourceCollection;
use App\Http\Resources\SlotResource;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SlotController extends Controller
{
    public function index(SlotRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Slot::class);

        $page = $request->input('page');


        if (isset($page)) {
            $items = Slot::select(
                'id',
                'start',
                'end',
                'comment',
                'slot_status_id',
                'overbooked',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else if ($request->input('date')) {
            $date = $request->input('date');
            $items = Slot::select(
                'id',
                'start',
                'end',
                'comment',
                'slot_status_id',
                'overbooked',
            )->whereBetween('start', [$date."-01", $date."-31"])
                ->orderBy('start')
                ->get();
        } else if ($request->input('simple-date')) {
            $date = $request->input('simple-date');
            $items = Slot::select(
                'id',
                'start',
                'end',
                'comment',
                'slot_status_id',
                'overbooked',
            )->where('start', 'like', $date."%")
                ->orderBy('start')
                ->get();
        } else {
            $items = Slot::select(
                'id',
                'start',
                'end',
                'comment',
                'slot_status_id',
                'overbooked',
            )
                ->orderBy('start')
                ->get();
        }
        $collection = new SlotResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    public function store(SlotRequest $request): JsonResponse
    {
        $this->authorize('create', Slot::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Slot::create($data);

            return response()->json(new SlotResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(Slot $slot): JsonResponse
    {
        $this->authorize('view', $slot);

        return response()->json(new SlotResource($slot), Response::HTTP_OK);
    }


    public function update(SlotRequest $request, Slot $slot)
    {
        $this->authorize('update', $slot);
        $endDate = Carbon::parse($request->end)->subMinute();

        $findSlots = Slot::whereBetween('start',  [$request->start, $endDate])
            ->orderBy('id')
            ->get();

        if($findSlots->count() > 0){
            $filteredBusySlots = $findSlots->filter(function ($slot){
                return $slot->slot_status_id == 2;
            });

            if($filteredBusySlots->count() > 0){
                return response()->json(null, Response::HTTP_CONFLICT);
            }

            foreach ($findSlots as $findedSlot){
                if($findedSlot->id !== $slot->id){
                    $findedSlot->update([
                        'deleted_user_id' => auth()->id(),
                        'deleted_user_ip' => $request->ip()
                    ]);
                    $findedSlot->delete();
                }
            }
        }

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $slot->update($data);

            return response()->json(new SlotResource($slot), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(SlotRequest $request, Slot $slot): JsonResponse
    {
        $this->authorize('delete', $slot);

        try {
            $slot->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $slot->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteInBatch(SlotRequest $request, $ids)
    {
        $slots = collect(json_decode($ids));

        foreach ($slots as  $slot){
            $findedSlot = Slot::find($slot);
            try {
                $findedSlot->update([
                    'deleted_user_id' => auth()->id(),
                    'deleted_user_ip' => $request->ip()
                ]);

                $findedSlot->delete();

            } catch (\Exception $ex) {
                return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public function updateInBatch(SlotRequest $request, $ids){

        $slots = collect(json_decode($ids));

        $modifiedSlots = [];

        foreach ($slots as  $slot){
            $findedSlot = Slot::find($slot);
            $data = array_merge((array) $findedSlot,
                [
                    'slot_status_id' => $request->status,
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip(),
                ]);

            try {
                $findedSlot->update($data);

                $modifiedSlots[] = $findedSlot;
            } catch (\Exception $ex) {
                return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        }

        $collection = new SlotResourceCollection($modifiedSlots);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);

    }

    public function createWithRangeDates(SlotRequest $request)
    {

        $diff = Carbon::parse($request['dates']['start'])->diffInDays(Carbon::parse($request['dates']['end']));

        $arrayDates = [];
        $arrayDates[] = Carbon::parse($request['dates']['start'])->format('Y-m-d');

        for ($i = 1; $i <= $diff; $i++) {
            $arrayDates[] = Carbon::parse($request['dates']['start'])->addDays($i)->format('Y-m-d');
        }

        $slotCreated = [];
        $slotNotCreated = [];
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

                $findSlots = Slot::whereBetween('start',  [
                    $startDate->format('Y-m-d H:i'),
                    $startDate->copy()->addMinutes($request['slotTime'])->format('Y-m-d H:i:s')
                ])
                    ->orderBy('id')
                    ->get();

                if($findSlots->count() > 0){

                    $filteredBusySlots = $findSlots->filter(function ($slot){
                        return $slot->slot_status_id == 2;
                    });

                    if($filteredBusySlots->count() > 0){
                        $slotNotCreated[] = [
                            'start' => $startDate->format('Y-m-d H:i:s'),
                            'end' => $startDate->copy()->addMinutes($request['slotTime'])->format('Y-m-d H:i:s'),
                            'slot_status_id' => 1,
                            'comment' => 'Comentario',
                            'overbooked' => false,
                        ];

                    }else{
                        foreach ($findSlots as $slot){
                            $slot->update([
                                'deleted_user_id' => auth()->id(),
                                'deleted_user_ip' => $request->ip()
                            ]);
                           $slot->delete();
                        }

                        $slot = Slot::create([
                            'start' => $startDate->format('Y-m-d H:i:s'),
                            'end' => $startDate->copy()->addMinutes($request['slotTime'])->format('Y-m-d H:i:s'),
                            'slot_status_id' => 1,
                            'comment' => 'Comentario',
                            'overbooked' => false,
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip()
                        ]);

                        $slotCreated[] = $slot;

                    }
                }else{
                    $slot = Slot::create([
                        'start' => $startDate->format('Y-m-d H:i:s'),
                        'end' => $startDate->copy()->addMinutes($request['slotTime'])->format('Y-m-d H:i:s'),
                        'slot_status_id' => 1,
                        'comment' => 'Comentario',
                        'overbooked' => false,
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);

                    $slotCreated[] = $slot;

                }

                $startDate->addMinutes($request['slotTime']);

            }

        }

        return [
            'slotCreated' => $slotCreated,
            'slotNotCreated' => $slotNotCreated
        ];

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDateParams);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $endDateParams);


        return response()->json($request);
    }
}
