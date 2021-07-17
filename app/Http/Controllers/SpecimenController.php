<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecimenRequest;
use App\Http\Resources\collections\SpecimenResourceCollection;
use App\Http\Resources\SpecimenResource;
use App\Models\Specimen;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecimenController extends Controller
{
    /**
     * @param SpecimenRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SpecimenRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Specimen::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Specimen::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Specimen::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SpecimenResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param SpecimenRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(SpecimenRequest $request): JsonResponse
    {
        $this->authorize('create', Specimen::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Specimen::create($data);

            return response()->json(new SpecimenResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Specimen $specimen
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Specimen $specimen): JsonResponse
    {
        $this->authorize('view', $specimen);

        return response()->json(new SpecimenResource($specimen), Response::HTTP_OK);
    }

    /**
     * @param SpecimenRequest $request
     * @param Specimen $specimen
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SpecimenRequest $request, Specimen $specimen): JsonResponse
    {
        $this->authorize('update', $specimen);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $specimen->update($data);

            return response()->json(new SpecimenResource($specimen) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenRequest $request
     * @param Specimen $specimen
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SpecimenRequest $request, Specimen $specimen): JsonResponse
    {
        $this->authorize('delete', $specimen);

        try {

            $specimen->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $specimen->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenRequest $request
     * @param Specimen $specimen
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(SpecimenRequest $request, Specimen $specimen): JsonResponse
    {
        $this->authorize('update', $specimen);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $specimen->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new SpecimenResource($specimen), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
