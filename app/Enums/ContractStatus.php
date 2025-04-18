<?php

namespace App\Enums;

enum ContractStatus: string
{
    case PENDING = 'Pending';
    case SIGNED = 'Signed';
}