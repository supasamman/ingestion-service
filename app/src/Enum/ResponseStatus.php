<?php

declare(strict_types=1);

namespace App\Enum;

enum ResponseStatus: string
{
    case ACCEPTED = 'accepted';
    case ERROR = 'error';
}
