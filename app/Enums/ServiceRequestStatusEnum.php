<?php

namespace App\Enums;

abstract class ServiceRequestStatusEnum
{

    const  DRAFT = "borrador";
    const  ACTIVE = "activo";
    const  ON_HOLD = "suspendido";
    const  REVOKED = "cancelado";
    const  COMPLETED = "completo";
    const  ENTERED_IN_ERROR = "ingresado-con-error";
    const  UNKNOWN = "unknown";

}
