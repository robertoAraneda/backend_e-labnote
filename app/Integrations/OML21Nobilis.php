<?php

namespace App\Integrations;

use App\Models\IdentifierType;
use App\Models\IntegrationObservationServiceRequest;
use App\Models\IntegrationTypeDocument;
use App\Models\NobilisDocumentType;
use App\Models\NobilisObservationsServiceRequest;
use Aranyasen\HL7\Connection;
use Aranyasen\HL7\Message; // If Message is used
use Aranyasen\HL7\Segment; // If Segment is used
use Aranyasen\HL7\Segments\DG1;
use Aranyasen\HL7\Segments\MSH;
use Aranyasen\HL7\Segments\NTE;
use Aranyasen\HL7\Segments\OBR;
use Aranyasen\HL7\Segments\ORC;
use Aranyasen\HL7\Segments\PID;
use Carbon\Carbon;

// If MSH is used

class OML21Nobilis
{
    private $eventType;
    private $payload;
    private $requisition;
    private $authoredOn;
    private $patient;
    private $observations;

    public function __construct($payload, $eventType)
    {
        $this->payload = $payload;
        $this->eventType = $eventType;

        $this->requisition = $payload['requisition'];
        $this->requisition = $payload['requisition'];
        $this->authoredOn = Carbon::parse($payload['authored_on']);
        $this->patient = $payload['_embedded']['patient'];
        $this->observations = $payload['_links']['observations']['collection'];

    }

    public function create()
    {
        $msg = new Message();
        $msh = $this->createMSH();
        $msg->addSegment($msh);

        $pid = $this->createPID();
        $msg->addSegment($pid);

        $orc = $this->createORC();
        $msg->addSegment($orc);

        $dg1 = $this->createDG1();
        $msg->addSegment($dg1);

        $observations = $this->createOBR();

        foreach ($observations as $observation){

            $msg->addSegment($observation);
        }

        $nte = $this->createNTE();
        $msg->addSegment($nte);

        $connection = new Connection('190.151.59.106', '6665');

        $response = $connection->send($msg);

        return $msg->toString(true);

    }

    private function createPID(){
        $pid = new PID();

        $identifier = $this->patient['identifier'][0];
        $identifierType = IdentifierType::where('display', $identifier['type'])->first();

        $integrationDocumentType = IntegrationTypeDocument::where('lis_name', 'Nobilis')
            ->where('identifier_type_id', $identifierType->id)->first();

        $nobilisDocumentType = NobilisDocumentType::find($integrationDocumentType->model_id);

        //PID_1
        $pid->setID($this->patient['id']);
        //PID_2_CX_1
        $pid->setPatientID($identifier['value']);
        //PID_3-CX_1 y PID_3-CX_2
        $pid->setPatientIdentifierList($identifierType->code . "^" . $nobilisDocumentType->description);
        //PID_5-XPN_1, PID_5-XPN_2 y PID_5-XPN_3
        $pid->setPatientName($this->patient['name']['father_family'] . "^" . $this->patient['name']['given'] . "^" . $this->patient['name']['mother_family']);
        //PID_7
        $pid->setDateTimeOfBirth(Carbon::createFromFormat('d/m/Y', $this->patient['birthdate'])->format('Ymd'));
        //PID_9
        $pid->setPatientAlias($this->patient['administrative_gender']=='Masculino'? 'M': 'F');

        return $pid;
    }

    private function createMSH(){
        $msh = new MSH();

        //control id es creado por la combinación del numero de solicitud +  la fecha de creación en formato YmdHis
        $mshControlId = $this->requisition . "-" . $this->authoredOn->format('YmdHis');

        $msh->setSendingApplication('ELABNOTE'); //sistema emisor
        $msh->setSendingFacility('MIRTH_3.5'); // sistema receptor
        $msh->setReceivingApplication('LIS'); // sistemna receptor final
        $msh->setMessageType("OML^O21"); // tipo de mensaje
        $msh->setMessageControlId($mshControlId); // control ID
        $msh->setProcessingId('N'); // ???
        $msh->setVersionId('2.6'); // versión hl7 de wiener

        return $msh;
    }

    private function createORC(){
        $orc = new ORC();

        $performer = $this->payload['_embedded']['performer'];
        $requester = $this->payload['_embedded']['requester'];

        //ORC_1
        $orc->setOrderControl($this->eventType);
        //ORC_2
        $orc->setPlacerOrderNumber($this->requisition);
        //ORC_9
        $orc->setDateTimeofTransaction($this->authoredOn->format('YmdHis'));
        //ORC_10-XCN_2 y ORC_10-XCN_3
        $orc->setEnteredBy("^".$performer['family']."^".$performer['given']);
        //ORC_12-XCN_1, ORC_12-XCN_2,  ORC_12-XCN_3 y ORC_12-XCN_4 -> final ORC_12-AssigningAgencyOrDepartment
        $orc->setOrderingProvider($requester['id']."^".$requester['father_family']."^".$requester['name']."^".$requester['mother_family']."^^^^^^^^^^^^^^^^^^^&&&&SALA 308 - ES&"); //TODO agregar servicios labisur
        //ORC_17-CWE_2
        $orc->setEnteringOrganization("101^LABISUR"); //TODO agregar lista de origen paciente
        //ORC_21-XON_2
        $orc->setOrderingFacilityName('1^LABORATORIO'); //este valor es por defecto
        //ORC_22-XAD_1 queda vacío impresora por defecto.
        //ORC_22-XAD_2 queda vacío impresora por defecto.

        return $orc;
    }

    private function createOBR(){

        $observations = [];

        foreach ($this->observations as $index => $observation){
            $integrationServiceRequest = IntegrationObservationServiceRequest::where('observation_service_request_id', $observation['id'])
                ->where('lis_name', 'Nobilis')
                ->where('active', true)
                ->first();

            //TODO quitar el .0 del id del nobilis service request
            $nobilisObservationServiceRequest = NobilisObservationsServiceRequest::find($integrationServiceRequest->model_id);

            if(!isset($nobilisObservationServiceRequest)){
                $nobilisObservationServiceRequest = NobilisObservationsServiceRequest::find($integrationServiceRequest->model_id.".0");
            }

            $obr = new OBR();

            $obr->setID($index + 1 );
            $obr->setPlacerOrderNumber($this->requisition);
            $obr->setUniversalServiceID($nobilisObservationServiceRequest->id."^".trim($nobilisObservationServiceRequest->description));

            $observations[] = $obr;
        }

        return $observations;
    }

    private function createDG1(){
        $dg1 = new DG1();

        //DG1_3-CWE_1 y DG1_3-CWE_2
        $dg1->setDiagnosisCodeDG1("4792^INFECCION VIRAL, NO ESPECIFICADA");//TODO agregar a la tabla diagnosticos

        return $dg1;
    }

    private function createNTE(){
        $nte = new NTE();

        $nte->setComment($this->payload['note']);

        return $nte;
    }


}
