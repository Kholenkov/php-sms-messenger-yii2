<?php

declare(strict_types=1);

namespace app\vendors\Dto;

final readonly class GetMessageStatusRequest
{
    public function __construct(public string $messageId)
    {
    }
}
