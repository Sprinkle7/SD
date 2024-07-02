<?php


namespace App\Helper\SystemMessage\Models\Cart;


use App\Helper\SystemMessage\SystemMessage;

class CartSystemMessage extends SystemMessage
{
    public function addToCart() {
        return $this->messages['addToCart'];
    }

    public function removeFromCart() {
        return $this->messages['removeFromCart'];
    }

    public function CartIsEmpty() {

    }

    public function notEnoughStock() {
        return $this->messages['notEnoughStock'];
    }

    public function enoughStock() {
        return $this->messages['enoughStock'];
    }
}
