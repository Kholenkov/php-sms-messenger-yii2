<?php

declare(strict_types=1);

namespace app\models\Message;

use yii\base\Model;

class SendingForm extends Model
{
    public string $phoneNumber = '';
    public string $text = '';

    public function formName(): string
    {
        return '';
    }

    public function rules(): array
    {
        return [
            [['phoneNumber', 'text'], 'required'],

            ['phoneNumber', 'match', 'pattern' => '/^\+7[0-9]{10}$/'],
            ['text', 'string', 'length' => [1, 255]],
        ];
    }
}
