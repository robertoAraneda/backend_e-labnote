<?php

namespace App\Integrations;

use App\Models\IdentifierType;
use App\Models\IntegrationTypeDocument;
use App\Models\NobilisDocumentType;
use Aranyasen\HL7\Message;
use Aranyasen\HL7\Segments\EVN;
use Aranyasen\HL7\Segments\MSH;
use Aranyasen\HL7\Segments\PID;
use Carbon\Carbon;

class ADTNobilis
{

    private $payload;
    private $typeEvent;

    public function __construct($payload, $typeEvent)
    {
        $this->payload = $payload;
        $this->typeEvent = $typeEvent;
    }

    /*{
    "id": 1,
    "identifier": [
        {
            "id": 1,
            "identifier_use_id": 2,
            "identifierUse": {
                "id": 2,
                "code": "official",
                "display": "Oficial",
                "created_at": "2021-08-17T06:13:46.000000Z",
                "updated_at": "2021-08-17T06:13:46.000000Z"
            },
            "identifier_type_id": 1,
            "identifierType": {
                "id": 1,
                "code": "rut",
                "display": "RUT",
                "created_at": "2021-08-17T06:13:46.000000Z",
                "updated_at": "2021-08-17T06:13:46.000000Z"
            },
            "value": "15654738-7"
        }
    ],
    "name": [
        {
            "id": 1,
            "use": "official",
            "given": "ROBERTO",
            "text": "ROBERTO ARANEDA ESPINOZA",
            "father_family": "ARANEDA",
            "mother_family": "ESPINOZA",
            "_links": {
                "self": {
                    "href": "/api/v1/users/1"
                }
            }
        }
    ],
    "telecom": [
        {
            "id": 1,
            "system": "EMAIL",
            "value": "robaraneda@gmail.com",
            "use": "PERSONAL"
        },
        {
            "id": 2,
            "system": "EMAIL",
            "value": "roberto.araneda@minsal.cl",
            "use": "TRABAJO"
        },
        {
            "id": 11,
            "system": "TELÉFONO",
            "value": "+56958639620",
            "use": "PERSONAL"
        }
    ],
    "address": [
        {
            "id": 2,
            "use": "TRABAJO",
            "text": "AMUNATEGUI 890 DEPARTAMENTO 2003",
            "city_code": "13101",
            "city_name": "SANTIAGO",
            "state_code": "13",
            "state_name": "REGIÓN METROPOLITANA DE SANTIAGO"
        },
        {
            "id": 1,
            "use": "PARTICULAR",
            "text": "JUAN ENRIQUE RODO 05080",
            "city_code": "09101",
            "city_name": "TEMUCO",
            "state_code": "09",
            "state_name": "REGIÓN DE LA ARAUCANÍA"
        }
    ],
    "contact": [
        {
            "id": 1,
            "given": "ROBERTO",
            "family": "ARANEDA",
            "relationship": "PADRE",
            "email": "robaraneda@gmail.com",
            "phone": "958639620"
        }
    ],
    "administrative_gender_id": 1,
    "birthdate": "1983-12-06",
    "active": true,
    "created_at": "21/08/2021 01:09:02",
    "_embedded": {
        "administrativeGender": {
            "display": "Masculino",
            "_links": {
                "self": {
                    "href": "/api/v1/administrative-genders/1"
                }
            }
        }
    }
}*/

    public function create()
    {

        $msg = new Message();
        $msh = $this->createMSH();
        $msg->addSegment($msh);

        $evn = $this->createEVN();
        $msg->addSegment($evn);

        $pid = $this->createPID();
        $msg->addSegment($pid);

        return $msg->toString(true);

    }

    private function createMSH()
    {
        $msh = new MSH();

        $msh->setSendingApplication('ELABNOTE'); //sistema emisor
        $msh->setSendingFacility('MIRTH_3.5'); // sistema receptor
        $msh->setReceivingApplication('LIS'); // sistemna receptor final
        $msh->setMessageType("ADT^" . $this->typeEvent); // tipo de mensaje
        $msh->setMessageControlId(Carbon::now()->format('YmdHis')); // control ID
        $msh->setProcessingId('T'); // ???
        $msh->setVersionId('2.6'); // versión hl7 de wiener

        return $msh;
    }

    private function createEVN()
    {
        $evn = new EVN();
        //EVN_1
        $evn->setEventTypeCode($this->typeEvent);//tipo de evento
        $evn->setRecordedDateTime(Carbon::createFromFormat("d/m/Y H:i:s", $this->payload['created_at'])->format('Ymd'));

        return $evn;
    }

    private function createPID()
    {
        $pid = new PID();

        $identifier = $this->payload['identifier'][0];
        $name = $this->payload['name'][0];
        $administrativeGender = $this->payload['_embedded']['administrativeGender']['display'];
        $address = $this->payload['address'][0];

        $integrationDocumentType = IntegrationTypeDocument::where('lis_name', 'Nobilis')
            ->where('identifier_type_id', $identifier['identifier_type_id'])->first();

        $nobilisDocumentType = NobilisDocumentType::find($integrationDocumentType->model_id);

        //PID_1
        $pid->setID($this->payload['id']);
        //PID_2_CX_1
        $pid->setPatientID($identifier['value']);
        //PID_3-CX_1 y PID_3-CX_2
        $pid->setPatientIdentifierList($nobilisDocumentType->id . "^" . $nobilisDocumentType->description);
        //PID_5-XPN_1, PID_5-XPN_2 y PID_5-XPN_3
        $pid->setPatientName($name['father_family'] . "^" . $name['given'] . "^" . $name['mother_family']);
        //PID_7
        $pid->setDateTimeOfBirth(Carbon::parse( $this->payload['birthdate'])->format('Ymd'));
        //PID_8
        $pid->setSex($administrativeGender == 'Masculino' ? 'M' : 'F');
        //PID_11-XAD_1
        $pid->setPatientAddress($address['text']."^^^^".$address['city_name']."^^".$address['state_name']);
        //PID_12
        $pid->setCountryCode('CHL');
        //PID_13-XTN_4
       $email =  collect( $this->payload['telecom'])->filter(function ($telecom) {
            return $telecom['system'] == 'EMAIL';
        })[0];

        $pid->setPhoneNumberHome("^^^".$email['value']);

        return $pid;
    }
}
