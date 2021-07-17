<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamplingConditionRequest;
use App\Http\Resources\collections\SamplingConditionResourceCollection;
use App\Http\Resources\SamplingConditionResource;
use App\Models\SamplingCondition;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class SamplingConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SamplingConditionRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SamplingConditionRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SamplingCondition::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = SamplingCondition::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = SamplingCondition::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SamplingConditionResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SamplingConditionRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(SamplingConditionRequest $request): JsonResponse
    {
        $this->authorize('create', SamplingCondition::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SamplingCondition::create($data);

            return response()->json(new SamplingConditionResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('view', $samplingCondition);

        return response()->json(new SamplingConditionResource($samplingCondition), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SamplingConditionRequest $request
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SamplingConditionRequest $request, SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('update', $samplingCondition);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $samplingCondition->update($data);

            return response()->json(new SamplingConditionResource($samplingCondition), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SamplingConditionRequest $request
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SamplingConditionRequest $request, SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('delete', $samplingCondition);

        try {

            $samplingCondition->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $samplingCondition->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SamplingConditionRequest $request
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(SamplingConditionRequest $request, SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('update', $samplingCondition);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $samplingCondition->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new SamplingConditionResource($samplingCondition), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
