<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoincRequest;
use App\Http\Resources\LoincResource;
use App\Http\Resources\collections\LoincResourceCollection;
use App\Models\Loinc;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LoincController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LoincRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LoincRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Loinc::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Loinc::select(
                'loinc_num',
                'long_common_name'
            )
                ->paginate($request->getPaginate());
        }else{
            $items = Loinc::select(
                'loinc_num',
                'long_common_name'
            )
                ->limit(500)
                ->get();

        }
        $collection = new LoincResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LoincRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LoincRequest $request): JsonResponse
    {
        $this->authorize('create', Loinc::class);

        $data = array_merge($request->validated(),
            [
/*                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),*/
            ]);
        try {

            $model = Loinc::create($data);

            return response()->json(new LoincResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Loinc $loinc
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Loinc $loinc): JsonResponse
    {
        $this->authorize('view', $loinc);

        return response()->json(new LoincResource($loinc), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LoincRequest $request
     * @param Loinc $loinc
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LoincRequest $request, Loinc $loinc): JsonResponse
    {
        $this->authorize('update', $loinc);

        $data = array_merge($request->validated(),
            [
    /*            'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),*/
            ]);

        try {
            $loinc->update($data);

            return response()->json(new LoincResource($loinc) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Loinc $loinc
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Loinc $loinc): JsonResponse
    {

        $this->authorize('delete', $loinc);

        try {

            $loinc->update([
    /*            'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()*/
            ]);

            $loinc->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
