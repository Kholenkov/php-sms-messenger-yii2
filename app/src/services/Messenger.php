<?php

declare(strict_types=1);

namespace app\services;

use app\models\Message;
use app\models\Vendor;
use app\vendors\Contract;
use app\vendors\Dto\GetMessageStatusRequest;
use app\vendors\Dto\SendMessageRequest;
use app\vo\MessageStatus;
use app\vo\VendorStatus;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use yii\db\ActiveRecord;

class Messenger
{
    public function __construct(private Contract\MessengerSelector $vendorMessengerSelector)
    {
    }

    public function getMessageStatus(Message $message): void
    {
        if (!$message->getVendorMessageId()) {
            throw new Exception('Empty vendor message id');
        }


        $vendor = $message->getVendor()->one();

        if (!($vendor instanceof Vendor)) {
            throw new Exception('Vendor not found');
        }

        $vendorMessenger = $this->vendorMessengerSelector->selectByVendorType($vendor->getType());


        $vendorRequest = new GetMessageStatusRequest($message->vendorMessageId);

        $vendorResponse = $vendorMessenger->getMessageStatus($vendorRequest);

        $message->setStatus($vendorResponse->status);
        $message->setUpdatedAt(new DateTime());
        if (false === $message->save()) {
            $this->throwExceptionOnFailedSave($message);
        }
    }

    public function sendMessage(string $phoneNumber, string $text): Message
    {
        $vendor = Vendor::find()
            ->where(['status' => VendorStatus::Active->value])
            ->orderBy(['priority' => SORT_DESC, 'type' => SORT_ASC])
            ->one();

        if (!($vendor instanceof Vendor)) {
            throw new Exception('Vendor not found');
        }

        $vendorMessenger = $this->vendorMessengerSelector->selectByVendorType($vendor->getType());


        $message = new Message();
        $message->setUuid(Uuid::uuid4());
        $message->setStatus(MessageStatus::Undefined);
        $message->setPhoneNumber($phoneNumber);
        $message->setText($text);
        $message->setVendorUuid($vendor->getUuid());
        $message->setVendorMessageId('');
        $message->setVendorErrorMessage('');
        $message->setCreatedAt(new DateTime());
        $message->setUpdatedAt(new DateTime());
        if (false === $message->save()) {
            $this->throwExceptionOnFailedSave($message);
        }


        $vendorRequest = new SendMessageRequest(
            $message->getUuid()->toString(),
            $message->getPhoneNumber(),
            $message->getText(),
        );

        $vendorResponse = $vendorMessenger->sendMessage($vendorRequest);

        $message->setStatus($vendorResponse->status);
        if (null !== $vendorResponse->messageId) {
            $message->setVendorMessageId($vendorResponse->messageId);
        }
        if (null !== $vendorResponse->errorMessage) {
            $message->setVendorErrorMessage($vendorResponse->errorMessage);
        }
        $message->setUpdatedAt(new DateTime());
        if (false === $message->save()) {
            $this->throwExceptionOnFailedSave($message);
        }


        return $message;
    }

    protected function throwExceptionOnFailedSave(ActiveRecord $model): void
    {
        $exceptionMessage = 'Unknown error';
        foreach ($model->getFirstErrors() as $exceptionMessage) {
            break;
        }

        throw new Exception($exceptionMessage);
    }
}
