<?php

declare(strict_types=1);

use app\models\Message;
use app\models\Vendor;
use app\vo\MessageStatus;
use app\vo\VendorStatus;
use app\vo\VendorType;
use Ramsey\Uuid\Uuid;
use yii\db\Migration;

class m231110_090000_init extends Migration
{
    public function up(): bool
    {
        $messagesStatuses = [];
        foreach (MessageStatus::cases() as $case) {
            $messagesStatuses[] = $case->value;
        }

        $vendorsStatuses = [];
        foreach (VendorStatus::cases() as $case) {
            $vendorsStatuses[] = $case->value;
        }

        $vendorsTypes = [];
        foreach (VendorType::cases() as $case) {
            $vendorsTypes[] = $case->value;
        }


        $this->createTable(
            Vendor::tableName(),
            [
                'uuid' => $this->char(36)->notNull() . ' PRIMARY KEY',
                'type' => "ENUM('" . implode("','", $vendorsTypes) . "') NOT NULL",
                'status' => "ENUM('" . implode("','", $vendorsStatuses) . "') NOT NULL",
                'priority' => $this->smallInteger(),
                'createdAt' => $this->dateTime()->notNull(),
                'updatedAt' => $this->dateTime()->notNull(),
            ]
        );

        $this->createIndex('index__vendor__type', Vendor::tableName(), ['type']);
        $this->createIndex('index__vendor__status', Vendor::tableName(), ['status']);
        $this->createIndex('index__vendor__priority', Vendor::tableName(), ['priority']);

        $vendors = [
            VendorType::Stub->value => 100,
            VendorType::ProstorSms->value => 90,
        ];

        foreach ($vendors as $vendorTypeValue => $vendorPriority) {
            $vendor = new Vendor();
            $vendor->setUuid(Uuid::uuid4());
            $vendor->setType(VendorType::from($vendorTypeValue));
            $vendor->setStatus(VendorStatus::Active);
            $vendor->setPriority($vendorPriority);
            $vendor->setCreatedAt(new DateTime());
            $vendor->setUpdatedAt(new DateTime());
            $vendor->save();
        }


        $this->createTable(
            Message::tableName(),
            [
                'uuid' => $this->char(36)->notNull() . ' PRIMARY KEY',
                'status' => "ENUM('" . implode("','", $messagesStatuses) . "') NOT NULL",
                'phoneNumber' => $this->string(25)->notNull(),
                'text' => $this->text()->notNull(),
                'vendorUuid' => $this->char(36)->notNull(),
                'vendorMessageId' => $this->string(255)->notNull(),
                'vendorErrorMessage' => $this->text()->notNull(),
                'createdAt' => $this->dateTime()->notNull(),
                'updatedAt' => $this->dateTime()->notNull(),
            ]
        );

        $this->createIndex('index__message__status', Message::tableName(), ['status']);
        $this->createIndex('index__message__phoneNumber', Message::tableName(), ['phoneNumber']);
        $this->createIndex('index__message__vendorUuid', Message::tableName(), ['vendorUuid']);

        $this->addForeignKey(
            'fk__message__vendorUuid',
            Message::tableName(),
            'vendorUuid',
            Vendor::tableName(),
            'uuid'
        );

        return true;
    }

    public function down(): bool
    {
        $this->dropForeignKey('fk__message__vendorUuid', Message::tableName());
        $this->dropTable(Message::tableName());

        $this->dropTable(Vendor::tableName());

        return true;
    }
}
