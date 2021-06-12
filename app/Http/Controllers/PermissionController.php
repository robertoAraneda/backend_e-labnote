<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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
    public function index()
    {
        return Permission::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);


        $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name
            ]
        );

        return response()->json(new PermissionResource($permission), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {

        $permission->update($request->all());

        return response()->json(new PermissionResource($permission), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {

        try {
            $permission->delete();
            return response()->json(null, 204);

        }catch (\Exception $exception){
            return response()->json(null, 404);
        }

    }
}
