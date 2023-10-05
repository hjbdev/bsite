<?php

namespace App\Enums;

enum SeriesMapStatus: string
{
    case UPCOMING = 'upcoming';
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';
}