<?php

namespace App\Helper\InvoiceCSV;

class CSVRow
{
    private $OrderNumber;
    private $ExternalOrderNumber;
    private $CustomerNumber;
    private $Comment;
    private $OrderDate;
    private $InvoiceSalutation;
    private $InvoiceTitle;
    private $InvoiceFirstName;
    private $InvoiceLastname;
    private $InvoiceCompany;
    private $InvoiceStreet;
    private $InvoiceStreetAddition;
    private $InvoiceZipCode;
    private $InvoiceCity;
    private $InvoiceCountry;
    private $InvoiceEMail;
    private $InvoicePhoneNumber;
    private $InvoiceVatNumber;
    private $DeliveryCompany;
    private $DeliverySalutation;
    private $DeliveryTitle;
    private $DeliveryFirstName;
    private $DeliveryLastName;
    private $DeliveryStreet;
    private $DeliveryStreetAddition;
    private $DeliveryZipCode;
    private $DeliveryCity;
    private $DeliveryCountry;
    private $DeliveryEMail;
    private $DeliveryPhoneNumber;
    private $ArticleNumber;
    private $ShippingMethodeName;
    private $ArticleName;
    private $ArticlePriceNetto;
    private $ArticlePriceVatInPercent;
    private $ArticlePriceDiscount;
    private $ArticleQuantity;
    private $PositionType;
    private $PaymentMethodName;
    private $PaymentAmount;
    private $PaymentDate;
    private $DiscountedArticlePrice;

    /**
     * @param mixed $OrderNumber
     */
    public function setOrderNumber($OrderNumber): void
    {
        $this->OrderNumber = $OrderNumber;
    }

    /**
     * @param mixed $ExternalOrderNumber
     */
    public function setExternalOrderNumber($ExternalOrderNumber): void
    {
        $this->ExternalOrderNumber = $ExternalOrderNumber;
    }

    /**
     * @param mixed $CustomerNumber
     */
    public function setCustomerNumber($CustomerNumber): void
    {
        $this->CustomerNumber = $CustomerNumber;
    }

    /**
     * @param mixed $Comment
     */
    public function setComment($Comment): void
    {
        $this->Comment = $Comment;
    }

    /**
     * @param mixed $OrderDate
     */
    public function setOrderDate($OrderDate): void
    {
        $this->OrderDate = $OrderDate;
    }

    /**
     * @param mixed $InvoiceSalutation
     */
    public function setInvoiceSalutation($InvoiceSalutation): void
    {
        $this->InvoiceSalutation = $InvoiceSalutation;
    }

    /**
     * @param mixed $InvoiceTitle
     */
    public function setInvoiceTitle($InvoiceTitle): void
    {
        $this->InvoiceTitle = $InvoiceTitle;
    }

    /**
     * @param mixed $InvoiceFirstName
     */
    public function setInvoiceFirstName($InvoiceFirstName): void
    {
        $this->InvoiceFirstName = $InvoiceFirstName;
    }

    /**
     * @param mixed $InvoiceLastname
     */
    public function setInvoiceLastname($InvoiceLastname): void
    {
        $this->InvoiceLastname = $InvoiceLastname;
    }

    /**
     * @param mixed $InvoiceCompany
     */
    public function setInvoiceCompany($InvoiceCompany): void
    {
        $this->InvoiceCompany = $InvoiceCompany;
    }

    /**
     * @param mixed $InvoiceStreet
     */
    public function setInvoiceStreet($InvoiceStreet): void
    {
        $this->InvoiceStreet = $InvoiceStreet;
    }

    /**
     * @param mixed $InvoiceStreetAddition
     */
    public function setInvoiceStreetAddition($InvoiceStreetAddition): void
    {
        $this->InvoiceStreetAddition = $InvoiceStreetAddition;
    }

    /**
     * @param mixed $InvoiceZipCode
     */
    public function setInvoiceZipCode($InvoiceZipCode): void
    {
        $this->InvoiceZipCode = $InvoiceZipCode;
    }

    /**
     * @param mixed $InvoiceCity
     */
    public function setInvoiceCity($InvoiceCity): void
    {
        $this->InvoiceCity = $InvoiceCity;
    }

    /**
     * @param mixed $InvoiceCountry
     */
    public function setInvoiceCountry($InvoiceCountry): void
    {
        $this->InvoiceCountry = $InvoiceCountry;
    }

    /**
     * @param mixed $InvoiceEMail
     */
    public function setInvoiceEMail($InvoiceEMail): void
    {
        $this->InvoiceEMail = $InvoiceEMail;
    }

    /**
     * @param mixed $InvoicePhoneNumber
     */
    public function setInvoicePhoneNumber($InvoicePhoneNumber): void
    {
        $this->InvoicePhoneNumber = $InvoicePhoneNumber;
    }

    /**
     * @param mixed $InvoiceVatNumber
     */
    public function setInvoiceVatNumber($InvoiceVatNumber): void
    {
        $this->InvoiceVatNumber = $InvoiceVatNumber;
    }

