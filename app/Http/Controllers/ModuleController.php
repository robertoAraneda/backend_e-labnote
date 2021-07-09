<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Http\Resources\MenuResource;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ModuleRequest $request
     * @return JsonResponse
     */
    public function index(ModuleRequest $request): JsonResponse
    {
        $items = Module::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            ModuleResource::collection($items)
                ->response()
                ->getData(true),
            200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ModuleRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ModuleRequest $request):JsonResponse
    {
        $this->authorize('create', Module::class);

        $model = Module::create($request->validated());

        return response()->json(new ModuleResource($model->fresh()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Module $module
     * @return JsonResponse
     */
    public function show(Module $module): JsonResponse
    {
        return response()->json(new ModuleResource($module), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ModuleRequest $request
     * @param Module $module
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ModuleRequest $request, Module $module): JsonResponse
    {

        $this->authorize('update', $module);

        $module->update($request->validated());

        return response()->json(new ModuleResource($module), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Module $module
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Module $module):JsonResponse
    {
        $this->authorize('delete', $module);

        try {
            $module->delete();

            return response()->json(null, 204);
        }catch (\Exception $exception){

            return response()->json(null, 500);
        }
    }


    public function searchByParams(Request $request): JsonResponse
    {
        if($request->input('slug')){
            $module = $this->findBySlug($request->input('slug'));
            return response()->json(new ModuleResource($module), Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    public function menusByModule(Module $module): JsonResponse
    {
        $menus = $module->menus()->active()->orderBy('id')->get();

        return response()->json(MenuResource::collection($menus), 200);
    }

    private function findByName($name){
        return Module::active()->with('menus')->where('name', $name)->first();
    }

    private function findBySlug($slug){
        return Module::active()->with('menus')->where('slug', $slug)->first();
    }
}
