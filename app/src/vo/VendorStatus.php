<?php

declare(strict_types=1);

namespace app\vo;

enum VendorStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
