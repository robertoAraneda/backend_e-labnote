<?php

namespace App\Http\Controllers;

use App\Http\Requests\PractitionerRequest;
use App\Http\Resources\collections\PractitionerResourceCollection;
use App\Http\Resources\PractitionerResource;
use App\Models\Practitioner;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PractitionerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param PractitionerRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(PractitionerRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Practitioner::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Practitioner::select(
                'id',
                'given',
                'family',
                'rut',
                'email',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Practitioner::select(
                'id',
                'given',
                'family',
                'rut',
                'email',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new PractitionerResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PractitionerRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(PractitionerRequest $request): JsonResponse
    {
        $this->authorize('create', Practitioner::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Practitioner::create($data);

            return response()->json(new PractitionerResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Practitioner $practitioner
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Practitioner $practitioner): JsonResponse
    {
        $this->authorize('view', $practitioner);

        return response()->json(new PractitionerResource($practitioner), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PractitionerRequest $request
     * @param Practitioner $practitioner
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PractitionerRequest $request, Practitioner $practitioner): JsonResponse
    {
        $this->authorize('update', $practitioner);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $practitioner->update($data);

            return response()->json(new PractitionerResource($practitioner) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PractitionerRequest $request
     * @param Practitioner $practitioner
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(PractitionerRequest $request, Practitioner $practitioner): JsonResponse
    {
        $this->authorize('delete', $practitioner);

        try {

            $practitioner->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $practitioner->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param PractitionerRequest $request
     * @param Practitioner $practitioner
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(PractitionerRequest $request, Practitioner $practitioner): JsonResponse
    {
        $this->authorize('update', $practitioner);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $practitioner->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new PractitionerResource($practitioner), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
