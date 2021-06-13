<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *      path="/permissions",
     *      tags={"Permisos"},
     *      summary="Obtener un listado de permisos",
     *      description="Retorna una lista de permnisos",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="page",
     *          description="Pagina",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="paginate",
     *          description="Numero de elementos a retornar",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index(PermissionRequest $request): JsonResponse
    {
        $permissions = Permission::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            PermissionResource::collection($permissions)
                ->response()
                ->getData(true),
            200);
    }

    /**
     * @throws AuthorizationException
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        $this->authorize('create', Permission::class);


        $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name
            ]
        );

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
