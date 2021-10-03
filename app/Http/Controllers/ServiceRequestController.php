<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestRequest;
use App\Http\Resources\collections\ServiceRequestResourceCollection;
use App\Http\Resources\ServiceRequestResource;
use App\Models\IdentifierPatient;
use App\Models\Patient;
use App\Models\ServiceRequest;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use function Symfony\Component\String\s;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequest::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestRequest $request)
    {
        $this->authorize('create', ServiceRequest::class);

        $paramsValidated = (object) $request->validated();

        try {
            DB::beginTransaction();

            $currentDate = Carbon::now()->format('ymd');


            $findLastCorrelativeNumber = ServiceRequest::where('date_requisition_fragment',$currentDate)->orderBy('correlative_number', 'desc')->first();

            if(!isset($findLastCorrelativeNumber)){
                $correlativeNumber = 1;
            }else{
                $correlativeNumber = $findLastCorrelativeNumber->correlative_number + 1;
            }

            $secuenceString = str_pad($correlativeNumber, 5, "0", STR_PAD_LEFT);

            $requisition = (string)$currentDate . (string)$secuenceString;


            $serviceRequest = ServiceRequest::create(
                array_merge($request->validated(),
                [
                    'requisition' => $requisition,
                    'date_requisition_fragment' => $currentDate,
                    'correlative_number' => $correlativeNumber,
                    'service_request_status_id' => 1,
                    'service_request_intent_id' => 4,
                    'service_request_category_id' => 1,
                    'requester_id' => auth()->id(),
                    'created_user_id' => auth()->id(),
                    'created_user_ip' => $request->ip(),
                ]));

            $specimensCollection = collect($paramsValidated->specimens);

            $specimens = $specimensCollection->map(function ($item) use ($request, $requisition) {

                return array_merge($item,
                    [
                        'accession_identifier' => $requisition,
                        'specimen_status_id' => SpecimenStatus::where('code', 'pendiente')->first()->id,
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->specimens()->createMany($specimens);

            $observationsCollection = collect($paramsValidated->observations);

            $observations = $observationsCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->observations()->createMany($observations);


            DB::commit();

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_CREATED);
        } catch (\Exception $ex) {

            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('view', $serviceRequest);

        return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('update', $serviceRequest);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequest->update($data);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('delete', $serviceRequest);

        try {

            $serviceRequest->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequest->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('update', $serviceRequest);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequest->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function observations(ServiceRequest $serviceRequest): JsonResponse
    {
        $observations= $serviceRequest->observations()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\collections\ServiceRequestResource::collection($observations);

        return response()->json($collection, 200);
    }


    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function specimens(ServiceRequest $serviceRequest): JsonResponse
    {
        $specimens = $serviceRequest->specimens()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\collections\ServiceRequestResource::collection($specimens);

        return response()->json($collection, 200);
    }

    public function searchByParams(ServiceRequestRequest $request): JsonResponse
    {

        if($request->identifier){
            $serviceRequests= $this->findByIdentifier($request->identifier);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }

        if($request->patient && $request->type){

            $serviceRequests= $this->findByPatient($request->patient,$request->type);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }


        if($request->patientId){

            $serviceRequests= $this->findByPatientId($request->patientId);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }


        return response()->json([], Response::HTTP_OK);
    }

    private function findByIdentifier($identifier){


        return ServiceRequest::where('requisition', $identifier)->get();
    }

    private function findByPatient($identifier, $type){

        $identifier = IdentifierPatient::where('value', $identifier)->where('identifier_type_id', $type)->first();

        return $identifier->patient->serviceRequests;
    }

    private function findByPatientId($id){

        return ServiceRequest::where('patient_id', $id)->get();

    }

}
