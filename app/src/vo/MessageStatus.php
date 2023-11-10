<?php

declare(strict_types=1);

namespace app\vo;

enum MessageStatus: string
{
    case Undefined = 'undefined';
    case SuccessSend = 'success_send';
    case SuccessDelivery = 'success_delivery';
    case ErrorSend = 'error_send';
    case ErrorDelivery = 'error_delivery';
}
