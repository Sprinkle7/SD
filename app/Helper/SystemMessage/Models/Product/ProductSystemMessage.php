<?php


namespace App\Helper\SystemMessage\Models\Product;


use App\Helper\SystemMessage\SystemMessage;

class ProductSystemMessage extends SystemMessage
{
    public function updateWorkingDay()
    {
        return $this->messages['updateWorkingDay'];
    }

    public function attachTechnicalInfo()
    {
        return $this->messages['attachTechnicalInfo'];
    }

    public function attachPortfolio()
    {
        return $this->messages['attachPortfolio'];
    }

    public function attachDiscount() {
        return $this->messages['attachDiscount'];
    }

    public function updateDiscount() {
        return $this->messages['updateDiscount'];
    }

    public function deleteDiscount() {
        return $this->messages['deleteDiscount'];
    }

    public function menuRequired() {
        return $this->messages['menuRequired'];
    }

    public function translationsRequired() {
        return $this->messages['translationsRequired'];
    }

    public function optionAttached() {
        return $this->messages['optionAttached'];
    }
    public function optionValueAttached() {
        return $this->messages['optionValueAttached'];
    }
    public function optionTranslationAttached() {
        return $this->messages['optionTranslationAttached'];
    }
    public function excludeServiceValue() {
        return $this->messages['excludeServiceValue'];
    }
}
