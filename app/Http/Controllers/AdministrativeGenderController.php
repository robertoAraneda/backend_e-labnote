<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdministrativeGenderRequest;
use App\Http\Resources\collections\AdministrativeGenderResourceCollection;
use App\Http\Resources\AdministrativeGenderResource;
use App\Models\AdministrativeGender;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AdministrativeGenderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AdministrativeGenderRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(AdministrativeGenderRequest $request): JsonResponse
    {
        $this->authorize('viewAny', AdministrativeGender::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = AdministrativeGender::select(
                'id',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = AdministrativeGender::select(
                'id',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AdministrativeGenderResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdministrativeGenderRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AdministrativeGenderRequest $request): JsonResponse
    {
        $this->authorize('create', AdministrativeGender::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = AdministrativeGender::create($data);

            return response()->json(new AdministrativeGenderResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param AdministrativeGender $administrativeGender
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(AdministrativeGender $administrativeGender)
    {
        $this->authorize('view', $administrativeGender);

        return response()->json(new AdministrativeGenderResource($administrativeGender), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdministrativeGenderRequest $request
     * @param AdministrativeGender $administrativeGender
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AdministrativeGenderRequest $request, AdministrativeGender $administrativeGender): JsonResponse
    {
        $this->authorize('update', $administrativeGender);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $administrativeGender->update($data);

            return response()->json(new AdministrativeGenderResource($administrativeGender) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AdministrativeGenderRequest $request
     * @param AdministrativeGender $administrativeGender
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(AdministrativeGenderRequest $request, AdministrativeGender $administrativeGender): JsonResponse
    {
        $this->authorize('delete', $administrativeGender);

        try {

            $administrativeGender->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $administrativeGender->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param AdministrativeGenderRequest $request
     * @param AdministrativeGender $administrativeGender
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(AdministrativeGenderRequest $request, AdministrativeGender $administrativeGender): JsonResponse
    {
        $this->authorize('update', $administrativeGender);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $administrativeGender->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new AdministrativeGenderResource($administrativeGender), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
