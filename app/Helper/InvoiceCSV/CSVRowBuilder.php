<?php

namespace App\Helper\InvoiceCSV;

class CSVRowBuilder
{
    private $CSVRow;

    public function __construct()
    {
        $this->CSVRow = new CSVRow();
    }

    /**
     * @param mixed $OrderNumber
     */
    public function setOrderNumber($OrderNumber): void
    {
        $this->CSVRow->setOrderNumber($OrderNumber);
    }

    /**
     * @param mixed $ExternalOrderNumber
     */
    public function setExternalOrderNumber($ExternalOrderNumber): void
    {
        $this->CSVRow->setExternalOrderNumber($ExternalOrderNumber);
    }

    /**
     * @param mixed $CustomerNumber
     */
    public function setCustomerNumber($CustomerNumber): void
    {
        $this->CSVRow->setCustomerNumber($CustomerNumber);
    }

    /**
     * @param mixed $Comment
     */
    public function setComment($Comment): void
    {
        $this->CSVRow->setComment($Comment);
    }

    /**
     * @param mixed $OrderDate
     */
    public function setOrderDate($OrderDate): void
    {
        $this->CSVRow->setOrderDate($OrderDate);
    }

    /**
     * @param mixed $InvoiceSalutation
     */
    public function setInvoiceSalutation($InvoiceSalutation): void
    {
        $this->CSVRow->setInvoiceSalutation($InvoiceSalutation);
    }

    /**
     * @param mixed $InvoiceTitle
     */
    public function setInvoiceTitle($InvoiceTitle): void
    {
        $this->CSVRow->setInvoiceTitle($InvoiceTitle);
    }

    /**
     * @param mixed $InvoiceFirstName
     */
    public function setInvoiceFirstName($InvoiceFirstName): void
    {
        $this->CSVRow->setInvoiceFirstName($InvoiceFirstName);
    }

    /**
     * @param mixed $InvoiceLastname
     */
    public function setInvoiceLastname($InvoiceLastname): void
    {
        $this->CSVRow->setInvoiceLastname($InvoiceLastname);
    }

    /**
     * @param mixed $InvoiceCompany
     */
    public function setInvoiceCompany($InvoiceCompany): void
    {
        $this->CSVRow->setInvoiceCompany($InvoiceCompany);
    }

    /**
     * @param mixed $InvoiceStreet
     */
    public function setInvoiceStreet($InvoiceStreet): void
    {
        $this->CSVRow->setInvoiceStreet($InvoiceStreet);
    }

    /**
     * @param mixed $InvoiceStreetAddition
     */
    public function setInvoiceStreetAddition($InvoiceStreetAddition): void
    {
        $this->CSVRow->setInvoiceStreetAddition($InvoiceStreetAddition);
    }

    /**
     * @param mixed $InvoiceZipCode
     */
    public function setInvoiceZipCode($InvoiceZipCode): void
    {
        $this->CSVRow->setInvoiceZipCode($InvoiceZipCode);
    }

    /**
     * @param mixed $InvoiceCity
     */
    public function setInvoiceCity($InvoiceCity): void
    {
        $this->CSVRow->setInvoiceCity($InvoiceCity);
    }

    /**
     * @param mixed $InvoiceCountry
     */
    public function setInvoiceCountry($InvoiceCountry): void
    {
        $this->CSVRow->setInvoiceCountry($InvoiceCountry);
    }

    /**
     * @param mixed $InvoiceEMail
     */
    public function setInvoiceEMail($InvoiceEMail): void
    {
        $this->CSVRow->setInvoiceEMail($InvoiceEMail);
    }

    /**
     * @param mixed $InvoicePhoneNumber
     */
    public function setInvoicePhoneNumber($InvoicePhoneNumber): void
    {
        $this->CSVRow->setInvoicePhoneNumber($InvoicePhoneNumber);
    }

    /**
     * @param mixed $InvoiceVatNumber
     */
    public function setInvoiceVatNumber($InvoiceVatNumber): void
    {
        $this->CSVRow->setInvoiceVatNumber($InvoiceVatNumber);
    }

    /**
     * @param mixed $DeliveryCompany
     */
    public function setDeliveryCompany($DeliveryCompany): void
    {
        $this->CSVRow->setDeliveryCompany($DeliveryCompany);
    }

    /**
     * @param mixed $DeliverySalutation
     */
    public function setDeliverySalutation($DeliverySalutation): void
    {
        $this->CSVRow->setDeliverySalutation($DeliverySalutation);
    }

    /**
     * @param mixed $DeliveryTitle
     */
    public function setDeliveryTitle($DeliveryTitle): void
    {
        $this->CSVRow->setDeliveryTitle($DeliveryTitle);
    }

