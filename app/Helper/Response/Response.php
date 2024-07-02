<?php


namespace App\Helper\Response;


class Response
{
    private static $messageDefault = null;
    private static $errorDefault = null;
    private static $bodyDefault = [];

    public static function error500($error = 'INTERNAL SERVER ERROR')
    {
        return response()->json(self::responseStructure(false, 500
            , self::$messageDefault, self::$bodyDefault, $error), 500);
    }

    public static function error401($error = 'Unauthenticated')
    {
        return response()->json(self::responseStructure(false, 401
            , self::$messageDefault, self::$bodyDefault, $error), 401);
    }

    public static function error403($error = 'Forbidden')
    {
        return response()->json(self::responseStructure(false, 403
            , self::$messageDefault, self::$bodyDefault, $error), 403);
    }

    public static function response200($message = null, $body = [])
    {
        return response()->json(self::responseStructure(true, 200, $message, $body), 200)->header('Content-Type', 'application/json; charset=utf-8');
    }

    public static function response202($message = null, $body = [])
    {
        return response()->json(self::responseStructure(true, 202, $message, $body), 202)->header('Content-Type', 'application/json; charset=utf-8');
    }

    public static function error400($error)
    {
        return response()->json(self::responseStructure(false, 400,
            self::$messageDefault, self::$bodyDefault, $error), 400);
    }

    public static function error404($error='Not Found') {
        return response()->json(self::responseStructure(false, 404,
            self::$messageDefault, self::$bodyDefault, $error), 404);
    }

    private static function responseStructure($ok, $code, $message = null, $body = [], $error = null)
    {
        if (!is_array($error) && !is_null($error)) {
            $error = [$error];
        }
        if (!is_array($message) && !is_null($message)) {
            $message = [$message];
        }
        return ['message' => $message, 'errors' => $error, 'body' => $body, 'ok' => $ok, 'code' => $code];
    }

//    public static function response401($message) {
//        return response()->json(self::responseStructure(false, 401, $message));
//    }

//    private static function responseStructure($ok, $code, $message = null, $body = [], $error = null) {
//        return ['message'=> $message,'error'=>$error,'body'=> $body,'ok'=>$ok,'code'=>$code];
//

}
