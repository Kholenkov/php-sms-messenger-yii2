<?php

declare(strict_types=1);

namespace app\vendors\Dto;

use app\vo\MessageStatus;

final readonly class GetMessageStatusResponse
{
    public function __construct(public MessageStatus $status)
    {
    }
}
