<?php

declare(strict_types=1);

namespace app\vendors\Contract;

use app\vendors\Dto\GetMessageStatusRequest;
use app\vendors\Dto\GetMessageStatusResponse;
use app\vendors\Dto\SendMessageRequest;
use app\vendors\Dto\SendMessageResponse;

interface Messenger
{
    public function getMessageStatus(GetMessageStatusRequest $request): GetMessageStatusResponse;

    public function sendMessage(SendMessageRequest $request): SendMessageResponse;
}
