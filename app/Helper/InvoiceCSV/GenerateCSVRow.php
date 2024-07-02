<?php

namespace App\Helper\InvoiceCSV;

class GenerateCSVRow
{
    public $builder;

    public function __construct()
    {
        $this->builder = new CSVRowBuilder();
    }

    public function setInvoiceInfo($user, $invoice)
    {
        $salutation = $this->getSalutation($user);
        $this->builder->setOrderNumber($invoice['invoice_id']);
        $this->builder->setExternalOrderNumber($invoice['invoice_id']);
        $this->builder->setCustomerNumber($user['id']);
        $this->builder->setComment($invoice['comment']);
        $this->builder->setOrderDate($invoice['created_at']);
        $this->builder->setInvoiceSalutation($salutation);
        $this->builder->setInvoiceFirstName($user['first_name']);
        $this->builder->setInvoiceLastname($user['last_name']);
        $this->builder->setInvoiceCompany($user['company']);
        $this->builder->setInvoiceStreet($user['address']);
        $this->builder->setInvoiceStreetAddition($user['additional_address']);
        $this->builder->setInvoiceZipCode($user['postcode']);
        $this->builder->setInvoiceCity($user['city']);
        $this->builder->setInvoiceCountry($user['country_name']);
        $this->builder->setInvoiceEMail($user['email']);
        $this->builder->setInvoicePhoneNumber($user['phone']);
        $this->builder->setPaymentMethodName($invoice['payment_type']);
        $this->builder->setInvoiceVatNumber($invoice['ust_id']);
        $this->builder->setPaymentDate((($invoice['payment_type'] == 'paypal' || $invoice['payment_type'] == 'stripe') ? $invoice['created_at'] : ''));
        // $this->builder->setInvoiceVatNumber($invoice['invoice_id']); 
    }

    public function setShippingAddress($address, $invoiceAddress)
    {
        $salutation = $this->getSalutation($address);
        $this->builder->setDeliveryCompany($address['company']);
        $this->builder->setDeliverySalutation($salutation);
        $this->builder->setDeliveryFirstName($address['first_name']);
        $this->builder->setDeliveryLastName($address['last_name']);
        $this->builder->setDeliveryStreet($address['address']);
        $this->builder->setDeliveryStreetAddition($address['additional_address']);
        $this->builder->setDeliveryZipCode($address['postcode']);
        $this->builder->setDeliveryCity($address['city']);
        $this->builder->setDeliveryCountry($address['country_name']);
        $this->builder->setDeliveryEMail($address['email']);
        $this->builder->setDeliveryPhoneNumber($address['phone']);
        $this->builder->setShippingMethodeName($invoiceAddress['post']['title']);
    }

    public function setproduct($product, $invoiceProduct)
    {
        $this->builder->setArticleNumber($product['code']);
        $this->builder->setArticleName($invoiceProduct['product_title']);
        $this->builder->setArticlePriceNetto($invoiceProduct['net_price'] + $invoiceProduct['pre_paid_coupon_price']+ $invoiceProduct['services_total_price']);
        $this->builder->setArticlePriceVatInPercent($invoiceProduct['tax']);
        $this->builder->setArticlePriceDiscount($invoiceProduct['pre_paid_percent']);
        $this->builder->setArticleQuantity($invoiceProduct['quantity']);
        $this->builder->setPositionType('Artikel');
        $this->builder->setDiscountedArticlePrice($invoiceProduct['pre_paid_coupon_price']);
        $this->builder->setPaymentAmount($invoiceProduct['total_price']);
    }

    public function generateRow()
    {
        $row = $this->builder->getCSVRow();
        $rowData = [
            'OrderNumber'   => 'SB'.$row->getOrderNumber(),
            'ExternalOrderNumber'   => $row->getExternalOrderNumber(),
            'CustomerNumber'    => 'SK'.$row->getCustomerNumber(),
            'Comment'   => $row->getComment(),
            'OrderDate'     => date("d.m.Y h:i a", strtotime($row->getOrderDate())),
            'InvoiceSalutation'     => $row->getInvoiceSalutation(),
            'InvoiceTitle'  => $row->getInvoiceTitle(),
            'InvoiceFirstName'  => $row->getInvoiceFirstName(),
            'InvoiceLastname'   => $row->getInvoiceLastname(),
            'Invoice Company'   => $row->getInvoiceCompany(),
            'InvoiceCompanyAddition'    => $row->getInvoiceStreetAddition(),
            'InvoiceStreet'     => implode(' ',explode(',',$row->getInvoiceStreet())),
            'InvoiceStreetAddition'     =>  $row->getInvoiceStreetAddition(),
            'InvoiceZipCode'    => $row->getInvoiceZipCode(),
            'InvoiceCity'   => $row->getInvoiceCity(),
            'InvoiceCountry'    => $row->getInvoiceCountry(),
            'InvoiceEMail'  => $row->getInvoiceEMail(),
            'InvoicePhoneNumber'    => $row->getInvoicePhoneNumber(),
            'InvoiceVatNumber'  => $row->getInvoiceVatNumber(),
            'DeliveryCompany'   => $row->getDeliveryCompany(),
            'DeliverySalutation'    => $row->getDeliverySalutation(),
            'DeliveryTitle'     => $row->getDeliveryTitle(),
            'DeliveryFirstName'     =>  $row->getDeliveryFirstName(),
            'DeliveryLastName'  => $row->getDeliveryLastName(),
            'DeliveryStreet'    => implode(' ',explode(',',$row->getDeliveryStreet())),
            'DeliveryStreetAddition'    => implode(' ',explode(',',$row->getDeliveryStreetAddition())),
            'DeliveryZipCode'   => $row->getDeliveryZipCode(),
            'DeliveryCity'  => $row->getDeliveryCity(),
            'DeliveryCountry'   => $row->getDeliveryCountry(),
            'DeliveryEMail'     => $row->getDeliveryEMail(),
            'DeliveryPhoneNumber'   => $row->getDeliveryPhoneNumber(),
            'ArticleNumber'     => $row->getArticleNumber(),
            'ShippingMethodeName'   => $row->getShippingMethodeName(),
            'ArticleName'   => $row->getArticleName(),
            'ArticlePriceNetto'     => $row->getArticlePriceNetto(),
            'ArticlePriceVatInPercent'  => $row->getArticlePriceVatInPercent(),
            'ArticlePriceDiscount'  => $row->getArticlePriceDiscount(),
            'ArticleQuantity'   => $row->getArticleQuantity(),
            'PositionType'  => $row->getPositionType(),
            'PaymentMethodName'     => $row->getPaymentMethodName(),
            'PaymentAmount'     => $row->getPaymentAmount(),
            'PaymentDate'   =>  date("d.m.Y h:i:s", strtotime($row->getOrderDate())),
            'DiscountedArticlePrice'     => $row->getDiscountedArticlePrice(),
        ];
        return implode(',', $rowData) . "\n";
    }

    private function getSalutation($user)
    {
        return $user['gender'] == 'male' ? 'Herr' : 'Frau';

    }
}
