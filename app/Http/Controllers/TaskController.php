<?php

namespace App\Http\Controllers;

use App\Events\PatientSamplingRoom;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\BusinessStatus;
use App\Models\ServiceRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    public function store(TaskRequest $request)
    {
        try {

            $this->authorize('create', Task::class);

            $data = (object)$request->validated();

            $businessStatusId = BusinessStatus::where('code', $data->business_status_id)->first()->id;

            $task = Task::where('based_on', $data->based_on)->first();

            if (isset($task) && $task->business_status_id === $businessStatusId) {
                return response()->json(null, Response::HTTP_CONFLICT);
            } else {
                $data = array_merge($request->validated(),
                    [
                        'business_status_id' => $businessStatusId,
                        'authored_on' => Carbon::now()->format('Y-m-d H:i:s'),
                        'owner_id' => auth()->id(),
                    ]);

                $model = Task::create($data);

                event(new PatientSamplingRoom('Patient in sampling room'));

                return response()->json(new TaskResource($model), Response::HTTP_CREATED);
            }
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
