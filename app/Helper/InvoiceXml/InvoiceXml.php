<?php

namespace App\Helper\InvoiceXml;

class InvoiceXml extends XmlObjectAbstract implements XmlObject
{

    public function generate($xml)
    {
        $child = $xml->addChild('tBestellung');
        $child->addAttribute('kFirma', 3);
        $child->addAttribute('kBenutzer', 33);
        $child->addChild('cSprache', 'ger');
        $child->addChild('cWaehrung', 'EUR');
        $child->addChild('cBestellNr', $this->info['id']);
        $child->addChild('cExterneBestellNr', $this->info['id']);
        $child->addChild('cVersandartName', 'Standard');
        $child->addChild('cVersandInfo');
        $child->addChild('dVersandDatum', $this->info['shipping_date']);// shipping date
        $child->addChild('cTracking');
        $child->addChild('dLieferDatum');
        $child->addChild('cKommentar', 'Bestandskunde');
        $child->addChild('dErstellt', now());// buying date
        $child->addChild('cZahlungsartName', $this->info['payment_type']);
        $child->addChild('dBezahltDatum');
        $child->addChild('nZahlungsziel', 8);
        return $xml;
    }

    public function updateShippingDate(\SimpleXMLElement $xml)
    {
        $child = $xml->tBestellung;
        $child->dVersandDatum = $this->info['shipping_date'];
        return $xml;
    }

    public function xmlObject()
    {
        return <<<XML
<?xml version="1.0" encoding="ISO-8859-1"?>
<tBestellungen>
	<tBestellung kFirma="3" kBenutzer="33">
		<cSprache>ger</cSprache>
		<cWaehrung>EUR</cWaehrung>
		<cBestellNr>36066</cBestellNr> addad factore
		<cExterneBestellNr>36066</cExterneBestellNr> same
		<cVersandartName>Standard</cVersandartName> post - sabet
        <cVersandInfo/>
		<dVersandDatum>2023-05-12</dVersandDatum> tarikh ersal
		<cTracking/>
		<dLieferDatum/>
		<cKommentar>Bestandskunde</cKommentar> sabet
		<cBemerkung/>
		<dErstellt>2023-04-24</dErstellt> tarikh kharid
		<cZahlungsartName>Rechnungskauf</cZahlungsartName> noe pardakht
		<dBezahltDatum/>
		<nZahlungsziel>8</nZahlungsziel>  sabet

	</tBestellung>
</tBestellungen>
XML;
    }
}
