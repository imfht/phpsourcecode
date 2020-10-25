<?php

use LessPHP\Net\Http;

class lesscreator_fs
{
    private static $client = null;

    public static function NetHttp($url)
    {
        if (self::$client === null) {
            self::$client = new Http("");
        }

        self::$client->setUri($url);
        
        return self::$client;
    }

    public static function FsList($path)
    {
        $req = array(
            'data' => array('path' => $path, 'subdir' => false),
        );

        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/fs-list");

        $ret = $cli->Post(json_encode($req));
        if ($ret != 200) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (!isset($ret->status)) {
            return false;
        }

        return $ret;
    }
    
    public static function FsListAll($path)
    {
        $req = array(
            'data' => array('path' => $path, 'subdir' => true)
        );

        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/fs-list");

        $ret = $cli->Post(json_encode($req));
        if ($ret != 200) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (!isset($ret->status)) {
            return false;
        }

        return $ret;
    }

    public static function FsFileGet($file)
    {
        $req = array(
            'data' => array('path' => $file),
        );

        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/fs-file-get");

        $ret = $cli->Post(json_encode($req));
        if ($ret != 200) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (!isset($ret->status)) {
            return false;
        }

        return $ret;
    }

    public static function FsFileExists($file)
    {
        $req = array(
            'data' => array('path' => $file),
        );

        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/fs-file-exists");

        $ret = $cli->Post(json_encode($req));
        if ($ret != 200 && $ret != 404) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (!isset($ret->status) || $ret->status == 404) {
            return false;
        }

        return true;
    }

    public static function FsFilePut($path, $body)
    {
        $req = array(
            'data' => array(
                'path' => $path,
                'body' => $body,
                'sumcheck' => md5($body),
            ),
        );
        if (isset($_COOKIE['access_token'])) {
            $req['access_token'] = $_COOKIE['access_token'];
        }

        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/fs-file-put");

        $ret = $cli->Post(json_encode($req));
        if ($ret != 200) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (!isset($ret->status)) {
            return false;
        }

        return $ret;
    }

    public static function EnvNetPort()
    {
        $cli = self::NetHttp("http://127.0.0.1:9531/lesscreator/api/env-netport");

        $ret = $cli->Get();
        if ($ret != 200 && $ret != 404) {
            return false;
        }

        $ret = json_decode($cli->getBody(), false);
        if (isset($ret->status) && $ret->status == 200) {
            return $ret->data->port;
        }

        return true;
    }
}
