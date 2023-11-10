<?php

declare(strict_types=1);

namespace app\vendors;

use app\models\Message;
use app\vendors\Dto\GetMessageStatusRequest;
use app\vendors\Dto\GetMessageStatusResponse;
use app\vendors\Dto\SendMessageRequest;
use app\vendors\Dto\SendMessageResponse;
use app\vo\MessageStatus;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

class Stub implements Contract\Messenger
{
    public function __construct(private StubHelper $helper, private LoggerInterface $logger)
    {
    }

    public function getMessageStatus(GetMessageStatusRequest $request): GetMessageStatusResponse
    {
        try {
            if (!$request->messageId) {
                throw new Exception('Invalid vendor message id');
            }


            $message = Message::find()
                ->where(['vendorMessageId' => $request->messageId])
                ->one();

            if (!($message instanceof Message)) {
                throw new Exception('Message not found');
            }


            switch ($message->getText()) {
                case 'Success send':
                case 'Success send, success delivery':
                    $status = MessageStatus::SuccessDelivery;
                    break;
                default:
                    $status = MessageStatus::ErrorDelivery;
                    break;
            }


            return new GetMessageStatusResponse($status);
        } catch (Throwable $throwable) {
            $this->logThrowable($throwable);

            throw new Exception('Error get message status', 0, $throwable);
        }
    }

    public function sendMessage(SendMessageRequest $request): SendMessageResponse
    {
        try {
            if ('Empty text' === $request->text) {
                throw new Exception('Send message failed');
            }


            switch ($request->text) {
                case 'Success send':
                case 'Success send, success delivery':
                case 'Success send, error delivery':
                    $status = MessageStatus::SuccessSend;
                    break;
                case 'Error send':
                default:
                    $status = MessageStatus::ErrorSend;
                    break;
            }

            $messageId = MessageStatus::SuccessSend === $status
                ? $this->helper->generateMessageId()
                : '';


            return new SendMessageResponse($status, $messageId);
        } catch (Throwable $throwable) {
            $this->logThrowable($throwable);

            return new SendMessageResponse(MessageStatus::ErrorSend);
        }
    }

    protected function logThrowable(Throwable $throwable): void
    {
        $this->logger->error(
            $throwable->getMessage(),
            [
                'code' => $throwable->getCode(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace(),
            ]
        );
    }
}
