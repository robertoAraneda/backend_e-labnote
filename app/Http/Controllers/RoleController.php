<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\collections\RoleResourceCollection;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    /**
     * @param RoleRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(RoleRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Role::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Role::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new RoleResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param RoleRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(RoleRequest $request): JsonResponse
    {

        $this->authorize('create', Role::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Role::create($data);

            return response()->json(new RoleResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Role $role): JsonResponse
    {
        $this->authorize('view', $role);

        return response()->json(new RoleResource($role), Response::HTTP_OK);

    }

    /**
     * @param RoleRequest $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $role->update($data);

            return response()->json(new RoleResource($role) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(RoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        try {

            $role->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $role->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     *
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function syncRolesPermission(Request $request, Role $role): JsonResponse
    {
        $this->authorize('create', Role::class);

        $role->syncPermissions($request->all());

        return response()->json(PermissionResource::collection($role->permissions) , Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function permissionsByRole(Request $request, Role $role)
    {
        $this->authorize('view', $role);

        if($request->input('cross')){

            $module = Module::find($request->input('module_id'));

           return $role;

            $roles_permissions = $role->permissions()->orderBy('id')->get()->pluck('id');

            $permissions = $module->permissions->map(function ($permission) use ($roles_permissions){
                $permission->checkbox = in_array($permission->id, $roles_permissions->all());
                return $permission;
            });


        }else{
            $permissions = $role->permissions()->orderBy('id')->get();
        }

        return response()->json(PermissionResource::collection($permissions), 200);
    }

    /**
     * @param Request $request
     * @param Role $role
     * @return JsonResponse
     */
    public function changeActiveAttribute(Request $request, Role $role): JsonResponse
    {

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        $role->update(['active' => $status, 'updated_user_id' => auth()->id()]);

        return response()->json(new RoleResource($role), 200);
    }

    public function assignSuperUser(){

        $role = Role::create(['name' => 'super-admin']);
        $user = User::find(auth()->id());
        $user->assignRole($role);

        return $role;
    }
}
