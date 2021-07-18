<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaboratoryRequest;
use App\Http\Resources\collections\LaboratoryResourceCollection;
use App\Http\Resources\LaboratoryResource;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\ModuleResource;
use App\Models\Laboratory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class LaboratoryController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param LaboratoryRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LaboratoryRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Laboratory::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Laboratory::select(
                'id',
                'name',
                'phone',
                'active'
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Laboratory::select(
                'id',
                'name',
                'phone',
                'active'
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new LaboratoryResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LaboratoryRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LaboratoryRequest $request): JsonResponse
    {
        $this->authorize('create', Laboratory::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Laboratory::create($data);

            return response()->json(new LaboratoryResource($model) , 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Laboratory $laboratory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Laboratory $laboratory): JsonResponse
    {

        $this->authorize('view', $laboratory);

        return response()->json(new LaboratoryResource($laboratory), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LaboratoryRequest $request
     * @param Laboratory $laboratory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LaboratoryRequest $request, Laboratory $laboratory): JsonResponse
    {

        $this->authorize('update', $laboratory);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $laboratory->update($data);

            return response()->json(new LaboratoryResource($laboratory) , 200);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LaboratoryRequest $request
     * @param Laboratory $laboratory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(LaboratoryRequest $request, Laboratory $laboratory): JsonResponse
    {

        $this->authorize('delete', $laboratory);

        $laboratory->update([
            'deleted_user_id' => auth()->id(),
            'deleted_user_ip' => $request->ip()
        ]);

        try {
            $laboratory->delete();

            return response()->json(null, 204);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), 500);
        }
    }


    public function syncModulesLaboratory(Request $request, Laboratory $laboratory): JsonResponse
    {
        $laboratory->modules()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $laboratory->modules()->orderBy('id')->get();

        return response()->json(ModuleResource::collection($collection), 200);
    }

    /**
     * Change status for specified resource.
     *
     * @param Request $request
     * @param Laboratory $laboratory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(Request $request, Laboratory $laboratory): JsonResponse
    {
        $this->authorize('update', $laboratory);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $laboratory->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new LaboratoryResource($laboratory), 200);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), 500);
        }
    }
}
