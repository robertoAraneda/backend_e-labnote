<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamplingIndicationRequest;
use App\Http\Resources\collections\SamplingIndicationResourceCollection;
use App\Http\Resources\SamplingIndicationResource;
use App\Models\SamplingIndication;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SamplingIndicationController extends Controller
{


    /**
     * @param SamplingIndicationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SamplingIndicationRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SamplingIndication::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = SamplingIndication::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = SamplingIndication::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SamplingIndicationResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param SamplingIndicationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(SamplingIndicationRequest $request): JsonResponse
    {
        $this->authorize('create', SamplingIndication::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SamplingIndication::create($data);

            return response()->json(new SamplingIndicationResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SamplingIndication $samplingIndication
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SamplingIndication $samplingIndication): JsonResponse
    {
        $this->authorize('view', $samplingIndication);

        return response()->json(new SamplingIndicationResource($samplingIndication), Response::HTTP_OK);
    }

    /**
     * @param SamplingIndicationRequest $request
     * @param SamplingIndication $samplingIndication
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SamplingIndicationRequest $request, SamplingIndication $samplingIndication): JsonResponse
    {
        $this->authorize('update', $samplingIndication);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $samplingIndication->update($data);

            return response()->json(new SamplingIndicationResource($samplingIndication) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SamplingIndicationRequest $request
     * @param SamplingIndication $samplingIndication
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SamplingIndicationRequest $request, SamplingIndication $samplingIndication): JsonResponse
    {
        $this->authorize('delete', $samplingIndication);

        try {

            $samplingIndication->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $samplingIndication->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
