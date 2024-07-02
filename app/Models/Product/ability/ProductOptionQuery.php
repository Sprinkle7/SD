<?php

namespace App\Models\Product\ability;

use App\Helper\Database\QueryHelper;
use App\Models\Product\Option\OptionProduct;

trait ProductOptionQuery
{

    public static function fetchOptions($product, $language)
    {

        $query = 'SELECT op.product_id,op.option_id,op.has_no_select,op.arrange,ot.title,ot.language  FROM option_product op ' .
            'JOIN option_product_translation ot on op.option_id=ot.option_id and op.product_id=ot.product_id  WHERE op.product_id=:product && ot.language=:language';
        return QueryHelper::pdoSelect($query, ['product' => $product['id'], 'language' => $language]);
    }

    public static function fetchOptionsRealTranslation($product, $language)
    {

        $query = 'SELECT op.product_id,op.option_id,op.has_no_select,op.arrange,ot.title,ot.language  FROM option_product op ' .
            'JOIN option_translations ot on op.option_id=ot.option_id WHERE op.product_id=:product && ot.language=:language';
        return QueryHelper::pdoSelect($query, ['product' => $product['id'], 'language' => $language]);
    }

    public static function fetchOptionValues($product, $language)
    {

        $query = 'SELECT op.product_id,op.option_value_id,op.option_id,ot.title,ot.language,op.price,op.stock,op.arrange FROM option_value_product op ' .
            'LEFT JOIN option_value_translations ot on op.option_value_id=ot.option_value_id WHERE op.product_id=:product && ot.language=:language';
        return QueryHelper::pdoSelect($query, ['product' => $product['id'], 'language' => $language]);
    }

    public static function fetchOptionValuesWithOutTitle($product)
    {
        $query = 'SELECT opv.product_id,op.has_no_select,opv.option_id,' .
            'JSON_ARRAYAGG(JSON_OBJECT(\'option_value_id\',opv.option_value_id,\'price\',opv.price)) as option_values ' .
            'FROM option_value_product opv ' .
            'JOIN option_product op ON opv.product_id=op.product_id and opv.option_id=op.option_id ' .
            'WHERE opv.product_id=:product GROUP BY option_id';
//        return $query;
        return QueryHelper::pdoSelect($query, ['product' => $product['id']]);
    }

    public static function fetchOptionsWithValuesWithTitle($product, $language)
    {
        $options = self::fetchOptions($product, $language);

        if (count($options)==0) {
            $options = self::fetchOptionsRealTranslation($product, $language);
        }
        $optionValues = self::fetchOptionValues($product, $language);
        $optionHash = [];
        foreach ($options as $option) {
            $optionHash[$option['option_id']] = $option;
        }
        foreach ($optionValues as $optionValue) {
            $optionHash[$optionValue['option_id']]['option_values'][] = $optionValue;
        }

        return $optionHash;
    }

}
