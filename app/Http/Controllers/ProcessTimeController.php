<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessTimeRequest;
use App\Http\Resources\ProcessTimeResource;
use App\Models\ProcessTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProcessTimeRequest $request
     * @return JsonResponse
     */
    public function index(ProcessTimeRequest $request): JsonResponse
    {
        $items = ProcessTime::orderBy('id')->get();

        return response()->json(ProcessTimeResource::collection($items), Response::HTTP_OK);
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