    /**
     * @param mixed $DeliveryCompany
     */
    public function setDeliveryCompany($DeliveryCompany): void
    {
        $this->DeliveryCompany = $DeliveryCompany;
    }

    /**
     * @param mixed $DeliverySalutation
     */
    public function setDeliverySalutation($DeliverySalutation): void
    {
        $this->DeliverySalutation = $DeliverySalutation;
    }

    /**
     * @param mixed $DeliveryTitle
     */
    public function setDeliveryTitle($DeliveryTitle): void
    {
        $this->DeliveryTitle = $DeliveryTitle;
    }

    /**
     * @param mixed $DeliveryFirstName
     */
    public function setDeliveryFirstName($DeliveryFirstName): void
    {
        $this->DeliveryFirstName = $DeliveryFirstName;
    }

    /**
     * @param mixed $DeliveryLastName
     */
    public function setDeliveryLastName($DeliveryLastName): void
    {
        $this->DeliveryLastName = $DeliveryLastName;
    }

    /**
     * @param mixed $DeliveryStreet
     */
    public function setDeliveryStreet($DeliveryStreet): void
    {
        $this->DeliveryStreet = $DeliveryStreet;
    }

    /**
     * @param mixed $DeliveryStreetAddition
     */
    public function setDeliveryStreetAddition($DeliveryStreetAddition): void
    {
        $this->DeliveryStreetAddition = $DeliveryStreetAddition;
    }

    /**
     * @param mixed $DeliveryZipCode
     */
    public function setDeliveryZipCode($DeliveryZipCode): void
    {
        $this->DeliveryZipCode = $DeliveryZipCode;
    }

    /**
     * @param mixed $DeliveryCity
     */
    public function setDeliveryCity($DeliveryCity): void
    {
        $this->DeliveryCity = $DeliveryCity;
    }

    /**
     * @param mixed $DeliveryCountry
     */
    public function setDeliveryCountry($DeliveryCountry): void
    {
        $this->DeliveryCountry = $DeliveryCountry;
    }

    /**
     * @param mixed $DeliveryEMail
     */
    public function setDeliveryEMail($DeliveryEMail): void
    {
        $this->DeliveryEMail = $DeliveryEMail;
    }

    /**
     * @param mixed $DeliveryPhoneNumber
     */
    public function setDeliveryPhoneNumber($DeliveryPhoneNumber): void
    {
        $this->DeliveryPhoneNumber = $DeliveryPhoneNumber;
    }

    /**
     * @param mixed $ArticleNumber
     */
    public function setArticleNumber($ArticleNumber): void
    {
        $this->ArticleNumber = $ArticleNumber;
    }

    /**
     * @param mixed $ShippingMethodeName
     */
    public function setShippingMethodeName($ShippingMethodeName): void
    {
        $this->ShippingMethodeName = $ShippingMethodeName;
    }

    /**
     * @param mixed $ArticleName
     */
    public function setArticleName($ArticleName): void
    {
        $this->ArticleName = $ArticleName;
    }

    /**
     * @param mixed $ArticlePriceNetto
     */
    public function setArticlePriceNetto($ArticlePriceNetto): void
    {
        $this->ArticlePriceNetto = $ArticlePriceNetto;
    }

    /**
     * @param mixed $ArticlePriceVatInPercent
     */
    public function setArticlePriceVatInPercent($ArticlePriceVatInPercent): void
    {
        $this->ArticlePriceVatInPercent = $ArticlePriceVatInPercent;
    }

    /**
     * @param mixed $ArticlePriceDiscount
     */
    public function setArticlePriceDiscount($ArticlePriceDiscount): void
    {
        $this->ArticlePriceDiscount = $ArticlePriceDiscount;
    }

     /**
     * @param mixed $DiscountedArticlePrice
     */
    public function setDiscountedArticlePrice($DiscountedArticlePrice): void
    {
        $this->DiscountedArticlePrice = $DiscountedArticlePrice;
    }
    
    /**
     * @param mixed $ArticleQuantity
     */
    public function setArticleQuantity($ArticleQuantity): void
    {
        $this->ArticleQuantity = $ArticleQuantity;
    }

    /**
     * @param mixed $PositionType
     */
    public function setPositionType($PositionType): void
    {
        $this->PositionType = $PositionType;
    }

    /**
     * @param mixed $PaymentMethodName
     */
    public function setPaymentMethodName($PaymentMethodName): void
    {
        $this->PaymentMethodName = $PaymentMethodName;
    }

    /**
     * @param mixed $PaymentAmount
     */
    public function setPaymentAmount($PaymentAmount): void
    {
        $this->PaymentAmount = $PaymentAmount;
    }

