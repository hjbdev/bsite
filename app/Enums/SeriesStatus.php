<?php

namespace App\Enums;

enum SeriesStatus: string
{
    case UPCOMING = 'upcoming';
    case ONGOING = 'ongoing';
    case FINISHED = 'finished';
    case CANCELLED = 'cancelled';
}