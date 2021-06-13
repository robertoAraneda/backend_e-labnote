<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
    public function index(UserRequest $request): JsonResponse
    {

        $users = User::orderBy('id')->paginate($request->getPaginate());

        return response()->json(UserResource::collection($users)->response()->getData(true));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(UserRequest $request):JsonResponse
    {
        $this->authorize('create', User::class);

        try {
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
    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user), 200);
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
     * @throws AuthorizationException
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $user->update($request->validated());

        return response()->json(new UserResource($user->fresh()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(null, 204);
    }
}
