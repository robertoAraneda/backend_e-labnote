<?php

namespace App\Enums;

abstract class AppointmentStatusEnum
{

    const  PROPOSED = "proposed";
    const  PENDING = "pending";
    const  BOOKED = "booked";
    const  ARRIVED = "arrived";
    const  FULFILLED = "fulfilled";
    const  CANCELLED = "cancelled";
    const  NO_SHOW = "noshow";
    const  ENTERED_IN_ERROR = "entered-in-error";
    const  CHECKED_IN = "checked-in";
    const  WAIT_LIST = "waitlist";

}
