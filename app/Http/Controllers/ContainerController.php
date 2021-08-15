<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContainerRequest;
use App\Http\Resources\collections\ContainerResourceCollection;
use App\Http\Resources\ContainerResource;
use App\Models\Container;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ContainerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ContainerRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ContainerRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Container::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Container::select(
                'id',
                'name',
                'shortname',
                'color',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Container::select(
                'id',
                'name',
                'shortname',
                'color',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ContainerResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContainerRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ContainerRequest $request): JsonResponse
    {
        $this->authorize('create', Container::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Container::create($data);

            return response()->json(new ContainerResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Container $container
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Container $container): JsonResponse
    {
        $this->authorize('view', $container);

        return response()->json(new ContainerResource($container), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContainerRequest $request
     * @param Container $container
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ContainerRequest $request, Container $container): JsonResponse
    {
        $this->authorize('update', $container);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $container->update($data);

            return response()->json(new ContainerResource($container) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ContainerRequest $request
     * @param Container $container
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ContainerRequest $request, Container $container): JsonResponse
    {
        $this->authorize('delete', $container);

        try {

            $container->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $container->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ContainerRequest $request
     * @param Container $container
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ContainerRequest $request, Container $container): JsonResponse
    {
        $this->authorize('update', $container);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $container->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ContainerResource($container), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
