<?php

declare(strict_types=1);

namespace app\vendors;

use app\vendors\Dto\GetMessageStatusRequest;
use app\vendors\Dto\GetMessageStatusResponse;
use app\vendors\Dto\SendMessageRequest;
use app\vendors\Dto\SendMessageResponse;
use app\vo\MessageStatus;
use Exception;
use Kholenkov\ProstorSmsSdk;
use Psr\Log\LoggerInterface;
use Throwable;

class ProstorSms implements Contract\Messenger
{
    public function __construct(
        private ProstorSmsSdk\Interfaces\Messages $api,
        private LoggerInterface $logger,
    ) {
    }

    public function getMessageStatus(GetMessageStatusRequest $request): GetMessageStatusResponse
    {
        try {
            $apiRequest = new ProstorSmsSdk\Dto\Messages\GetStatusRequest(
                new ProstorSmsSdk\Dto\Messages\MessageIdCollection(
                    new ProstorSmsSdk\Dto\Messages\MessageId(
                        new ProstorSmsSdk\ValueObject\MessageId($request->messageId),
                    ),
                ),
            );

            $apiResponse = $this->api->getStatus($apiRequest);

            if (
                ProstorSmsSdk\ValueObject\ResponseStatus::Ok !== $apiResponse->status
                || null === $apiResponse->messages
            ) {
                throw new Exception($apiResponse->description ?: 'Error get status message');
            }


            $status = MessageStatus::ErrorSend;

            foreach ($apiResponse->messages as $message) {
                switch ($message->status) {
                    case ProstorSmsSdk\ValueObject\MessageStatus::Accepted:
                    case ProstorSmsSdk\ValueObject\MessageStatus::Queued:
                    case ProstorSmsSdk\ValueObject\MessageStatus::SmscDelivered:
                        $status = MessageStatus::SuccessSend;
                        break;
                    case ProstorSmsSdk\ValueObject\MessageStatus::Delivered:
                        $status = MessageStatus::SuccessDelivery;
                        break;
                    case ProstorSmsSdk\ValueObject\MessageStatus::DeliveryError:
                    case ProstorSmsSdk\ValueObject\MessageStatus::SmscRejected:
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
            $apiRequest = new ProstorSmsSdk\Dto\Messages\SendRequest(
                new ProstorSmsSdk\Dto\Messages\MessageCollection(
                    new ProstorSmsSdk\Dto\Messages\Message(
                        new ProstorSmsSdk\ValueObject\MessageId(str_replace('-', '', $request->messageId)),
                        new ProstorSmsSdk\ValueObject\PhoneNumber($request->phoneNumber),
                        $request->text,
                    ),
                ),
            );

            $apiResponse = $this->api->send($apiRequest);

            if (
                ProstorSmsSdk\ValueObject\ResponseStatus::Ok !== $apiResponse->status
                || null === $apiResponse->messages
            ) {
                throw new Exception($apiResponse->description ?: 'Error send message');
            }


            $status = MessageStatus::ErrorSend;
            $messageId = '';

            foreach ($apiResponse->messages as $message) {
                switch ($message->status) {
                    case ProstorSmsSdk\ValueObject\MessageStatus::Accepted:
                    case ProstorSmsSdk\ValueObject\MessageStatus::Queued:
                    case ProstorSmsSdk\ValueObject\MessageStatus::SmscDelivered:
                        $status = MessageStatus::SuccessSend;
                        break;
                    case ProstorSmsSdk\ValueObject\MessageStatus::Delivered:
                        $status = MessageStatus::SuccessDelivery;
                        break;
                    case ProstorSmsSdk\ValueObject\MessageStatus::DeliveryError:
                    case ProstorSmsSdk\ValueObject\MessageStatus::SmscRejected:
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
