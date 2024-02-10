<?php

namespace App\Enums;

enum DemoStatus: string
{
    case NONE = 'none';
    case PENDING = 'pending';
    case PARSED = 'parsed';
    case FAILED = 'failed';
}