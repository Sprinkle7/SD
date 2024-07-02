<?php

namespace App\Helper\InvoiceXml;

class XmlObjectAbstract
{
    protected $info;

    public function __construct($info = [])
    {
        $this->info = $info;
    }

    public function setObject($info) {
        $this->info = $info;
    }

    protected function xmlObject() {

    }
}
