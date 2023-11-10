<?php

declare(strict_types=1);

namespace app\vendors;

use Ramsey\Uuid\Uuid;

class StubHelper
{
    public function generateMessageId(): string
    {
        return Uuid::uuid6()->toString();
    }
}
