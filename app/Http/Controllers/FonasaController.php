<?php

namespace App\Http\Controllers;

use App\Http\Requests\FonasaRequest;
use App\Http\Resources\collections\FonasaResourceCollection;
use App\Http\Resources\FonasaResource;
use App\Models\Fonasa;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class FonasaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param FonasaRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(FonasaRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Fonasa::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = Fonasa::select(
                'id',
                'codigo_mai',
                'codigo_rem',
                'name',
                'active'
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = Fonasa::select(
                'id',
                'codigo_mai',
                'codigo_rem',
                'name',
                'active'
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new FonasaResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FonasaRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(FonasaRequest $request):JsonResponse
    {
        $this->authorize('create', Fonasa::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Fonasa::create($data);

            return response()->json(new FonasaResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Fonasa $fonasa
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Fonasa $fonasa): JsonResponse
    {
        $this->authorize('view', $fonasa);

        return response()->json(new FonasaResource($fonasa), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FonasaRequest $request
     * @param Fonasa $fonasa
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(FonasaRequest $request, Fonasa $fonasa): JsonResponse
    {
        $this->authorize('update', $fonasa);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $fonasa->update($data);

            return response()->json(new FonasaResource($fonasa), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param FonasaRequest $request
     * @param Fonasa $fonasa
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(FonasaRequest $request, Fonasa $fonasa): JsonResponse
    {
        $this->authorize('delete', $fonasa);

        try {

            $fonasa->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $fonasa->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param FonasaRequest $request
     * @param Fonasa $fonasa
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(FonasaRequest $request, Fonasa $fonasa): JsonResponse
    {
        $this->authorize('update', $fonasa);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $fonasa->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new FonasaResource($fonasa), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
