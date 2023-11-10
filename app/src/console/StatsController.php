<?php

declare(strict_types=1);

namespace app\console;

use app\models\Message;
use app\models\Vendor;
use Exception;
use Yii;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Query;

class StatsController extends BaseController
{
    public function actionIndex(): int
    {
        try {
            /** @var Connection $db */
            $db = Yii::$container->get(Connection::class);

            $messageTableName = Message::tableName();
            $vendorTableName = Vendor::tableName();

            $rows = (new Query())
                ->select([
                    'm.vendorUuid as vendor_uuid',
                    'v.type as vendor_type',
                    'm.status as status',
                    'COUNT(*) as qnt',
                ])
                ->from("{$messageTableName} m")
                ->leftJoin("{$vendorTableName} v", 'm.vendorUuid = v.uuid')
                ->groupBy(['m.vendorUuid', 'm.status'])
                ->orderBy([
                    'v.type' => SORT_ASC,
                    'm.status' => SORT_ASC,
                ])
                ->all($db);

            $currentVendorType = null;
            foreach ($rows as $row) {
                if ($currentVendorType !== $row['vendor_type']) {
                    if (null !== $currentVendorType) {
                        echo PHP_EOL;
                    }
                    echo $row['vendor_type'], ' (uuid = ', $row['vendor_uuid'], ')', ':', PHP_EOL;
                    $currentVendorType = $row['vendor_type'];
                }
                echo $row['status'], ' = ', $row['qnt'], PHP_EOL;
            }

            return ExitCode::OK;
        } catch (Exception $exception) {
            echo $exception->getMessage(), PHP_EOL;

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
