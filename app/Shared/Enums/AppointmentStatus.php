<?php

namespace App\Shared\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'Pending';
    case AWAITING_PAYMENT = 'AwaitingPayment';
    case PAID = 'Paid';
    case CONFIRMED = 'Confirmed';
    case UPCOMING = 'Upcoming';
    case IN_PROGRESS = 'InProgress';
    case COMPLETED = 'Completed';
    case CANCELLED_BY_PATIENT = 'CancelledByPatient';
    case CANCELLED_BY_DOCTOR = 'CancelledByDoctor';
    case EXPIRED = 'Expired';
    case NO_SHOW = 'NoShow';
    case REFUNDED = 'Refunded';
}
