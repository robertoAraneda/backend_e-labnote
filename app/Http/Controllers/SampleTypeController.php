<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleTypeRequest;
use App\Http\Resources\collections\SampleTypeResourceCollection;
use App\Http\Resources\SampleTypeResource;
use App\Models\SampleType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class SampleTypeController extends Controller
{
    /**
     * @param SampleTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SampleTypeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SampleType::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = SampleType::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = SampleType::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SampleTypeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     *
     * @author ELABNOTE
     */
    public function store(SampleTypeRequest $request): JsonResponse
    {
        $this->authorize('create', SampleType::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SampleType::create($data);

            return response()->json(new SampleTypeResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SampleType $sampleType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SampleType $sampleType): JsonResponse
    {
        $this->authorize('view', $sampleType);

        return response()->json(new SampleTypeResource($sampleType), Response::HTTP_OK);
    }

    /**
     * @param SampleTypeRequest $request
     * @param SampleType $sampleType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SampleTypeRequest $request, SampleType $sampleType): JsonResponse
    {
        $this->authorize('update', $sampleType);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $sampleType->update($data);

            return response()->json(new SampleTypeResource($sampleType) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SampleTypeRequest $request
     * @param SampleType $sampleType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SampleTypeRequest $request, SampleType $sampleType): JsonResponse
    {
        $this->authorize('delete', $sampleType);

        try {

            $sampleType->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $sampleType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
