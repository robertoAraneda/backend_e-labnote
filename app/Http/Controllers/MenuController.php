<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param MenuRequest $request
     * @return JsonResponse
     */
    public function index(MenuRequest $request): JsonResponse
    {
        $items = Menu::with('module')->orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            MenuResource::collection($items)
                ->response()
                ->getData(true),
            200);
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

        $model = Menu::create($request->validated());

        return response()->json(new MenuResource($model->fresh()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Menu $menu
     * @return JsonResponse
     */
    public function show(Menu $menu): JsonResponse
    {
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

        $menu->update($request->validated());

        return response()->json(new MenuResource($menu), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Menu $menu
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Menu $menu): JsonResponse
    {
        $this->authorize('delete', $menu);

        try {
            $menu->delete();

            return response()->json(null, 204);
        }catch (\Exception $exception){

            return response()->json(null, 500);
        }
    }

    public function findById(int $id): Menu
    {
        return Menu::find($id);

    }
}
