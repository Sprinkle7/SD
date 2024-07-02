<?php

namespace App\Helper\SystemMessage\Models\Invoice;

use App\Helper\SystemMessage\SystemMessage;

class InvoiceSystemMessage extends SystemMessage
{
    public function confirmed() {
        return $this->messages['confirmed'];
    }

    public function waitForImage() {
        return $this->messages['waitForImage'];
    }
}