    /**
     * @param mixed $DeliveryFirstName
     */
    public function setDeliveryFirstName($DeliveryFirstName): void
    {
        $this->CSVRow->setDeliveryFirstName($DeliveryFirstName);
    }

    /**
     * @param mixed $DeliveryLastName
     */
    public function setDeliveryLastName($DeliveryLastName): void
    {
        $this->CSVRow->setDeliveryLastName($DeliveryLastName);
    }

    /**
     * @param mixed $DeliveryStreet
     */
    public function setDeliveryStreet($DeliveryStreet): void
    {
        $this->CSVRow->setDeliveryStreet($DeliveryStreet);
    }

    /**
     * @param mixed $DeliveryStreetAddition
     */
    public function setDeliveryStreetAddition($DeliveryStreetAddition): void
    {
        $this->CSVRow->setDeliveryStreetAddition($DeliveryStreetAddition);
    }

    /**
     * @param mixed $DeliveryZipCode
     */
    public function setDeliveryZipCode($DeliveryZipCode): void
    {
        $this->CSVRow->setDeliveryZipCode($DeliveryZipCode);
    }

    /**
     * @param mixed $DeliveryCity
     */
    public function setDeliveryCity($DeliveryCity): void
    {
        $this->CSVRow->setDeliveryCity($DeliveryCity);
    }

    /**
     * @param mixed $DeliveryCountry
     */
    public function setDeliveryCountry($DeliveryCountry): void
    {
        $this->CSVRow->setDeliveryCountry($DeliveryCountry);
    }

    /**
     * @param mixed $DeliveryEMail
     */
    public function setDeliveryEMail($DeliveryEMail): void
    {
        $this->CSVRow->setDeliveryEMail($DeliveryEMail);
    }

    /**
     * @param mixed $DeliveryPhoneNumber
     */
    public function setDeliveryPhoneNumber($DeliveryPhoneNumber): void
    {
        $this->CSVRow->setDeliveryPhoneNumber($DeliveryPhoneNumber);
    }

    /**
     * @param mixed $ArticleNumber
     */
    public function setArticleNumber($ArticleNumber): void
    {
        $this->CSVRow->setArticleNumber($ArticleNumber);
    }

    /**
     * @param mixed $ShippingMethodeName
     */
    public function setShippingMethodeName($ShippingMethodeName): void
    {
        $this->CSVRow->setShippingMethodeName($ShippingMethodeName);
    }

    /**
     * @param mixed $ArticleName
     */
    public function setArticleName($ArticleName): void
    {
        $this->CSVRow->setArticleName($ArticleName);
    }

    /**
     * @param mixed $ArticlePriceNetto
     */
    public function setArticlePriceNetto($ArticlePriceNetto): void
    {
        $this->CSVRow->setArticlePriceNetto($ArticlePriceNetto);
    }

    /**
     * @param mixed $ArticlePriceVatInPercent
     */
    public function setArticlePriceVatInPercent($ArticlePriceVatInPercent): void
    {
        $this->CSVRow->setArticlePriceVatInPercent($ArticlePriceVatInPercent);
    }

    /**
     * @param mixed $ArticlePriceDiscount
     */
    public function setArticlePriceDiscount($ArticlePriceDiscount): void
    {
        $this->CSVRow->setArticlePriceDiscount($ArticlePriceDiscount);
    }

    /**
     * @param mixed $DiscountedArticlePrice
     */
    public function setDiscountedArticlePrice($DiscountedArticlePrice): void
    {
        $this->CSVRow->setDiscountedArticlePrice($DiscountedArticlePrice);
    }

    /**
     * @param mixed $ArticleQuantity
     */
    public function setArticleQuantity($ArticleQuantity): void
    {
        $this->CSVRow->setArticleQuantity($ArticleQuantity);
    }

    /**
     * @param mixed $PositionType
     */
    public function setPositionType($PositionType): void
    {
        $this->CSVRow->setPositionType($PositionType);
    }

    /**
     * @param mixed $PaymentMethodName
     */
    public function setPaymentMethodName($PaymentMethodName): void
    {
        $this->CSVRow->setPaymentMethodName($PaymentMethodName);
    }

    /**
     * @param mixed $PaymentAmount
     */
    public function setPaymentAmount($PaymentAmount): void
    {
        $this->CSVRow->setPaymentAmount($PaymentAmount);
    }

    /**
     * @param mixed $PaymentDate
     */
    public function setPaymentDate($PaymentDate): void
    {
        $this->CSVRow->setPaymentDate($PaymentDate);
    }

    public function getCSVRow() {
        return $this->CSVRow;
    }
}
