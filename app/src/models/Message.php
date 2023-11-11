<?php

declare(strict_types=1);

namespace app\models;

use app\vo\MessageStatus;
use DateTime;
use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $status
 * @property string $phoneNumber
 * @property string $text
 * @property string $vendorUuid
 * @property string $vendorMessageId
 * @property string $vendorErrorMessage
 * @property string $createdAt
 * @property string $updatedAt
 */
class Message extends ActiveRecord implements JsonSerializable
{
    public function getUuid(): UuidInterface
    {
        return Uuid::fromString($this->uuid);
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid->toString();
    }

    public function getStatus(): MessageStatus
    {
        return MessageStatus::from($this->status);
    }

    public function setStatus(MessageStatus $status): void
    {
        $this->status = $status->value;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getVendorUuid(): UuidInterface
    {
        return Uuid::fromString($this->vendorUuid);
    }

    public function setVendorUuid(UuidInterface $vendorUuid): void
    {
        $this->vendorUuid = $vendorUuid->toString();
    }

    public function getVendorMessageId(): string
    {
        return $this->vendorMessageId;
    }

    public function setVendorMessageId(string $vendorMessageId): void
    {
        $this->vendorMessageId = $vendorMessageId;
    }

    public function getVendorErrorMessage(): string
    {
        return $this->vendorErrorMessage;
    }

    public function setVendorErrorMessage(string $vendorErrorMessage): void
    {
        $this->vendorErrorMessage = $vendorErrorMessage;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return new DateTime($this->createdAt);
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt->format('Y-m-d H:i:s');
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return new DateTime($this->updatedAt);
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt->format('Y-m-d H:i:s');
    }

    // ----

    public function getVendor(): ActiveQuery
    {
        return $this->hasOne(Vendor::class, ['uuid' => 'vendorUuid']);
    }

    // ----

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => $this->status,
            'phoneNumber' => $this->phoneNumber,
            'text' => $this->text,
            'vendorUuid' => $this->vendorUuid,
            'vendorMessageId' => $this->vendorMessageId,
            'vendorErrorMessage' => $this->vendorErrorMessage,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function primaryKey(): array
    {
        return ['uuid'];
    }

    public function rules(): array
    {
        return [
            [['uuid', 'status', 'phoneNumber', 'text', 'vendorUuid', 'createdAt', 'updatedAt'], 'required'],

            ['createdAt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['updatedAt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public static function tableName(): string
    {
        return '{{message}}';
    }
}
