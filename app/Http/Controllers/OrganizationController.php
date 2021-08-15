<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationRequest;
use App\Http\Resources\collections\OrganizationResourceCollection;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param OrganizationRequest $request
     * @return JsonResponse+
     * @throws AuthorizationException
     */
    public function index(OrganizationRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Organization::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Organization::select(
                'id',
                'name',
                'alias',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Organization::select(
                'id',
                'name',
                'alias',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new OrganizationResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrganizationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(OrganizationRequest $request): JsonResponse
    {
        $this->authorize('create', Organization::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Organization::create($data);

            return response()->json(new OrganizationResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Organization $organization
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        return response()->json(new OrganizationResource($organization), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(OrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $organization->update($data);

            return response()->json(new OrganizationResource($organization) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param OrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(OrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('delete', $organization);

        try {

            $organization->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $organization->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param OrganizationRequest $request
     * @param Organization $organization
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(OrganizationRequest $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $organization->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new OrganizationResource($organization), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
