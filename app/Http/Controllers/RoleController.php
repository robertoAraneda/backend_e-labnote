<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function index(RoleRequest $request): JsonResponse
    {
        $roles = Role::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
           RoleResource::collection($roles)
                ->response()
                ->getData(true),
            200);
    }


    /**
     * @throws AuthorizationException
     */
    public function store(RoleRequest $request): JsonResponse
    {

        $this->authorize('create', Role::class);

        $role = Role::create($request->validated());

        return response()->json(new RoleResource($role), 201);
    }

    /**
     * @OA\Get(
     *      path="/roles/{id}",
     *      tags={"Rol"},
     *      summary="Obtener un rol",
     *      description="Retorna un rol solicitado",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Id del rol",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=200, description="Successful operation"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=204, description="No Content"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=401, description="Unauthenticated"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=403, description="Forbidden")
     *     )
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json(new RoleResource($role), 200);

    }

    /**
     * @OA\Put(
     *      path="/roles/{id}",
     *      tags={"Rol"},
     *      summary="Actualizar un rol",
     *      description="Retorna un rol actualizado",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Id del rol",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/RoleRequest")
     *      ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=200, description="Successful operation"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=204, description="No Content"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=401, description="Unauthenticated"),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=403, description="Forbidden")
     *     )
     */
    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $role->update($request->validated());

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->json(null, 204);
    }
}
