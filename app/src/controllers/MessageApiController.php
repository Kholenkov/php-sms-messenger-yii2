<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Message;
use app\models\Message\SendingForm;
use app\services\Messenger;
use Exception;
use Ramsey\Uuid\Uuid;
use Yii;

class MessageApiController extends BaseApiController
{
    public function actionGetStatus(string $uuid): array
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception('Invalid UUID');
        }

        $message = Message::find()
            ->where(['uuid' => $uuid])
            ->one();

        if (!($message instanceof Message)) {
            throw new Exception('Message not found');
        }

        /** @var Messenger $messenger */
        $messenger = Yii::$container->get(Messenger::class);
        $messenger->getMessageStatus($message);

        return $this->responseSuccess($message->jsonSerialize());
    }

    public function actionSend(): array
    {
        $form = new SendingForm();
        $form->load($this->getRequestBodyParameters());

        if (!$form->validate()) {
            $errorMessage = 'Unknown error';
            foreach ($form->getFirstErrors() as $errorMessage) {
                break;
            }

            throw new Exception($errorMessage);
        }

        /** @var Messenger $messenger */
        $messenger = Yii::$container->get(Messenger::class);
        $message = $messenger->sendMessage($form->phoneNumber, $form->text);

        return $this->responseSuccess($message->jsonSerialize());
    }
}
