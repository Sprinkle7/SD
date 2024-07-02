<?php

namespace App\Models\Product\ability;

use App\Helper\Database\QueryHelper;

trait ProductCombination
{
    public static function fetchCombinations($request, $product, $language)
    {
        $parameters = ['product' => $product['id'], 'language' => $language,];

            $condition = '(SELECT * FROM combinations c where c.product_id=:product';
            if (!is_null($request['optionValueIds'])) {
                $condition .= ' AND c.combination=:options ';
                $parameters['options'] = $request['optionValueIds'];
            }
            if (!is_null($request['is_active'])) {
                $condition .= ' AND c.is_active=:activation';
                $parameters['activation'] = $request['is_active'];

            }
            $condition .= ' ORDER BY c.id ASC'.
                QueryHelper::rawQueryPerPage($request['page'], $request['per_page']).' )';

        $query = 'SELECT pc.id,pc.is_default,pc.price,pc.real_price,pc.is_active,pc.additional_price,pc.combination,' .
            'GROUP_CONCAT(ovt.title) as option_values,(SELECT COUNT(pci.id) FROM combination_images pci ' .
            'WHERE pci.combination_id=pc.id) as image_count FROM ' . $condition . ' pc ' .
            'JOIN combination_option_value pov ON pc.id=pov.combination_id ' .
            'LEFT JOIN option_value_translations ovt ON pov.option_value_id=ovt.option_value_id AND ovt.language=:language ' .
            'GROUP BY pc.id';
        if (!is_null($request['have_image'])) {
            $query .= ' HAVING image_count>0';
        }
//        $query .= ' ORDER BY pc.is_active DESC';
//        return $query;
        return QueryHelper::pdoSelect($query, $parameters);

//        return $query;
    }

    public static function fetchCombinationCount($request, $product, $language)
    {
        $condition = 'combinations';
        $parameters = ['product' => $product['id'], 'language' => $language,];
        if (!is_null($request['optionValueIds']) || !is_null($request['is_active'])) {

            $condition = '(SELECT * FROM combinations c where c.product_id=:product';
            if (!is_null($request['optionValueIds'])) {
                $condition .= ' AND c.combination=:options ';
                $parameters['options'] = $request['optionValueIds'];
            }
            if (!is_null($request['is_active'])) {
                $condition .= ' AND c.is_active=:activation';
                $parameters['activation'] = $request['is_active'];

            }
            $condition .= ' )';

        }
        $query = 'SELECT pc.id,pc.is_default,pc.price,pc.real_price,pc.is_active,pc.additional_price,pc.combination,' .
            'JSON_ARRAYAGG(JSON_OBJECT(\'title\',ovt.title)) as option_values,(SELECT COUNT(pci.id) FROM combination_images pci ' .
            'WHERE pci.combination_id=pc.id) as image_count FROM ' . $condition . ' pc ' .
            'JOIN combination_option_value pov ON pc.id=pov.combination_id ' .
            'LEFT JOIN option_value_translations ovt ON pov.option_value_id=ovt.option_value_id AND ovt.language=:language ' .
            'WHERE pc.product_id=:product GROUP BY pc.id';
        if (!is_null($request['have_image'])) {
            $query .= ' HAVING image_count>0';
        }

        return QueryHelper::pdoSelect($query, $parameters);

        return $query;
    }

    public static function fetchCombinationOptionValuesAllLanguage($combination)
    {
        $query = 'SELECT cov.combination_id,JSON_ARRAYAGG(ovt.title) as option_values,ovt.language FROM combination_option_value cov '.
            'JOIN option_value_translations ovt ON cov.option_value_id=ovt.option_value_id '.
            'WHERE cov.combination_id=:combination GROUP BY cov.combination_id';
        return QueryHelper::pdoSelect($query, ['combination' => $combination]);
    }

}
