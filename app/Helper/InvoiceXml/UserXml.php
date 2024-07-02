<?php

namespace App\Helper\InvoiceXml;

class UserXml extends XmlObjectAbstract implements XmlObject
{
    public function generate($xml)
    {
        $child = $xml->tBestellung;

        $child = $child->addChild('tkunde');
        $child->addChild('cKundenNr',$this->info['id']);
        $child->addChild('cAnrede',($this->info['gender']=='male') ? 'Herr':'');
        $child->addChild('cTitel');
        $child->addChild('cVorname',$this->info['first_name']);
        $child->addChild('cNachname',$this->info['last_name']);
        $child->addChild('cFirma',$this->info['company']);
        $child->addChild('cStrasse',$this->info['address']);
        $child->addChild('cAdressZusatz',$this->info['additional_address']);
        $child->addChild('cPLZ',$this->info['postcode']);
        $child->addChild('cOrt',$this->info['city']);
        $child->addChild('cBundesland');
        $child->addChild('cLand',$this->info['country']);
        $child->addChild('cTel',$this->info['phone']);
        $child->addChild('cMobil');
        $child->addChild('cFax');
        $child->addChild('cMail',$this->info['email']);
        $child->addChild('cUSTID');
        $child->addChild('cWWW');
        $child->addChild('cHerkunft','Wawi');
        return $xml;
    }

    public function xmlObject()
    {

    }
}
