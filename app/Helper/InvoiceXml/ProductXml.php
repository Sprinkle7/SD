<?php

namespace App\Helper\InvoiceXml;

class ProductXml extends XmlObjectAbstract implements XmlObject
{

    public function generate($xml)
    {
        $child = $xml->tBestellung;
        $child = $child->addChild('twarenkorbpos');
        $child->addChild('cName', $this->info['product_title']);
        $child->addChild('cArtNr', 'Freifeld');
        $child->addChild('cBarcode');
        $child->addChild('cEinheit');
        $child->addChild('fPreisEinzelNetto', $this->info['net_price']);
        $child->addChild('fMwSt', $this->info['tax']);
        $child->addChild('fAnzahl', $this->info['quantity']);
        $child->addChild('cPosTyp');
        $child->addChild('fRabatt', $this->info['discount_price']);
        return $xml;
    }

    public function xmlObject()
    {

    }
}
