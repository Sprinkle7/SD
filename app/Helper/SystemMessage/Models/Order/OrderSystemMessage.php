<?php


namespace App\Helper\SystemMessage\Models\Order;


use App\Helper\SystemMessage\SystemMessage;

class OrderSystemMessage extends SystemMessage
{

    public function orderCanceled() {
        return $this->messages['orderCanceled'];
    }
}
