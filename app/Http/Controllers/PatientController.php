<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Http\Resources\collections\PatientResourceCollection;
use App\Http\Resources\PatientResource;
use App\Mail\AppointmentCreated;
use App\Mail\PatientUpdated;
use App\Models\HumanName;
use App\Models\IdentifierPatient;
use App\Models\Patient;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
    public function store(PatientRequest $request): JsonResponse
    {

        $this->authorize('create', Patient::class);

        try {
            DB::beginTransaction();

            $dataPatient = $request->validated();

            //se agrega al objeto paciente la información de auditoria
            $model = Patient::create([
                'active' => $dataPatient['active'],
                'administrative_gender_id' => $dataPatient['administrative_gender_id'],
                'birthdate' => $dataPatient['birthdate'],
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);


            //se obtiene la información de los identificadores del paciente
            $nameCollection = collect($request->validated()['name']);


            $name = $nameCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $model->humanNames()->createMany($name);


            //se obtiene la información de los identificadores del paciente
            $identifierPatientCollection = collect($request->validated()['identifier']);


            $identifierPatient = $identifierPatientCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $model->identifierPatient()->createMany($identifierPatient);

            //se obtiene la información de los puntos de contacto
            $contactPointCollection = collect($request->validated()['telecom']);
            $contactPoint = $contactPointCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);

            });
            $model->contactPointPatient()->createMany($contactPoint);

            //se obtiene la información de los puntos de contacto
            $addressCollection = collect($request->validated()['address']);
            $address = $addressCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });
            $model->addressPatient()->createMany($address);

            //se obtiene la información de familiar de contacto
            $contactPatientCollection = collect($request->validated()['contact']);
            $contactPatient = $contactPatientCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);

            });
            $model->contactPatient()->createMany($contactPatient);

            DB::commit();

            return response()->json(new PatientResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {

            DB::rollBack();
            return response()->json($ex->getTrace(), Response::HTTP_INTERNAL_SERVER_ERROR);
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
    public function update(PatientRequest $request, Patient $patient): JsonResponse
    {
        $this->authorize('update', $patient);

        $validated = (object)$request->validated();

        try {
            DB::beginTransaction();
            if (isset($validated->name) && count($validated->name) != 0) {
                foreach ($validated->name as $name) {
                    $data = array_merge($name,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                            'updated_user_id' => auth()->id(),
                            'updated_user_ip' => $request->ip(),
                        ]);

                    $patient->humanNames()->updateOrCreate(
                        [
                            'id' => $name['id'],
                        ], $data);

                }
            }

            if (isset($validated->address) && count($validated->address) != 0) {
                foreach ($validated->address as $address) {
                    $data = array_merge($address,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                            'updated_user_id' => auth()->id(),
                            'updated_user_ip' => $request->ip(),
                        ]);

                    $patient->addressPatient()->updateOrCreate(
                        [
                            'id' => $address['id'],
                        ], $data);

                }
            }

            if (isset($validated->identifier) && count($validated->identifier) != 0) {
                foreach ($validated->identifier as $identifierPatient) {

                    $data = array_merge($identifierPatient,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                            'updated_user_id' => auth()->id(),
                            'updated_user_ip' => $request->ip(),
                        ]);

                    $patient->identifierPatient()->updateOrCreate(
                        [
                            'id' => $identifierPatient['id'],
                        ], $data);

                }
            }

            if (isset($validated->telecom) && count($validated->telecom) != 0) {
                foreach ($validated->telecom as $contactPointPatient) {
                    $data = array_merge($contactPointPatient,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                            'updated_user_id' => auth()->id(),
                            'updated_user_ip' => $request->ip(),
                        ]);
                    $patient->contactPointPatient()->updateOrCreate(
                        [
                            'id' => $contactPointPatient['id'] ?? -1,
                        ], $data);
                }
            }

            if (isset($validated->contact) && count($validated->contact) != 0) {
                foreach ($validated->contact as $contactPatient) {

                    $data = array_merge($contactPatient,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                            'updated_user_id' => auth()->id(),
                            'updated_user_ip' => $request->ip(),
                        ]);
                    $patient->contactPatient()->updateOrCreate(
                        [
                            'id' => $contactPatient['id'],
                        ], $data);
                }
            }

            $patient->update(
                [
                    'active' => $validated->active,
                    'administrative_gender_id' => $validated->administrative_gender_id,
                    'birthdate' => $validated->birthdate,
                    'updated_user_id' => auth()->id(),
                    'updated_user_ip' => $request->ip(),
                ]
            );

            Mail::to('c.alarconlazo@gmail.com')
                ->cc('robaraneda@gmail.com')
                ->send(new PatientUpdated($patient));

            DB::commit();
            return response()->json(new PatientResource($patient), Response::HTTP_OK);
        } catch (\Exception $ex) {

            DB::rollBack();
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
            $patient->addressPatient()->delete();
            $patient->identifierPatient()->delete();
            $patient->contactPatient()->delete();
            $patient->contactPointPatient()->delete();

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

    public function searchByParams(PatientRequest $request)
    {

        if($request->query('query') == 'identifier'){
            $identifier = IdentifierPatient::where('value', $request->query('value'))->first();

            if(isset($identifier)){
                return response()->json(new PatientResource($identifier->patient), Response::HTTP_OK);
            }else{
                return response()->json(null, Response::HTTP_OK);
            }
        }

        if($request->query('query') == 'names'){

            $given = Str::upper($request->query('given'));
            $father_family = Str::upper($request->query('father_family'));
            $mother_family = Str::upper($request->query('mother_family'));


            $names = HumanName::where('given', $given)
                ->orWhere('father_family', 'like',  "%$father_family%")
                ->orWhere('mother_family',$mother_family )
                ->get()
            ->map(function($name){
                return $name->patient;
            });

            if(isset($names)){
                return response()->json(PatientResource::collection($names), Response::HTTP_OK);
            }else{
                return response()->json(null, Response::HTTP_OK);
            }
        }
        return response()->json([], Response::HTTP_OK);
    }

}
