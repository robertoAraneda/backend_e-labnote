<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Http\Resources\collections\MenuResourceCollection;
use App\Http\Resources\collections\ModuleResourceCollection;
use App\Http\Resources\MenuResource;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ModuleRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ModuleRequest $request): JsonResponse
    {

        $this->authorize('viewAny', Module::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Module::select(
                'id',
                'name',
                'url',
                'icon',
                'slug',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Module::select(
                'id',
                'name',
                'url',
                'icon',
                'slug',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ModuleResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);

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

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Module::create($data);

            return response()->json(new ModuleResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Module $module
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Module $module): JsonResponse
    {
        $this->authorize('view', $module);

        return response()->json(new ModuleResource($module), Response::HTTP_OK);
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

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $module->update($data);

            return response()->json(new ModuleResource($module) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ModuleRequest $request
     * @param Module $module
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ModuleRequest $request, Module $module):JsonResponse
    {

        $this->authorize('delete', $module);

        $module->update([
            'deleted_user_id' => auth()->id(),
            'deleted_user_ip' => $request->ip()
        ]);

        try {
            $module->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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

        $collection = new MenuResourceCollection($menus);

        return response()->json($collection->response()->getData(true), 200);
    }

    private function findByName($name){
        return Module::active()->with('menus')->where('name', $name)->first();
    }

    private function findBySlug($slug){
        return Module::active()->with('menus')->where('slug', $slug)->first();
    }

    /**
     * @param ModuleRequest $request
     * @param Module $module
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ModuleRequest $request, Module $module): JsonResponse
    {
        $this->authorize('update', $module);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $module->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ModuleResource($module), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
