<?php

namespace App\Models\Product\ability;


use App\Helper\Database\QueryHelper;
use App\Models\Product\Pivot\Type1\Pt1Combination;
use App\Models\Product\Pivot\Type2\Pt2Combination;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

trait ProductQuery
{
    use ProductOptionQuery, ProductCombination;

    public static function getProductOptionsAndValues($productId, $language)
    {
        $query = 'SELECT ot.option_id, ot.title as \'option\', JSON_ARRAYAGG(JSON_OBJECT(\'option_value_id\',ovt.option_value_id,\'value\',ovt.title)) as \'values\' FROM pt1_option_value pv ' .
            'JOIN option_translations ot ON pv.option_id=ot.option_id AND ot.language=\'' . $language .
            '\' JOIN option_value_translations ovt on pv.option_value_id=ovt.option_value_id and ovt.language = \'' .
            $language . '\' where pv.product_id=' . $productId . ' GROUP BY pv.option_id ';
        return QueryHelper::select($query);
    }


    public static function fetchStockAmount($productId, $combinationId)
    {
        $query = 'SELECT cov.combination_id,ovp.stock,ovp.option_id,cov.option_value_id FROM combination_option_value cov ' .
            'JOIN option_value_product ovp ON cov.option_value_id=ovp.option_value_id AND ovp.product_id=:product ' .
            'WHERE cov.combination_id=:combination';
        return QueryHelper::pdoSelect($query, ['product' => $productId, 'combination' => $combinationId]);
    }

    public static function checkStock($productId, $combinationIds)
    {
        $query = 'SELECT cov.combination_id,ovp.stock FROM combination_option_value cov ' .
            'JOIN option_value_product ovp ON cov.option_value_id=ovp.option_value_id AND ovp.product_id=:product ' .
            'AND ovp.stock=0 WHERE IN cov.combination_id=:combinations';
        return QueryHelper::pdoSelect($query, ['product' => $productId, 'combinations' => $combinationIds]);
    }
}

