<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisponibilityRequest;
use App\Http\Resources\DisponibilityResource;
use App\Models\Disponibility;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisponibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DisponibilityRequest $request)
    {
        $items = Disponibility::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            DisponibilityResource::collection($items)
                ->response()
                ->getData(true),
            Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
