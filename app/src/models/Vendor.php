<?php

declare(strict_types=1);

namespace app\models;

use app\vo\VendorStatus;
use app\vo\VendorType;
use DateTime;
use DateTimeInterface;
use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property string $uuid
 * @property string $type
 * @property string $status
 * @property int $priority
 * @property string $createdAt
 * @property string $updatedAt
 */
class Vendor extends ActiveRecord implements JsonSerializable
{
    public function getUuid(): UuidInterface
    {
        return Uuid::fromString($this->uuid);
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid->toString();
    }

    public function getType(): VendorType
    {
        return VendorType::from($this->type);
    }

    public function setType(VendorType $type): void
    {
        $this->type = $type->value;
    }

    public function getStatus(): VendorStatus
    {
        return VendorStatus::from($this->status);
    }

    public function setStatus(VendorStatus $status): void
    {
        $this->status = $status->value;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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

    public function getMessages(): ActiveQuery
    {
        return $this->hasMany(Message::class, ['vendorUuid' => 'uuid']);
    }

    // ----

    public function jsonSerialize(): array
    {
        return [
            'uuid' => $this->uuid,
            'type' => $this->type,
            'status' => $this->status,
            'priority' => $this->priority,
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
            [['uuid', 'type', 'status', 'priority', 'createdAt', 'updatedAt'], 'required'],

            ['createdAt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['updatedAt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public static function tableName(): string
    {
        return '{{vendor}}';
    }
}
