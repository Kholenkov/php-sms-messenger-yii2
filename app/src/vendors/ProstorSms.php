<?php

declare(strict_types=1);

namespace app\vendors;

use app\vendors\Dto\GetMessageStatusRequest;
use app\vendors\Dto\GetMessageStatusResponse;
use app\vendors\Dto\SendMessageRequest;
use app\vendors\Dto\SendMessageResponse;
use app\vo\MessageStatus;
use Exception;
use Kholenkov\ProstorSmsSdk\Dto;
use Kholenkov\ProstorSmsSdk\Interfaces;
use Kholenkov\ProstorSmsSdk\ValueObject;
use Psr\Log\LoggerInterface;
use Throwable;

class ProstorSms implements Contract\Messenger
{
    public function __construct(
        private Interfaces\Messages $api,
        private LoggerInterface $logger,
    ) {
    }

    public function getMessageStatus(GetMessageStatusRequest $request): GetMessageStatusResponse
    {
        try {
            $apiRequest = new Dto\Messages\GetStatusRequest(
                new Dto\Messages\MessageIdCollection(
                    new Dto\Messages\MessageId(
                        new ValueObject\MessageId($request->messageId),
                    ),
                ),
            );

            $apiResponse = $this->api->getStatus($apiRequest);

            if (
                ValueObject\ResponseStatus::Ok !== $apiResponse->status
                || null === $apiResponse->messages
            ) {
                throw new Exception($apiResponse->description ?: 'Error get status message');
            }


            $status = MessageStatus::ErrorSend;

            foreach ($apiResponse->messages as $message) {
                switch ($message->status) {
                    case ValueObject\MessageStatus::Accepted:
                    case ValueObject\MessageStatus::Queued:
                    case ValueObject\MessageStatus::SmscDelivered:
                        $status = MessageStatus::SuccessSend;
                        break;
                    case ValueObject\MessageStatus::Delivered:
                        $status = MessageStatus::SuccessDelivery;
                        break;
                    case ValueObject\MessageStatus::DeliveryError:
                    case ValueObject\MessageStatus::SmscRejected:
                        $status = MessageStatus::ErrorDelivery;
                        break;
                }
            }

            return new GetMessageStatusResponse($status);
        } catch (Throwable $throwable) {
            $this->logThrowable($throwable);

            throw new Exception('Error get status message', 0, $throwable);
        }
    }

    public function sendMessage(SendMessageRequest $request): SendMessageResponse
    {
        try {
            $apiRequest = new Dto\Messages\SendRequest(
                new Dto\Messages\MessageCollection(
                    new Dto\Messages\Message(
                        new ValueObject\MessageId(str_replace('-', '', $request->messageId)),
                        new ValueObject\PhoneNumber($request->phoneNumber),
                        $request->text,
                    ),
                ),
            );

            $apiResponse = $this->api->send($apiRequest);

            if (
                ValueObject\ResponseStatus::Ok !== $apiResponse->status
                || null === $apiResponse->messages
            ) {
                throw new Exception($apiResponse->description ?: 'Error send message');
            }


            $status = MessageStatus::ErrorSend;
            $messageId = '';

            foreach ($apiResponse->messages as $message) {
                switch ($message->status) {
                    case ValueObject\MessageStatus::Accepted:
                    case ValueObject\MessageStatus::Queued:
                    case ValueObject\MessageStatus::SmscDelivered:
                        $status = MessageStatus::SuccessSend;
                        break;
                    case ValueObject\MessageStatus::Delivered:
                        $status = MessageStatus::SuccessDelivery;
                        break;
                    case ValueObject\MessageStatus::DeliveryError:
                    case ValueObject\MessageStatus::SmscRejected:
                        $status = MessageStatus::ErrorDelivery;
                        break;
                }

                $messageId = (string) $message->smscId;
            }

            return new SendMessageResponse($status, $messageId);
        } catch (Throwable $throwable) {
            $this->logThrowable($throwable);

            return new SendMessageResponse(MessageStatus::ErrorSend, '', $throwable->getMessage());
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
