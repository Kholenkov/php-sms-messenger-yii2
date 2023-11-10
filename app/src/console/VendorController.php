<?php

declare(strict_types=1);

namespace app\console;

use app\models\Vendor;
use app\vo\VendorStatus;
use app\vo\VendorType;
use DateTime;
use Exception;
use Ramsey\Uuid\Uuid;
use yii\console\ExitCode;
use yii\db\ActiveRecord;

class VendorController extends BaseController
{
    public function actionList(): int
    {
        try {
            $vendors = Vendor::find()
                ->orderBy(['priority' => SORT_DESC, 'type' => SORT_ASC])
                ->all();

            echo json_encode($vendors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), PHP_EOL;

            return ExitCode::OK;
        } catch (Exception $exception) {
            echo $exception->getMessage(), PHP_EOL;

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    public function actionSave(string $typeValue, string $statusValue = '', int $priority = -1): int
    {
        try {
            $type = VendorType::tryFrom($typeValue);
            if (null === $type) {
                throw new Exception('Invalid type value');
            }

            $defaultStatus = VendorStatus::Active;
            $defaultPriority = 100;


            $vendor = Vendor::find()
                ->where(['type' => $type->value])
                ->one();

            if (!($vendor instanceof Vendor)) {
                $vendor = new Vendor();
                $vendor->setUuid(Uuid::uuid4());
                $vendor->setType($type);
                $vendor->setStatus($defaultStatus);
                $vendor->setPriority($defaultPriority);
                $vendor->setCreatedAt(new DateTime());
                $vendor->setUpdatedAt(new DateTime());
                if (false === $vendor->save()) {
                    $this->throwExceptionOnFailedSave($vendor);
                }
            }

            echo $vendor->getUuid()->toString(), PHP_EOL;


            $hasChange = false;

            if ('' === $statusValue) {
                echo 'Change status skipped', PHP_EOL;
            } else {
                $status = VendorStatus::tryFrom($statusValue);
                if (null === $status) {
                    throw new Exception('Invalid status value');
                }

                if ($status === $vendor->getStatus()) {
                    echo 'Change status skipped', PHP_EOL;
                } else {
                    $vendor->setStatus($status);

                    $hasChange = true;
                    echo 'Status changed', PHP_EOL;
                }
            }


            if (-1 === $priority) {
                echo 'Change priority skipped', PHP_EOL;
            } else {
                if ($priority < 0) {
                    $priority = 0;
                } elseif (100 < $priority) {
                    $priority = 100;
                }

                if ($priority === $vendor->getPriority()) {
                    echo 'Change priority skipped', PHP_EOL;
                } else {
                    $vendor->setPriority($priority);

                    $hasChange = true;
                    echo 'Priority changed', PHP_EOL;
                }
            }


            if ($hasChange) {
                $vendor->setUpdatedAt(new DateTime());
                if (false === $vendor->save()) {
                    $this->throwExceptionOnFailedSave($vendor);
                }
            }


            echo json_encode($vendor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), PHP_EOL;

            return ExitCode::OK;
        } catch (Exception $exception) {
            echo $exception->getMessage(), PHP_EOL;

            return ExitCode::UNSPECIFIED_ERROR;
        }
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
