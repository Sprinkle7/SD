<?php


namespace App\Helper\SystemMessage\Models\Product;


use App\Helper\SystemMessage\SystemMessage;

class Pt2SystemMessage extends SystemMessage
{
    public function pt2AttachCategory() {
        return $this->messages['pt2AttachCategory'];
    }

    public function pt2Pt1Combination() {
        return $this->messages['pt2Pt1Combination'];
    }

    public function combinationUpdated()
    {
        return $this->messages['combinationUpdated'];
    }

    public function updateNoSelect() {
        return $this->messages['updateNoSelect'];

    }
    public function updateDefaultCategory() {
        return $this->messages['updateDefaultCategory'];

    }
    public function updateCombActivation() {
        return $this->messages['updateCombActivation'];
    }

    public function setDefaultCombination() {
        return $this->messages['setDefaultCombination'];
    }

    public function setDefaultCombCatNotMatchDefaultCat() {
        return $this->messages['setDefaultCombCatNotMatchDefaultCat'];
    }

    public function setDisabled() {
        return $this->messages['setDisabled'];
    }
}
