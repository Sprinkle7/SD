<?php


namespace App\Helper\SystemMessage\Models\Menu;


use App\Helper\SystemMessage\SystemMessage;

class MenuSystemMessage extends SystemMessage
{
    public function wrongParentMenu()
    {
        return $this->messages['wrongParentMenu'];
    }

    public function projectAttached()
    {
        return $this->messages['menuProject'];
    }

}
