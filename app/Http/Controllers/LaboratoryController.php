<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaboratoryRequest;
use App\Http\Resources\LaboratoryResource;
use App\Models\Module;
use Illuminate\Http\Request;
use App\Http\Resources\ModuleResource;
use App\Models\Laboratory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LaboratoryController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @param LaboratoryRequest $request
     * @return JsonResponse
     */
    public function index(LaboratoryRequest $request): JsonResponse
    {
        $items = Laboratory::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            LaboratoryResource::collection($items)
                ->response()
                ->getData(true),
            200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LaboratoryRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LaboratoryRequest $request)
    {

        $this->authorize('create', Laboratory::class);

        $model = Laboratory::create($request->validated());

        return response()->json(new LaboratoryResource($model->fresh()), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Laboratory $laboratory
     * @return JsonResponse
     */
    public function show(Laboratory $laboratory): JsonResponse
    {
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

        $laboratory->update($request->validated());

        return response()->json(new LaboratoryResource($laboratory), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Laboratory $laboratory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy($id): JsonResponse
    {
        if(!is_numeric($id)){
            return response()->json(null, 406) ;
        }
        $model = Laboratory::find($id);

        if(!isset($model)){
            abort(404);
            //return response()->json(null, 404);
        }

        $this->authorize('delete', $model);

        try {
            $model->delete();

            return response()->json(null, 204);
        }catch (\Exception $exception){

            return response()->json(null, 500);
        }

    }

    public function findById($id){
        if(!is_numeric($id)){
            return response()->json(null, 400);
        }
        $model = Laboratory::find($id);

        if(!isset($model)){
            abort(404);
            //return response()->json(null, 404);
        }
    }


    public function syncModulesLaboratory(Request $request, Laboratory $laboratory): JsonResponse
    {
        $laboratory->modules()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $laboratory->modules()->orderBy('id')->get();

        return response()->json(ModuleResource::collection($collection), 200);
    }
}
