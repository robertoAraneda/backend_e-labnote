<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\collections\PermissionResourceCollection;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;

class PermissionController extends Controller
{

    /**
     * @param PermissionRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(PermissionRequest $request): JsonResponse
    {

        $this->authorize('viewAny', Permission::class);

        $page = $request->input('page');

        if(isset($page)) {
            $items = Permission::select(
                'id',
                'name',
                'guard_name',
                'action',
                'model',
                'description',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Permission::select(
                'id',
                'name',
                'guard_name',
                'action',
                'model',
                'description',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new PermissionResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        $this->authorize('create', Permission::class);


        $permission = Permission::create($request->validated());

        return response()->json(new PermissionResource($permission), 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(PermissionRequest $request, Permission $permission): JsonResponse
    {

        $this->authorize('update', $permission);

        $permission->update($request->validated());

        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Permission $permission): JsonResponse
    {

        $this->authorize('delete', $permission);

        $permission->delete();

        return response()->json(null, 204);


    }
}
