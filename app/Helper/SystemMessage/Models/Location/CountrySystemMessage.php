<?php


namespace App\Helper\SystemMessage\Models\Location;


use App\Helper\SystemMessage\SystemMessage;

class CountrySystemMessage extends SystemMessage
{
    public function attachPostMethod()
    {
        return $this->messages['attachPostMethod'];
    }

    public function unableToDelete() {
        return $this->entity . $this->messages['unableToDeleteCountry'];
    }

}
