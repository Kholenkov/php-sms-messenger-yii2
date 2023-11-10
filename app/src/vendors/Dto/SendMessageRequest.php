<?php

declare(strict_types=1);

namespace app\vendors\Dto;

final readonly class SendMessageRequest
{
    public function __construct(
        public string $messageId,
        public string $phoneNumber,
        public string $text,
    ) {
    }
}
