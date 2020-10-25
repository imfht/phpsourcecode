<?php

namespace App\Libs\Routers;

class ControllerPathCbRouter
{
    private static $path = '';

    public static function path($app)
    {
        $reqUri = $app->request->server('request_uri');
        self::parseInit($reqUri);
        return self::$path;
    }

    private static function parseInit($reqUri)
    {
        $arrPath = parse_url($reqUri);
        $query = isset($arrPath['query']) ? $arrPath['query'] : '';
        $arrQuery = array_filter(explode('&', $query));
        list($path, $arrQuery) = self::getRequestPath($arrQuery);

        self::initGetParam($arrQuery);
        self::$path = '/' . trim($path, '/');
    }

    private static function getRequestPath($arrQuery)
    {
        $path = $arrQuery ? array_shift($arrQuery) : '';
        if (false !== stripos($path, '=')) {
            $path = '';
            array_unshift($arrQuery, $path);
        }
        return [$path, $arrQuery];
    }

    private static function initGetParam(array $arrQuery)
    {
        if ($arrQuery) {
            foreach ($arrQuery as $get) {
                $itemGet = explode('=', $get);
                if (count($itemGet) != 2) {
                    continue;
                }
                $_GET[$itemGet[0]] = $itemGet[1];
            }
        }
    }
}
