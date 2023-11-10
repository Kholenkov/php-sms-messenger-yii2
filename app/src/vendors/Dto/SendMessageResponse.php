<?php

declare(strict_types=1);

namespace app\vendors\Dto;

use app\vo\MessageStatus;

final readonly class SendMessageResponse
{
    public function __construct(
        public MessageStatus $status,
        public ?string $messageId = null,
        public ?string $errorMessage = null,
    ) {
    }
}
