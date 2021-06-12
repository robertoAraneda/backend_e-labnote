<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // d
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = Role::create([
                'name' => $request->name
            ]
        );

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
    public function show(Role $role)
    {
        if($role){
            return response()->json(new RoleResource($role), 200);
        }else{
            return response()->json(new RoleResource(NULL), 204);
        }

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
        //TODO request->validated()
        $role->update($request->validated());

        return response()->json(new RoleResource($role), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(null, 204);
    }
}
