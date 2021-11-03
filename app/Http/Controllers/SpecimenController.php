<?php

namespace App\Http\Controllers;

use App\Enums\ServiceRequestStatusEnum;
use App\Enums\SpecimenStatusEnum;
use App\Http\Requests\SpecimenRequest;
use App\Http\Resources\Collections\SpecimenResourceCollection;
use App\Http\Resources\SpecimenResource;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestStatus;
use App\Models\Specimen;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SpecimenController extends Controller
{

    public function index(SpecimenRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Specimen::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = Specimen::select(
                'id',
                'accession_identifier',
                'collected_at',

            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = Specimen::select(
                'id',
                'accession_identifier',
                'collected_at',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SpecimenResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }


    public function store(SpecimenRequest $request): JsonResponse
    {
        $this->authorize('create', Specimen::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Specimen::create($data);

            return response()->json(new SpecimenResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(Specimen $specimen): JsonResponse
    {
        $this->authorize('view', $specimen);

        return response()->json(new SpecimenResource($specimen), Response::HTTP_OK);
    }


    public function update(SpecimenRequest $request, Specimen $specimen)
    {
        $this->authorize('update', $specimen);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $specimen->update($data);

            return response()->json(new SpecimenResource($specimen), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(SpecimenRequest $request, Specimen $specimen): JsonResponse
    {
        $this->authorize('delete', $specimen);

        try {

            $specimen->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $specimen->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeTracking(SpecimenRequest $request)
    {
        if ($request->accession_identifier && $request->service_request_id) {
            $specimen = $this->findByAccessionNumber($request->accession_identifier);


            if($request->type == 'collected_at' && isset($specimen) && !isset($specimen->collected_at)){

                $specimen->update([
                    'collected_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'specimen_status_id' => SpecimenStatus::where('code', SpecimenStatusEnum::AVAILABLE)->first()->id,
                    'collector_id' => auth()->id(),
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip(),
                ]);

                //se buscan si hay muestras pendientes
                $completeSpecimens = Specimen::where('service_request_id', $request->service_request_id)
                    ->where('specimen_status_id', SpecimenStatus::where('code', SpecimenStatusEnum::PENDING)->first()->id)
                    ->get();

                if(count($completeSpecimens) === 0){
                    //se modifica el estado de la solicitud a completo
                   $serviceRequest =  ServiceRequest::find( $request->service_request_id);
                   $serviceRequest->update([
                       'service_request_status_id' => ServiceRequestStatus::where('code', ServiceRequestStatusEnum::COMPLETED)->first()->id,
                       'updated_user_id' => auth()->id(),
                       'updated_user_ip' => $request->ip(),
                   ]);
                }

                return response()->json(
                    ['specimen' => $specimen,
                    'specimen_status' => $specimen->status
                ], Response::HTTP_OK);

            }else{
                return response()->json($specimen, Response::HTTP_CONFLICT);
            }
        }
        return response()->json(null, Response::HTTP_OK);
    }



    public function searchByParams(SpecimenRequest $request): JsonResponse
    {
        if ($request->accession_identifier) {
            $specimen = $this->findByAccessionNumber($request->accession_identifier);

            return response()->json(new SpecimenResource($specimen), Response::HTTP_OK);
        }

        return response()->json(null, Response::HTTP_OK);
    }

    private function findByAccessionNumber($accession_identifier)
    {
        return Specimen::where('accession_identifier', $accession_identifier)->first();
    }

}
