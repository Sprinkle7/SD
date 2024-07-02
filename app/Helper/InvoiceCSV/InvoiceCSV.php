<?php

namespace App\Helper\InvoiceCSV;

class InvoiceCSV
{
    private $invoice = [];
    private $cloumns = [
            'OrderNumber', 'ExternalOrderNumber', 'CustomerNumber', 'Comment', 'OrderDate', 'InvoiceSalutation', 'InvoiceTitle', 'InvoiceFirstName', 'InvoiceLastname', 'Invoice Company', 'InvoiceCompanyAddition', 'InvoiceStreet', 'InvoiceStreetAddition', 'InvoiceZipCode', 'InvoiceCity', 'InvoiceCountry', 'InvoiceEMail', 'InvoicePhoneNumber', 'InvoiceVatNumber', 'DeliveryCompany', 'DeliverySalutation', 'DeliveryTitle', 'DeliveryFirstName', 'DeliveryLastName', 'DeliveryStreet', 'DeliveryStreetAddition', 'DeliveryZipCode', 'DeliveryCity', 'DeliveryCountry', 'DeliveryEMail', 'DeliveryPhoneNumber', 'ArticleNumber', 'ShippingMethodeName', 'ArticleName', 'ArticlePriceNetto', 'ArticlePriceVatInPercent', 'ArticlePriceDiscount', 'ArticleQuantity', 'PositionType', 'PaymentMethodName', 'PaymentAmount', 'PaymentDate'
        ];
        // [
        //     'OrderNumber', 'ExternalOrderNumber', 'CustomerNumber', 'Comment', 'OrderDate', 'InvoiceSalutation', 'InvoiceTitle', 'InvoiceFirstName', 'InvoiceLastname', 'Invoice Company', 'InvoiceCompanyAddition', 'InvoiceStreet', 'InvoiceStreetAddition', 'InvoiceZipCode', 'InvoiceCity', 'InvoiceCountry', 'InvoiceEMail', 'InvoicePhoneNumber', 'InvoiceVatNumber', 'DeliveryCompany', 'DeliveryCompanyAddition', 'DeliverySalutation', 'DeliveryTitle', 'DeliveryFirstName', 'DeliveryLastName', 'DeliveryStreet', 'DeliveryStreetAddition', 'DeliveryZipCode', 'DeliveryCity', 'DeliveryCountry', 'DeliveryEMail', 'DeliveryPhoneNumber', 'ArticleNumber', 'ShippingMethodeName', 'ArticleName', 'ArticlePriceNetto', 'ArticlePriceVatInPercent', 'ArticlePriceDiscount', 'ArticleQuantity', 'PositionType', 'PaymentMethodName', 'PaymentAmount', 'PaymentDate'
        // ];
    public function __construct()
    {
        $this->invoice[] = $this->cloumns;
    }

    public function setInvoice(array $row)
    {
        $this->invoice[] = $row;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function generaetCSV($name)
    {
        $name = 'Order_'.$name . '.csv';
        $handle = fopen(storage_path('app/OrderCsv/') . $name, 'w');
        
        // $headings = [
        //     'OrderNumber', 'ExternalOrderNumber', 'CustomerNumber', 'Comment', 'OrderDate', 'InvoiceSalutation', 'InvoiceTitle', 'InvoiceFirstName', 'InvoiceLastname', 'Invoice Company', 'InvoiceCompanyAddition', 'InvoiceStreet', 'InvoiceStreetAddition', 'InvoiceZipCode', 'InvoiceCity', 'InvoiceCountry', 'InvoiceEMail', 'InvoicePhoneNumber', 'InvoiceVatNumber', 'DeliveryCompany', 'DeliveryCompanyAddition', 'DeliverySalutation', 'DeliveryTitle', 'DeliveryFirstName', 'DeliveryLastName', 'DeliveryStreet', 'DeliveryStreetAddition', 'DeliveryZipCode', 'DeliveryCity', 'DeliveryCountry', 'DeliveryEMail', 'DeliveryPhoneNumber', 'ArticleNumber', 'ShippingMethodeName', 'ArticleName', 'ArticlePriceNetto', 'ArticlePriceVatInPercent', 'ArticlePriceDiscount', 'ArticleQuantity', 'PositionType', 'PaymentMethodName', 'PaymentAmount', 'PaymentDate'
        // ];
        
        // // Write the headings to the file
        // fputcsv($handle, $headings, ',');
        
        foreach ($this->invoice as $fields) {
            $formattedRow = implode(',', array_map(function($value) {
                return utf8_decode($value);
            }, $fields));
            
            fwrite($handle, $formattedRow . PHP_EOL); // Write the formatted row to the file
        }
        
        fclose($handle);
        return $name;

        // $name = $name . '.csv';
        // $name = uniqid() . time() . '.csv';
        // $handle = fopen(storage_path('app/OrderCsv/') . $name, 'w');
        // foreach ($this->invoice as $fields) {
        //     $array = array_map(function($value) {
        //         return utf8_decode($value);
        //     }, $fields);
        //     fputcsv($handle, $array);
        // }
        // fclose($handle);
        // return $name;
    }
}
