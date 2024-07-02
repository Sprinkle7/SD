<?php

namespace App\Helper\InvoiceXml;

interface XmlObject
{
    public function generate(\SimpleXMLElement $xml);
}
