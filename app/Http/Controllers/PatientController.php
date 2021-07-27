<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Resources\collections\PatientResourceCollection;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param PatientRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(PatientRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Patient::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = Patient::select(
                'id',
                'birthdate',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = Patient::select(
                'id',
                'birthdate',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new PatientResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PatientRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(PatientRequest $request)
    {

        $this->authorize('create', Patient::class);

        try {
            DB::beginTransaction();

            //se agrega al objeto paciente la información de auditoria
            $patient = array_merge($request->validated()['patient'],
                [
                    'created_user_id' => auth()->id(),
                    'created_user_ip' => $request->ip(),
                ]);

            $model = Patient::create($patient);

            //se obtiene la información del nombre
            $humanName = array_merge(
                $request->validated()['humanName'],
                [
                    'created_user_id' => auth()->id(),
                    'created_user_ip' => $request->ip()
                ]);

            $model->humanNames()->create($humanName);


            //se obtiene la información de los identificadores del paciente
            $identifierPatientCollection = collect($request->validated()['identifierPatient']);


            $identifierPatient = $identifierPatientCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);
            });


            $model->identifierPatient()->createMany($identifierPatient);

            //se obtiene la información de los puntos de contacto
            $contactPointCollection = collect($request->validated()['contactPointPatient']);
            $contactPoint = $contactPointCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);

            });
            $model->contactPointPatient()->createMany($contactPoint);

            //se obtiene la información de los puntos de contacto
            $addressCollection = collect($request->validated()['addressPatient']);
            $address = $addressCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);
            });
            $model->addressPatient()->createMany($address);

            //se obtiene la información de familiar de contacto
            $contactPatientCollection = collect($request->validated()['contactPatient']);
            $contactPatient = $contactPatientCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);

            });
            $model->contactPatient()->createMany($contactPatient);

            DB::commit();

            return response()->json(new PatientResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {

            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Patient $patient
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Patient $patient): JsonResponse
    {
        $this->authorize('view', $patient);

        return response()->json(new PatientResource($patient), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PatientRequest $request
     * @param Patient $patient
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(PatientRequest $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $validated = (object)$request->validated();

        if (isset($validated->humanName)) {

            $data = array_merge($validated->humanName,
                [
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip(),
                ]);

            $patient->humanNames()->updateOrCreate(
                [
                    'id' => $validated->humanName['id'],
                ], $data);
        }

        if (isset($validated->addressPatient)) {

            $data = array_merge($validated->addressPatient,
                [
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip(),
                ]);

            $patient->addressPatient()->updateOrCreate(
                [
                    'id' => $validated->addressPatient['id'],
                ], $data);
        }



        if (isset($validated->identifierPatient) && count($validated->identifierPatient) != 0) {
            foreach ($validated->identifierPatient as $identifierPatient) {
                $data = array_merge($identifierPatient,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);

                $patient->identifierPatient()->updateOrCreate(
                    [
                        'id' => $identifierPatient['id'],
                    ], $data);

            }
        }

        if (isset($validated->contactPointPatient) && count($validated->contactPointPatient) != 0) {
            foreach ($validated->contactPointPatient as $contactPointPatient) {
                $data = array_merge($contactPointPatient,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);
                $patient->contactPointPatient()->updateOrCreate(
                    [
                        'id' => $contactPointPatient['id'],
                    ], $data);
            }
        }


        if (isset($validated->contactPatient) && count($validated->contactPatient) != 0) {
            foreach ($validated->contactPatient as $contactPatient) {

                $data = array_merge($contactPatient,
                    [
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]);
                $patient->contactPatient()->updateOrCreate(
                    [
                        'id' => $contactPatient['id'],
                    ], $data);
            }
        }

        $data = array_merge($validated->patient,
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $patient->update($data);

            return response()->json(new PatientResource($patient), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PatientRequest $request
     * @param Patient $patient
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(PatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('delete', $patient);

        try {

            $patient->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $patient->humanNames()->delete();

            $patient->delete();


            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param PatientRequest $request
     * @param Patient $patient
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(PatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $patient->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new PatientResource($patient), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
