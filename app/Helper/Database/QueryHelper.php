<?php


namespace App\Helper\Database;


use Illuminate\Support\Facades\DB;

class QueryHelper
{
    public static function select($query)
    {
        $result = DB::select(
            DB::raw($query));
        return json_decode(json_encode($result, JSON_UNESCAPED_UNICODE), true);
    }

    public static function pdoSelect($query, $bind)
    {
        $result = DB::select(
            DB::raw($query), $bind);
        return json_decode(json_encode($result, JSON_UNESCAPED_UNICODE), true);
    }

    public static function pdoSelectNoDecode($query, $bind)
    {
        $result = DB::select(
            DB::raw($query), $bind);
        return $result;
    }

    public static function rawQueryPerPage($page = 1, $perPage = 10)
    {
        $perPage = (is_null($perPage)) ? 10 : $perPage;
        $page = is_null($page) ? 0 : ($page - 1) * $perPage;
        $query = ' LIMIT ' . $page . ',' . $perPage;
        return $query;
    }

    public static function perPage($request)
    {
        return isset($request['per_page']) ? $request['per_page'] : 10;
    }
}
