<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateRequest;
use App\Http\Resources\collections\CityResource;
use App\Http\Resources\collections\DistrictResource;
use App\Http\Resources\collections\StateResourceCollection;
use App\Http\Resources\StateResource;
use App\Models\State;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StateController extends Controller
{
    /**
 * Display a listing of the resource.
 *
 * @param StateRequest $request
 * @return JsonResponse
 * @throws AuthorizationException
 */
    public function index(StateRequest $request): JsonResponse
    {
        $this->authorize('viewAny', State::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = State::select(
                'code',
                'name',
                'active',
            )
                ->orderBy('code')
                ->paginate($request->getPaginate());
        }else{
            $items = State::select(
                'code',
                'name',
                'active',
            )
                ->orderBy('code')
                ->get();
        }
        $collection = new StateResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StateRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StateRequest $request): JsonResponse
    {
        $this->authorize('create', State::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = State::create($data);

            return response()->json(new StateResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param State $state
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(State $state): JsonResponse
    {
        $this->authorize('view', $state);

        return response()->json(new StateResource($state), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StateRequest $request
     * @param State $state
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(StateRequest $request, State $state): JsonResponse
    {
        $this->authorize('update', $state);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $state->update($data);

            return response()->json(new StateResource($state) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param StateRequest $request
     * @param State $state
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(StateRequest $request, State $state): JsonResponse
    {
        $this->authorize('delete', $state);

        try {

            $state->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $state->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param StateRequest $request
     * @param State $state
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(StateRequest $request, State $state): JsonResponse
    {
        $this->authorize('update', $state);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $state->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new StateResource($state), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param State $state
     * @return JsonResponse
     */
    public function cities(State $state): JsonResponse
    {
        $cities = $state->cities()->active()->orderBy('id')->get();

        $collection = CityResource::collection($cities);

        return response()->json($collection, 200);
    }

}
