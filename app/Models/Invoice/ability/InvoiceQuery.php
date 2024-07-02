<?php


namespace App\Models\Invoice\ability;


use App\Helper\Database\QueryHelper;

trait InvoiceQuery
{
    public static function fetchInvoiceAddressProducts($language, $orderAddressId)
    {
        $query = 'SELECT iap.invoice_address_product_id,iap.order_address_id,iap.payment_intent,iap.user_id,iap.tax,iap.product_id,' .
            'iap.combination_id,iap.type,iap.price,iap.additional_price,iap.duration_id,dt.title as duration_title,iap.duration_price,' .
            'iap.quantity,iap.total,iap.number_of_images,iap.is_available FROM invoice_address_products iap ' .
            'JOIN invoice_address_product_translations iapt ON ' .
            'iap.product_id=iapt.product_id AND iap.combination_id=iapt.combination_id AND iapt.language=\'' .
            $language . '\' JOIN duration_translations dt ON iap.duration_id=dt.duration_id AND dt.language=\'' .
            $language . '\' WHERE iap.order_address_id=\'' . $orderAddressId . '\'';
        return QueryHelper::select($query);
    }

    public static function fetchUserInvoiceAddressProducts($language, $orderAddressId, $userId)
    {
        $query = 'SELECT iap.invoice_address_product_id,iap.order_address_id,iap.payment_intent,iap.user_id,iap.tax,iap.product_id,' .
            'iap.combination_id,iap.type,iap.price,iap.additional_price,iap.duration_id,dt.title as duration_title,iap.duration_price,' .
            'iap.quantity,iap.total,iap.number_of_images,iap.is_available FROM invoice_address_products iap ' .
            'JOIN invoice_address_product_translations iapt ON ' .
            'iap.product_id=iapt.product_id AND iap.combination_id=iapt.combination_id AND iapt.language=\'' .
            $language . '\' JOIN duration_translations dt ON iap.duration_id=dt.duration_id AND dt.language=\'' .
            $language . '\' WHERE iap.order_address_id=\'' . $orderAddressId . '\' AND iap.user_id=' . $userId;
        return $query;
        return QueryHelper::select($query);
    }
}
