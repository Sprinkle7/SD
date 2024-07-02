<?php


namespace App\Helper\Sort;


class BubbleSort
{
    public static function sort(&$arr)
    {
        $n = sizeof($arr);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                $catj = explode('/',$arr[$j]);
                $catj1 = explode('/',$arr[$j + 1]);
                if (intval($catj[0]) > intval($catj1[0])) {
                    $t = $arr[$j];
                    $arr[$j] = $arr[$j + 1];
                    $arr[$j + 1] = $t;
                }
            }
        }
    }
}
