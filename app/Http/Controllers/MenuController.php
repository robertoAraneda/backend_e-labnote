<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\collections\MenuResourceCollection;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

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
    public function store(MenuRequest $request):JsonResponse
    {
        $this->authorize('create', Menu::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Menu::create($data);

            return response()->json(new MenuResource($model) , 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
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

        return response()->json(new MenuResource($menu), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuRequest $request
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(MenuRequest $request, Menu $menu): JsonResponse
    {

        $this->authorize('update', $menu);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $menu->update($data);

            return response()->json(new MenuResource($menu) , 200);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , 500);
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

            return response()->json(null, 204);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), 500);
        }
    }



}
