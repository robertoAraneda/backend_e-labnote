<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

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
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(int $id):JsonResponse
    {
        $model = Module::find($id);

        if(!isset($model)){
            return response()->json(null, 404);
        }

        $this->authorize('delete', $model);

        try {
            $model->delete();

            return response()->json(null, 204);
        }catch (\Exception $exception){

            return response()->json(null, 500);
        }
    }

    public function findById(int $id): Module
    {
       return Module::find($id);

    }
}
