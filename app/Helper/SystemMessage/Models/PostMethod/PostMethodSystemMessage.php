<?php


namespace App\Helper\SystemMessage\Models\PostMethod;


use App\Helper\SystemMessage\SystemMessage;

class PostMethodSystemMessage extends SystemMessage
{

    public function unableToDelete()
    {
        return $this->entity . $this->messages['unableToDeletePostMethod'];

    }

}
