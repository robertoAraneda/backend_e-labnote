<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\Collections\UserResourceCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{


    /**
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {

        $this->authorize('viewAny', User::class);

        $users = User::select(
            'id',
            'names',
            'lastname',
            'mother_lastname',
            'rut',
            'email',
            'active',
            )
            ->orderBy('id')
            ->paginate(10);

        $collection = new UserResourceCollection($users);

        return response()->json($collection->response()->getData(true), 200);
    }
    /**
     * @throws AuthorizationException
     */
    public function store(UserRequest $request):JsonResponse
    {
        $this->authorize('create', User::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
                'url' => $request->url()
            ]);
        try {

            $user =  User::create($data);

            return response()->json(new UserResource($user) , 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json(new UserResource($user), 200);
    }

    /**
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
                'url' => $request->url()
            ]);

        $user->update($data);

        return response()->json(new UserResource($user->fresh()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(UserRequest $request, User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $user->update([
            'deleted_user_id' => auth()->id(),
            'deleted_user_ip' => $request->ip()
        ]);

        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function changeActiveAttribute(Request $request, User $user): JsonResponse
    {

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        $user->update(['active' => $status, 'updated_user_id' => auth()->id()]);

        return response()->json(new UserResource($user), 200);
    }
}
