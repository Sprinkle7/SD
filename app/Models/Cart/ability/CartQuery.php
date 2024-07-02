<?php

namespace App\Models\Cart\ability;


use App\Helper\Database\QueryHelper;
use App\Models\Cart\Cart;
use App\Models\Discount\Discount;
use App\Models\Product\Combination\CombinationImage;
use App\Models\Product\Product;
use App\Models\Service\ServiceValue;
use App\Models\User\Address;

trait CartQuery
{
    public static function fetchCartQuery($userId, $language, $page = 1, $perPage = 10)
    {
        $query = 'SELECT cc.product_id,p.code,p.price as product_price,p.cover_image,pt.title as product_title,pt.language,cc.duration_id,dd.duration,dt.title as duration_title,dp.price as duration_price,cc.user_id,cc.services,
cc.combination_id,cs.real_price,cs.additional_price,cartItemTitle(cc.combination_id,:language) as options,cc.quantity FROM carts cc ' .
            'JOIN products p ON cc.product_id=p.id ' .
            'JOIN product_translations pt ON cc.product_id=pt.product_id AND pt.language=:language ' .
            'JOIN durations dd ON cc.duration_id=dd.id ' .
            'JOIN duration_product dp ON cc.product_id=dp.product_id and cc.duration_id=dp.duration_id ' .
            'JOIN duration_translations dt ON cc.duration_id=dt.duration_id and dt.language=:language ' .
            'JOIN combinations cs ON cc.combination_id=cs.id where cc.user_id=:user AND cc.is_active=1' .
            QueryHelper::rawQueryPerPage($page, $perPage);
        return QueryHelper::pdoSelect($query, ['language' => $language, 'user' => $userId]);
    }

    public static function fetchCartItems($userId, $language = 'de', $page, $per_page, $withMenu = 1)
    {
        $cartItems = self::fetchCartQuery($userId, $language, $page, $per_page);
        foreach ($cartItems as $index => $item) {
            $cartItems[$index]['options'] = json_decode($item['options']);
            $services = explode(',', $item['services']);

            $services = ServiceValue::select('id', 'service_id', 'price', 'duration')->with(['serviceValueTranslation' => function ($query) use ($language) {
                $query->select('service_id', 'service_value_id', 'title', 'language')->where('language', $language);
            }])->whereIn('id', $services)->get();

            $discount = Discount::where('product_id', $cartItems[$index]['product_id'])
                ->where('quantity', '<=', $cartItems[$index]['quantity'])
                ->orderBy('quantity', 'DESC')->first();
          
            if ($withMenu) {
                $product = Product::with([
                    'defaultMenu' => function ($query) {
                        $query->select('id', 'level', 'parent_id')->where('is_active', 1);
                    },
                    'defaultMenu.menuT' => function ($query) use ($language) {
                        $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                    },
                    'defaultMenu.parent:id,level,parent_id',
                    'defaultMenu.parent.menuT' => function ($query) use ($language) {
                        $query->select('id', 'menu_id', 'title', 'language')->where('language', $language);
                    },
                ])->find($item['product_id']);
                $cartItems[$index]['menu'] = $product['defaultMenu'];
            }

            $cartItems[$index]['discount'] = $discount;
            $cartItems[$index]['service_values'] = $services;
            $cartItems[$index]['images'] = CombinationImage::select('path','arrange') ->where('combination_id',$item['combination_id'])->orderBy('arrange','ASC')->get();
        }
        return $cartItems;
    }

    public static function checkStock($productId, $combinationIds)
    {
        $combinationIds = implode(',', $combinationIds);
        $query = 'SELECT cov.combination_id,ovp.stock FROM combination_option_value cov ' .
            'JOIN option_value_product ovp ON cov.option_value_id=ovp.option_value_id AND ovp.product_id=:product ' .
            'AND ovp.stock=0 WHERE cov.combination_id IN (' . $combinationIds . ')';
        return QueryHelper::pdoSelect($query, ['product' => $productId]);
    }
}
