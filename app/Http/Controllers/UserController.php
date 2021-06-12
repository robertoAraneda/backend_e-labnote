<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *      path="/users",
     *      tags={"Users"},
     *      summary="Obtener un listado de usuarios",
     *      description="Retorna una lista de usuarios",
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
    public function index(Request $request)
    {

        $limit = $request->get('paginate', 5);

        $users = User::orderBy('id')->paginate($limit);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            if (!request()->isJson()) {
               return response()->json([], 400);
            }

            $user = new User([
                'rut' => $request->rut,
                'names' => $request->names,
                'lastname' => $request->lastname,
                'mother_lastname' => $request->mother_lastname,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            $user->save();

            return response()->json(new UserResource($user->fresh()) , 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
        }
    }


    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      summary="Obtener un usuario",
     *      description="Retorna un usuario solicitado",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Id del usuario",
     *          required=true,
     *          in="path",
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
     *          response=204,
     *          description="No Content",
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
    public function show($id)
    {

        try {
            $user =User::find($id);

            if(isset($user)){

                return response()->json(new UserResource(User::find($id)), 200);

            }else {

                return response()->json(null, 204);

            }
        }catch (\Exception $exception){

            return response()->json($exception, 500);
        }



    }

    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      tags={"Users"},
     *      summary="Actualizar un usuario",
     *      description="Retorna un usuario actualizado",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Id del usuario",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UserRequest")
     *      ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *         @OA\MediaType(mediaType="application/json"),
     *          response=204,
     *          description="No Content",
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
    public function update(UserRequest $request, User $user)
    {
        $user->update($request->all());

        return new UserResource($user->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $delete = $user->delete();

        return response()->json(null, 204);
    }
}
