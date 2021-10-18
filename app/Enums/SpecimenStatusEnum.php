<?php

namespace App\Enums;

abstract class SpecimenStatusEnum
{

    const  AVAILABLE = "disponible";
    const  UNAVAILABLE = "no-disponible";
    const  UNSATISFACTORY = "insatisfactoria";
    const  ENTERED_IN_ERROR = "error";
    const  PENDING = "pendiente";

}
