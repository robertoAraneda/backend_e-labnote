<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObservationServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'clinical_information' => $this->clinical_information,
            'active' => (bool)$this->active,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.observation-service-requests.show', ['observation_service_request' => $this->id], false),
                ],
                'samplingConditions' => [
                    'href' => route('api.observation-service-request.sampling-conditions.index', ['observation_service_request' => $this->id], false)
                ]
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'process_time' => $this->processTime($this->processTime),
                'workarea' => $this->workarea($this->workarea),
                'medical_request_type' => $this->medicalRequestType($this->medicalRequestType),
                'availability' => $this->availability($this->availability),
                'specimen' => $this->specimen($this->specimen),
                'loinc' => $this->loinc($this->loinc),
                'analyte' => $this->analyte($this->analyte),
                'samplingConditions' => $this->samplingConditions($this->samplingConditions)
            ],
        ];
    }


    /**
     * @param $date
     * @return string|null
     */

    private function date($date): ?string
    {
        if (!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }


    /**
     * @param $user
     * @return array|null
     */

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


    /**
     * @param $availability
     * @return array|null
     */

    private function availability($availability): ?array
    {
        if (!isset($availability)) return null;

        return [
            'name' => $availability->name,
            '_links' => [
                'self' => [
                    'href' => route('api.availabilities.show', ['availability' => $availability->id], false)
                ]
            ]
        ];
    }

    /**
     * @param $analyte
     * @return array|null
     */

    private function analyte($analyte): ?array
    {
        if (!isset($analyte)) return null;

        return [
            'name' => $analyte->name,
            '_links' => [
                'self' => [
                    'href' => route('api.analytes.show', ['analyte' => $analyte->id], false)
                ]
            ]
        ];
    }


    /**
     * @param $loinc
     * @return array|null
     */

    private function loinc($loinc): ?array
    {
        if (!isset($loinc)) return null;

        return [
            'name' => $loinc->long_common_name,
            '_links' => [
                'self' => [
                    'href' => route('api.loincs.show', ['loinc' => $loinc->loinc_num], false)
                ]
            ]
        ];
    }

    /**
     * @param $specimen
     * @return array|null
     */

    private function specimen($specimen): ?array
    {
        if (!isset($specimen)) return null;

        return [
            'name' => $specimen->name,
            '_links' => [
                'self' => [
                    'href' => route('api.specimens.show', ['specimen' => $specimen->id], false)
                ]
            ]
        ];
    }

    /**
     * @param $processTime
     * @return array|null
     */

    private function processTime($processTime): ?array
    {
        if (!isset($processTime)) return null;

        return [
            'name' => $processTime->name,
            '_links' => [
                'self' => [
                    'href' => route('api.process-times.show', ['process_time' => $processTime->id], false)
                ]
            ]
        ];
    }

    /**
     * @param $workarea
     * @return array|null
     */

    private function workarea($workarea): ?array
    {
        if (!isset($workarea)) return null;

        return [
            'name' => $workarea->name,
            '_links' => [
                'self' => [
                    'href' => route('api.workareas.show', ['workarea' => $workarea->id], false)
                ]
            ]
        ];
    }

    /**
     * @param $medicalRequestType
     * @return array|null
     */

    private function medicalRequestType($medicalRequestType): ?array
    {
        if (!isset($medicalRequestType)) return null;

        return [
            'name' => $medicalRequestType->name,
            '_links' => [
                'self' => [
                    'href' => route('api.medical-request-types.show', ['medical_request_type' => $medicalRequestType->id], false)
                ]
            ]
        ];
    }

    private function samplingConditions($samplingConditions)
    {
        if (!isset($samplingConditions)) return null;

        return $samplingConditions->map(function ($samplingCondition) {
            return [
                'name' => $samplingCondition->name,
                '_links' => [
                    'self' => route('api.sampling-conditions.show', ['sampling_condition' => $samplingCondition->id], false),
                ]
            ];
        });
    }
}
