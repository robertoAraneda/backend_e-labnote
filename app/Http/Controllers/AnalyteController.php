<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyteRequest;
use App\Http\Resources\AnalyteResource;
use App\Http\Resources\Collections\AnalyteResourceCollection;
use App\Models\Analyte;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class AnalyteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AnalyteRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(AnalyteRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Analyte::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Analyte::select(
                'id',
                'name',
                'is_patient_codable',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Analyte::select(
                'id',
                'is_patient_codable',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AnalyteResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AnalyteRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AnalyteRequest $request): JsonResponse
    {
        $this->authorize('create', Analyte::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Analyte::create($data);

            return response()->json(new AnalyteResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Analyte $analyte
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Analyte $analyte): JsonResponse
    {
        $this->authorize('view', $analyte);

        return response()->json(new AnalyteResource($analyte), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AnalyteRequest $request
     * @param Analyte $analyte
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AnalyteRequest $request, Analyte $analyte): JsonResponse
    {
        $this->authorize('update', $analyte);

        $data = array_merge($request->validated(),
            [
           /*     'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),*/
            ]);

        try {
            $analyte->update($data);

            return response()->json(new AnalyteResource($analyte) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AnalyteRequest $request
     * @param Analyte $analyte
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(AnalyteRequest $request, Analyte $analyte): JsonResponse
    {

        $this->authorize('delete', $analyte);

        try {

            $analyte->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $analyte->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change status for specified resource.
     *
     * @param Request $request
     * @param Analyte $analyte
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(Request $request, Analyte $analyte): JsonResponse
    {
        $this->authorize('update', $analyte);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $analyte->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new AnalyteResource($analyte), 200);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), 500);
        }
    }

}
