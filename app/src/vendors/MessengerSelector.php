<?php

declare(strict_types=1);

namespace app\vendors;

use app\vo\VendorType;
use Exception;
use yii\di\Container;

class MessengerSelector implements Contract\MessengerSelector
{
    public function __construct(private Container $container)
    {
    }

    public function selectByVendorType(VendorType $vendorType): Contract\Messenger
    {
        $messenger = $this->container->get(Contract\Messenger::class . '__' . $vendorType->value);

        if (!($messenger instanceof Contract\Messenger)) {
            throw new Exception('Cannot create messenger');
        }

        return $messenger;
    }
}
