<?php

declare(strict_types=1);

namespace app\vo;

enum VendorType: string
{
    case ProstorSms = 'prostor_sms';
    case Stub = 'stub';
}
