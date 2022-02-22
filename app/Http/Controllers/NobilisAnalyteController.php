<?php

namespace App\Http\Controllers;

use App\Http\Requests\NobilisAnalyteRequest;
use App\Http\Resources\Collections\NobilisAnalyteResourceCollection;
use App\Http\Resources\NobilisAnalyteResource;
use App\Models\NobilisAnalyte;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NobilisAnalyteController extends Controller
{

    public function index(NobilisAnalyteRequest $request): JsonResponse
    {
        $this->authorize('viewAny', NobilisAnalyte::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = NobilisAnalyte::select(
                'id',
                'description',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = NobilisAnalyte::select(
                'id',
                'description',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new NobilisAnalyteResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    public function store(NobilisAnalyteRequest $request): JsonResponse
    {
        $this->authorize('create', NobilisAnalyte::class);

        try {

            $model = NobilisAnalyte::create($request->validated());

            return response()->json(new NobilisAnalyteResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(NobilisAnalyte $nobilisAnalyte): JsonResponse
    {
        $this->authorize('view', $nobilisAnalyte);

        return response()->json(new NobilisAnalyteResource($nobilisAnalyte), Response::HTTP_OK);
    }

    public function update(NobilisAnalyteRequest $request, NobilisAnalyte $nobilisAnalyte)
    {
        $this->authorize('update', $nobilisAnalyte);

        try {
            $nobilisAnalyte->update($request->validated());

            return response()->json(new NobilisAnalyteResource($nobilisAnalyte) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(NobilisAnalyteRequest $request, NobilisAnalyte $nobilisAnalyte): JsonResponse
    {
        $this->authorize('delete', $nobilisAnalyte);

        try {

            $nobilisAnalyte->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
