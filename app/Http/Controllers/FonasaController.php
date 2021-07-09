<?php

namespace App\Http\Controllers;

use App\Http\Requests\FonasaRequest;
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
     */
    public function index(FonasaRequest $request): JsonResponse
    {
        $items = Fonasa::orderBy('id')->get();

        return response()->json(FonasaResource::collection($items), Response::HTTP_OK);
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

        $model = Fonasa::create($request->validated());

        return response()->json(new FonasaResource($model->fresh()), Response::HTTP_CREATED);
    }
    /**
     * Display the specified resource.
     *
     * @param Fonasa $fonasa
     * @return JsonResponse
     */
    public function show(Fonasa $fonasa): JsonResponse
    {
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

        $fonasa->update($request->validated());

        return response()->json(new FonasaResource($fonasa), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Fonasa $fonasa
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Fonasa $fonasa): JsonResponse
    {
        $this->authorize('delete', $fonasa);

        try {
            $fonasa->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
