<?php

declare(strict_types=1);

namespace app\vendors\Contract;

use app\vo\VendorType;

interface MessengerSelector
{
    public function selectByVendorType(VendorType $vendorType): Messenger;
}