    /**
     * @param mixed $PaymentDate
     */
    public function setPaymentDate($PaymentDate): void
    {
        $this->PaymentDate = $PaymentDate;
    }

    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->OrderNumber;
    }

    /**
     * @return mixed
     */
    public function getExternalOrderNumber()
    {
        return $this->ExternalOrderNumber;
    }

    /**
     * @return mixed
     */
    public function getCustomerNumber()
    {
        return $this->CustomerNumber;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->Comment;
    }

    /**
     * @return mixed
     */
    public function getOrderDate()
    {
        return $this->OrderDate;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSalutation()
    {
        return $this->InvoiceSalutation;
    }

    /**
     * @return mixed
     */
    public function getInvoiceTitle()
    {
        return $this->InvoiceTitle;
    }

    /**
     * @return mixed
     */
    public function getInvoiceFirstName()
    {
        return $this->InvoiceFirstName;
    }

    /**
     * @return mixed
     */
    public function getInvoiceLastname()
    {
        return $this->InvoiceLastname;
    }

    /**
     * @return mixed
     */
    public function getInvoiceCompany()
    {
        return $this->InvoiceCompany;
    }

    /**
     * @return mixed
     */
    public function getInvoiceStreet()
    {
        return $this->InvoiceStreet;
    }

    /**
     * @return mixed
     */
    public function getInvoiceStreetAddition()
    {
        return $this->InvoiceStreetAddition;
    }

    /**
     * @return mixed
     */
    public function getInvoiceZipCode()
    {
        return $this->InvoiceZipCode;
    }

    /**
     * @return mixed
     */
    public function getInvoiceCity()
    {
        return $this->InvoiceCity;
    }

    /**
     * @return mixed
     */
    public function getInvoiceCountry()
    {
        return $this->InvoiceCountry;
    }

    /**
     * @return mixed
     */
    public function getInvoiceEMail()
    {
        return $this->InvoiceEMail;
    }

    /**
     * @return mixed
     */
    public function getInvoicePhoneNumber()
    {
        return $this->InvoicePhoneNumber;
    }

    /**
     * @return mixed
     */
    public function getInvoiceVatNumber()
    {
        return $this->InvoiceVatNumber;
    }

    /**
     * @return mixed
     */
    public function getDeliveryCompany()
    {
        return $this->DeliveryCompany;
    }

    /**
     * @return mixed
     */
    public function getDeliverySalutation()
    {
        return $this->DeliverySalutation;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTitle()
    {
        return $this->DeliveryTitle;
    }

    /**
     * @return mixed
     */
    public function getDeliveryFirstName()
    {
        return $this->DeliveryFirstName;
    }

    /**
     * @return mixed
     */
    public function getDeliveryLastName()
    {
        return $this->DeliveryLastName;
    }

    /**
     * @return mixed
     */
    public function getDeliveryStreet()
    {
        return $this->DeliveryStreet;
    }

    /**
     * @return mixed
     */
    public function getDeliveryStreetAddition()
    {
        return $this->DeliveryStreetAddition;
    }

    /**
     * @return mixed
     */
    public function getDeliveryZipCode()
    {
        return $this->DeliveryZipCode;
    }

    /**
     * @return mixed
     */
    public function getDeliveryCity()
    {
        return $this->DeliveryCity;
    }

    /**
     * @return mixed
     */
    public function getDeliveryCountry()
    {
        return $this->DeliveryCountry;
    }

    /**
     * @return mixed
     */
    public function getDeliveryEMail()
    {
        return $this->DeliveryEMail;
    }

    /**
     * @return mixed
     */
    public function getDeliveryPhoneNumber()
    {
        return $this->DeliveryPhoneNumber;
    }

    /**
     * @return mixed
     */
    public function getArticleNumber()
    {
        return $this->ArticleNumber;
    }

    /**
     * @return mixed
     */
    public function getShippingMethodeName()
    {
        return $this->ShippingMethodeName;
    }

    /**
     * @return mixed
     */
    public function getArticleName()
    {
        return $this->ArticleName;
    }

    /**
     * @return mixed
     */
    public function getArticlePriceNetto()
    {
        return $this->ArticlePriceNetto;
    }

    /**
     * @return mixed
     */
    public function getArticlePriceVatInPercent()
    {
        return $this->ArticlePriceVatInPercent;
    }

    /**
     * @return mixed
     */
    public function getArticlePriceDiscount()
    {
        return $this->ArticlePriceDiscount;
    }

    /**
     * @return mixed
     */
    public function getArticleQuantity()
    {
        return $this->ArticleQuantity;
    }

    /**
     * @return mixed
     */
    public function getPositionType()
    {
        return $this->PositionType;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodName()
    {
        return $this->PaymentMethodName;
    }


    public function getDiscountedArticlePrice()
    {
        return $this->DiscountedArticlePrice;
    }

    /**
     * @return mixed
     */
    public function getPaymentAmount()
    {
        return $this->PaymentAmount;
    }

    /**
     * @return mixed
     */
    public function getPaymentDate()
    {
        return $this->PaymentDate;
    }



}
