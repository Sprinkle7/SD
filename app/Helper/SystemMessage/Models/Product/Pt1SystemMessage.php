<?php


namespace App\Helper\SystemMessage\Models\Product;


use App\Helper\SystemMessage\SystemMessage;

class Pt1SystemMessage extends SystemMessage
{
    public function optionAttached() {
        return $this->messages['optionAttached'];
    }

    public function combinationUpdated() {
        return $this->messages['combinationUpdated'];
    }

    public function setDefaultCombination() {
        return $this->messages['setDefaultCombination'];
    }
}
