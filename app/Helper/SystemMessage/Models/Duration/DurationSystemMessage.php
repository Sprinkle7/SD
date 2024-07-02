<?php


namespace App\Helper\SystemMessage\Models\Duration;


use App\Helper\SystemMessage\SystemMessage;

class DurationSystemMessage extends SystemMessage
{

    public function unableToDelete()
    {
        return $this->entity . $this->messages['unableToDeleteDuration'];

    }

}
