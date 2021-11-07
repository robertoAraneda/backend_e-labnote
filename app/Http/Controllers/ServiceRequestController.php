<?php

namespace App\Http\Controllers;

use App\Enums\ServiceRequestCategoryEnum;
use App\Enums\ServiceRequestIntentEnum;
use App\Enums\ServiceRequestStatusEnum;
use App\Enums\SpecimenStatusEnum;
use App\Http\Requests\ServiceRequestRequest;
use App\Http\Resources\Collections\ServiceRequestResourceCollection;
use App\Http\Resources\ServiceRequestResource;
use App\Integrations\OML21Nobilis;
use App\Models\Container;
use App\Models\IdentifierPatient;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestIntent;
use App\Models\ServiceRequestStatus;
use App\Models\SpecimenStatus;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use PDF;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequest::class);

        $page = $request->input('page');
        $date = $request->input('date');


        if (isset($page)) {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else if (isset($date)) {
            $items = ServiceRequest::where('occurrence', 'like', $date . "%")
                ->orderBy('id')
                ->get();
        } else {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->get();
        }

        if (isset($date)) return response()->json(ServiceRequestResource::collection($items), Response::HTTP_OK);

        $collection = new ServiceRequestResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestRequest $request)
    {
        $this->authorize('create', ServiceRequest::class);

        $paramsValidated = (object)$request->validated();

        $serviceRequests = [];

        try {
            DB::beginTransaction();

            $specimensCollection = collect($paramsValidated->confidential_specimens);

            if ($specimensCollection->count() !== 0) {
                $currentDate = Carbon::now()->format('ymd');

                $findLastCorrelativeNumber = ServiceRequest::where('date_requisition_fragment', $currentDate)->orderBy('correlative_number', 'desc')->first();

                if (!isset($findLastCorrelativeNumber)) {
                    $correlativeNumber = 1;
                } else {
                    $correlativeNumber = $findLastCorrelativeNumber->correlative_number + 1;
                }

                $secuenceString = str_pad($correlativeNumber, 5, "0", STR_PAD_LEFT);

                $requisition = (string)$currentDate . (string)$secuenceString;

                $serviceRequest = ServiceRequest::create(
                    array_merge($request->validated(),
                        [
                            'requisition' => $requisition,
                            'date_requisition_fragment' => $currentDate,
                            'correlative_number' => $correlativeNumber,
                            'service_request_status_id' => ServiceRequestStatus::where('code', ServiceRequestStatusEnum::ACTIVE)->first()->id,
                            'service_request_intent_id' => ServiceRequestIntent::where('code', ServiceRequestIntentEnum::ORDER)->first()->id,
                            'service_request_category_id' => ServiceRequestCategory::where('code', ServiceRequestCategoryEnum::LABORATORY)->first()->id,
                            'requester_id' => auth()->id(),
                            'is_confidential' => true,
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                        ]));
                $specimens = $specimensCollection->map(function ($item) use ($request, $requisition) {

                    $container = Container::find($item['container_id']);

                    return array_merge($item,
                        [
                            'accession_identifier' => $requisition . $container->suffix,
                            'specimen_status_id' => SpecimenStatus::where('code', SpecimenStatusEnum::PENDING)->first()->id,
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip()
                        ]);
                });
                $serviceRequest->specimens()->createMany($specimens);

                $observationsCollection = collect($paramsValidated->confidential_observations);

                $observations = $observationsCollection->map(function ($item) use ($request) {

                    return array_merge($item,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip()
                        ]);
                });

                $serviceRequest->observations()->createMany($observations);
                $serviceRequests['confidential'] = $serviceRequest;
            }

            $specimensNotConfidentialCollection = collect($paramsValidated->not_confidential_specimens);

            if ($specimensNotConfidentialCollection->count() !== 0) {
                $currentDate = Carbon::now()->format('ymd');


                $findLastCorrelativeNumber = ServiceRequest::where('date_requisition_fragment', $currentDate)->orderBy('correlative_number', 'desc')->first();

                if (!isset($findLastCorrelativeNumber)) {
                    $correlativeNumber = 1;
                } else {
                    $correlativeNumber = $findLastCorrelativeNumber->correlative_number + 1;
                }

                $secuenceString = str_pad($correlativeNumber, 5, "0", STR_PAD_LEFT);

                $requisition = (string)$currentDate . (string)$secuenceString;

                $serviceRequest = ServiceRequest::create(
                    array_merge($request->validated(),
                        [
                            'requisition' => $requisition,
                            'date_requisition_fragment' => $currentDate,
                            'correlative_number' => $correlativeNumber,
                            'service_request_status_id' => ServiceRequestStatus::where('code', ServiceRequestStatusEnum::ACTIVE)->first()->id,
                            'service_request_intent_id' => ServiceRequestIntent::where('code', ServiceRequestIntentEnum::ORDER)->first()->id,
                            'service_request_category_id' => ServiceRequestCategory::where('code', ServiceRequestCategoryEnum::LABORATORY)->first()->id,
                            'requester_id' => auth()->id(),
                            'is_confidential' => false,
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip(),
                        ]));
                $specimens = $specimensNotConfidentialCollection->map(function ($item) use ($request, $requisition) {

                    $container = Container::find($item['container_id']);

                    return array_merge($item,
                        [
                            'accession_identifier' => $requisition . $container->suffix,
                            'specimen_status_id' => SpecimenStatus::where('code', SpecimenStatusEnum::PENDING)->first()->id,
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip()
                        ]);
                });
                $serviceRequest->specimens()->createMany($specimens);

                $observationsCollection = collect($paramsValidated->not_confidential_observations);

                $observations = $observationsCollection->map(function ($item) use ($request) {

                    return array_merge($item,
                        [
                            'created_user_id' => auth()->id(),
                            'created_user_ip' => $request->ip()
                        ]);
                });

                $serviceRequest->observations()->createMany($observations);
                $serviceRequests['not_confidential'] = $serviceRequest;
            }

            DB::commit();

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_CREATED);
        } catch (\Exception $ex) {

            DB::rollBack();
            return response()->json([
                'trace' => $ex->getTrace(), 'message' => $ex->getMessage(), 'statusCode' => $ex->getCode()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('view', $serviceRequest);

        return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        $this->authorize('update', $serviceRequest);
        $paramsValidated = (object)$request->validated();

        try {
            $serviceRequest->update(
                array_merge($request->validated(),
                    [
                        'service_request_status_id' => ServiceRequestStatus::where('code', ServiceRequestStatusEnum::ACTIVE)->first()->id,
                        'service_request_intent_id' => ServiceRequestIntent::where('code', ServiceRequestIntentEnum::ORDER)->first()->id,
                        'service_request_category_id' => ServiceRequestCategory::where('code', ServiceRequestCategoryEnum::LABORATORY)->first()->id,
                        'updated_user_id' => auth()->id(),
                        'updated_user_ip' => $request->ip(),
                    ]));

            //observations
            $serviceRequest->observations()->delete();

            $observationsCollection = collect($paramsValidated->observations);

            $observations = $observationsCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->observations()->createMany($observations);

            //specimens
            $specimensCollection = collect($paramsValidated->specimens);

            $serviceRequest->specimens()->delete();

            $requisition = $paramsValidated->requisition;

            $specimens = $specimensCollection->map(function ($item) use ($request, $requisition) {

                $container = Container::find($item['container_id']);

                return array_merge($item,
                    [
                        'accession_identifier' => $requisition . $container->suffix,
                        'specimen_status_id' => SpecimenStatus::where('code', SpecimenStatusEnum::PENDING)->first()->id,
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->specimens()->createMany($specimens);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('delete', $serviceRequest);

        try {

            $serviceRequest->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequest->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('update', $serviceRequest);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequest->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function observations(ServiceRequest $serviceRequest): JsonResponse
    {
        $observations = $serviceRequest->observations()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\Collections\ServiceRequestResource::collection($observations);

        return response()->json($collection, 200);
    }


    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function specimens(ServiceRequest $serviceRequest): JsonResponse
    {
        $specimens = $serviceRequest->specimens()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\Collections\ServiceRequestResource::collection($specimens);

        return response()->json($collection, 200);
    }

    public function searchByParams(ServiceRequestRequest $request): JsonResponse
    {

        if ($request->identifier) {
            $serviceRequests = $this->findByIdentifier($request->identifier);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }

        if ($request->patient && $request->type) {

            $serviceRequests = $this->findByPatient($request->patient, $request->type);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }


        if ($request->patientId) {

            $serviceRequests = $this->findByPatientId($request->patientId);

            return response()->json(ServiceRequestResource::collection($serviceRequests), Response::HTTP_OK);
        }


        return response()->json([], Response::HTTP_OK);
    }

    private function findByIdentifier($identifier)
    {


        return ServiceRequest::where('requisition', $identifier)->get();
    }

    private function findByPatient($identifier, $type)
    {

        $identifier = IdentifierPatient::where('value', $identifier)->where('identifier_type_id', $type)->first();

        return $identifier->patient->serviceRequests;
    }

    private function findByPatientId($id)
    {

        return ServiceRequest::where('patient_id', $id)->get();

    }

    public function viewPdf(ServiceRequest $serviceRequest)
    {

        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $var = new BarcodeGenerator();
        $var->setText($serviceRequest->requisition);
        $var->setType(BarcodeGenerator::Code128);
        $var->setScale(2);
        $var->setThickness(30);
        $var->setFontSize(12);
        $code = $var->generate();

        $payload = [
            'barcode' => "data:image/png;base64," . $code,
            'serviceRequest' => $this->toArray($serviceRequest),
        ];

        $pdf = PDF::loadView('pdf.serviceRequest', $payload);

        return $pdf->stream('prueba.pdf');

    }

    public function generateCodbar(ServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {

        $accession_identifier = $request->input('accession_identifier');

        if (isset($accession_identifier)) {

            $mappedServiceRequest = $this->toArray($serviceRequest);

            $container = collect($mappedServiceRequest['_links']['specimens']['collection'])->filter(function ($specimen) use ($accession_identifier) {
                return $specimen['specimen']['accession_identifier'] == $accession_identifier;
            });

            $mappedServiceRequest['_links']['specimens']['collection'] = $container;

            $payload = [
                'serviceRequest' => $mappedServiceRequest,
            ];

        } else {
            $payload = [
                'serviceRequest' => $this->toArray($serviceRequest),
            ];
        }


        $pdf = PDF::loadView('pdf.specimenLabel', $payload);

        return $pdf->stream('prueba.pdf');
    }


    public function toArray($serviceRequest): array
    {
        return [
            'id' => $serviceRequest->id,
            'note' => $serviceRequest->note,
            'requisition' => $serviceRequest->requisition,
            'occurrence' => Carbon::parse($serviceRequest->occurrence)->format('d/m/Y h:i:s'),
            'created_user_ip' => $serviceRequest->created_user_ip,
            'updated_user_ip' => $serviceRequest->updated_user_ip,
            'authored_on' => $this->date($serviceRequest->authored_on),
            'updated_at' => $this->date($serviceRequest->updated_at),
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.service-requests.show',
                        ['service_request' => $serviceRequest->id],
                        false),
                ],
                'observations' => [
                    'href' => route('api.service-request.observations', ['service_request' => $serviceRequest->id], false),
                    'collection' => $serviceRequest->observations->map(function ($observation) {
                        return $observation->code;
                    })
                ],
                'specimens' => [
                    'href' => route('api.service-request.specimens', ['service_request' => $serviceRequest->id], false),
                    'collection' => $serviceRequest->specimens->map(function ($specimen) {
                        $var = new BarcodeGenerator();
                        $var->setText($specimen->accession_identifier);
                        $var->setType(BarcodeGenerator::Code128);
                        $var->setScale(2);
                        $var->setThickness(30);
                        $var->setFontSize(12);
                        $var->setLabel('');
                        $code = $var->generate();
                        return [
                            'specimen' => $specimen,
                            'barcode' => "data:image/png;base64," . $code,
                            'specimen_code' => $specimen->code,
                            'specimen_status' => $specimen->status,
                            'container' => $specimen->container,];
                    })
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($serviceRequest->createdUser),
                'updatedUser' => $this->user($serviceRequest->updatedUser),
                'status' => $this->status($serviceRequest->status),
                'intent' => $this->intent($serviceRequest->intent),
                'priority' => $this->priority($serviceRequest->priority),
                'category' => $this->category($serviceRequest->category),
                'patient' => $this->patient($serviceRequest->patient),
                'requester' => $this->requester($serviceRequest->requester),
                'performer' => $this->performer($serviceRequest->performer),
                'location' => $this->location($serviceRequest->location),

            ],
        ];
    }

    private function date($date): ?string
    {
        if (!isset($date)) return null;

        return $date->format('d/m/Y H:i:s');
    }


    private function user($user): ?array
    {
        if (!isset($user)) return null;

        return [
            'name' => $user->names,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $user->id], false)
                ]
            ]
        ];
    }

    private function status($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-statuses.show', ['service_request_status' => $payload->id], false)
                ]
            ]
        ];
    }

    private function intent($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-intents.show', ['service_request_intent' => $payload->id], false)
                ]
            ]
        ];
    }

    private function priority($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-priorities.show', ['service_request_priority' => $payload->id], false)
                ]
            ]
        ];
    }

    private function category($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-categories.show', ['service_request_category' => $payload->id], false)
                ]
            ]
        ];
    }

    private function requester($payload): ?array
    {

        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->names,
            'father_family' => $payload->lastname,
            'mother_family' => $payload->mother_lastname,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $payload->id], false)
                ]
            ]
        ];
    }

    private function performer($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'given' => $payload->given,
            'family' => $payload->family,
            '_links' => [
                'self' => [
                    'href' => route('api.practitioners.show', ['practitioner' => $payload->id], false)
                ]
            ]
        ];

    }

    private function location($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->name,
            '_links' => [
                'self' => [
                    'href' => route('api.locations.show', ['location' => $payload->id], false)
                ]
            ]
        ];
    }

    private function patient($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'id' => $payload->id,
            'name' => $payload->humanNames
                ->filter(function ($name) {
                    return $name->use == 'usual' || $name->use == 'official';
                })
                ->map(function ($name) {
                    return [
                        'use' => $name->use,
                        'given' => $name->given,
                        'father_family' => $name->father_family,
                        'mother_family' => $name->mother_family];
                })[0],
            'birthdate' => Carbon::parse($payload->birthdate)->format('d/m/Y'),
            'administrative_gender' => $payload->administrativeGender->display,
            'identifier' => $payload->identifierPatient
                ->filter(function ($identifier) {
                    return $identifier->identifierUse->code == 'usual' || $identifier->identifierUse->code == 'official';
                })
                ->map(function ($identifier) {
                    return [
                        'use' => $identifier->identifierUse->display,
                        'type' => $identifier->identifierType->display,
                        'value' => $identifier->value,
                    ];
                }),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $payload->id], false)
                ]
            ]
        ];
    }

    public function createOml21Wiener()
    {

        $serviceRequest = ServiceRequest::where('requisition', '21110100002')->first();

        $oml = new OML21Nobilis($this->toArray($serviceRequest), 'NW');

        $hl7 = $oml->create();

        Storage::put("pruebaOML.hl7", str_replace(chr(10), chr(13), $hl7));

        return response()->json(["hl7" => $hl7]);
    }


}
