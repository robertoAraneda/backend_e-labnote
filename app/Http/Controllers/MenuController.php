<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\collections\MenuResourceCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\Module;
use App\Models\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param MenuRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(MenuRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Menu::class);

        $page = $request->input('page');

        if(isset($page)) {
            $items = Menu::select(
                'id',
                'name',
                'permission_id',
                'module_id',
                'url',
                'icon',
                'order',
                'active'
            )
                ->with(['module', 'permission'])
                ->orderBy('order', 'ASC')
                ->paginate($request->getPaginate());
        }else{
            $items = Menu::select(
                'id',
                'name',
                'permission_id',
                'module_id',
                'url',
                'icon',
                'order',
                'active'
            )
                ->with(['module', 'permission'])
                ->orderBy('order', 'ASC')
                ->get();
        }
        $collection = new MenuResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(MenuRequest $request)
    {
        $this->authorize('create', Menu::class);



        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Menu::create($data);

            $params = $request->validated();

            $permission = Permission::find($params['permission_id']);
            $module = Module::find($params['module_id']);

            $currentPermissions = $module->permissions()->get()->pluck('id')->all();

            $arrayPermissions = Permission::where('model', $permission->model)->get()->pluck('id')->all();

            $module->permissions()->syncWithPivotValues(array_merge($arrayPermissions, $currentPermissions), ['user_id' => auth()->id()]);

            return response()->json(new MenuResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Menu $menu): JsonResponse
    {
        $this->authorize('view', $menu);

        return response()->json(new MenuResource($menu), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuRequest $request
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(MenuRequest $request, Menu $menu)
    {

        $this->authorize('update', $menu);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $menu->update($data);

            $params = $request->validated();

            $permission = Permission::find($params['permission_id']);
            $module = Module::find($params['module_id']);

            $currentPermissions = $module->permissions()->get()->pluck('id')->all();

            $arrayPermissions = Permission::where('model', $permission->model)->get()->pluck('id')->all();

            $module->permissions()->syncWithPivotValues(array_merge($arrayPermissions, $currentPermissions), ['user_id' => auth()->id()]);

            return response()->json(new MenuResource($menu) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(MenuRequest $request, Menu $menu): JsonResponse
    {
        $this->authorize('delete', $menu);

        $menu->update([
            'deleted_user_id' => auth()->id(),
            'deleted_user_ip' => $request->ip()
        ]);

        try {
            $menu->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param MenuRequest $request
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(MenuRequest $request, Menu $menu): JsonResponse
    {
        $this->authorize('update', $menu);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $menu->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new MenuResource($menu), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
