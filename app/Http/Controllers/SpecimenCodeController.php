<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecimenCodeRequest;
use App\Http\Resources\Collections\SpecimenCodeResourceCollection;
use App\Http\Resources\SpecimenCodeResource;
use App\Models\Analyte;
use App\Models\ServiceRequestObservationCode;
use App\Models\SpecimenCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecimenCodeController extends Controller
{
    /**
     * @param SpecimenCodeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SpecimenCodeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SpecimenCode::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = SpecimenCode::select(
                'id',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = SpecimenCode::select(
                'id',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SpecimenCodeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param SpecimenCodeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(SpecimenCodeRequest $request): JsonResponse
    {
        $this->authorize('create', SpecimenCode::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SpecimenCode::create($data);

            return response()->json(new SpecimenCodeResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenCode $specimenCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SpecimenCode $specimenCode): JsonResponse
    {
        $this->authorize('view', $specimenCode);

        return response()->json(new SpecimenCodeResource($specimenCode), Response::HTTP_OK);
    }

    /**
     * @param SpecimenCodeRequest $request
     * @param SpecimenCode $specimenCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SpecimenCodeRequest $request, SpecimenCode $specimenCode): JsonResponse
    {
        $this->authorize('update', $specimenCode);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $specimenCode->update($data);

            $serviceRequestObservationCodes = ServiceRequestObservationCode::where('specimen_code_id', $specimenCode->id)->get();

            foreach ($serviceRequestObservationCodes as $observationCode) {
                $analyte = $observationCode->analyte;
                $observationCode->update([
                    'name' => $analyte->name. ", ".$specimenCode->display,
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip()
                ]);
            }

            return response()->json(new SpecimenCodeResource($specimenCode) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenCodeRequest $request
     * @param SpecimenCode $specimenCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SpecimenCodeRequest $request, SpecimenCode $specimenCode): JsonResponse
    {
        $this->authorize('delete', $specimenCode);

        try {

            $specimenCode->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $specimenCode->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenCodeRequest $request
     * @param SpecimenCode $specimenCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(SpecimenCodeRequest $request, SpecimenCode $specimenCode): JsonResponse
    {
        $this->authorize('update', $specimenCode);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $specimenCode->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new SpecimenCodeResource($specimenCode), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
