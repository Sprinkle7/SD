<?php


namespace App\Helper\json;


class JsonD
{
    public static function json_decode($jsonString)
    {
        $combs = json_decode($jsonString);
        $jsonArr = [];
        foreach ($combs as $comb) {
            if (is_string($comb)) {
                $jsonArr[] = json_decode($comb);
            } else {
                $jsonArr[] = $comb;
            }
        }
        return $jsonArr;
    }
}
