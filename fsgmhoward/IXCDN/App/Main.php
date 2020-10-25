<?php
/**
 * This file is part of IXCDN
 * Published under MIT License
 * Copyright (c) Howard Liu, 2016
 * Please refer to LICENSE file for more information
 */

namespace App;

use IXNetwork\Lib\Tool\Cache as CDNCache;

class Main
{
    //Default CDN address, you do not need to change it.
    private $cdn = 'cdn.ixnet.work';

    private $site;
    private $route;
    private $isCaching;
    private static $sites = ['wpcom', 'fonts', 'ajax', 'gravatar', 'worg', 'gs-fonts'];
    private static $reReplaceTypes = ['css', 'js'];
    private static $isText = [
        'wpcom'    => true,
        'fonts'    => true,
        'ajax'     => true,
        'gravatar' => false,
        'worg'     => false,
        'gs-fonts' => false
    ];
    private static $addresses = [
        'wpcom'    => 'http://s0.wp.com',
        'fonts'    => 'http://fonts.googleapis.com',
        'ajax'     => 'http://ajax.googleapis.com',
        'gravatar' => 'http://0.gravatar.com',
        'worg'     => 'http://s.w.org',
        'gs-fonts' => 'http://fonts.gstatic.com'
    ];

    public function __construct($link, $cdnAddress = null, $allowOrigin = '*', $isCaching = true)
    {
        CDNCache::init(
            self::$sites,
            [
                'wpcom'    => 0,
                'fonts'    => 0,
                'ajax'     => 0,
                'gravatar' => 7200,
                'worg'     => 7200,
                'gs-fonts' => 28400
            ],
            __DIR__.'/../Cache/'
        );
        header("Access-Control-Allow-Origin: $allowOrigin");
        $this->isCaching = $isCaching;
        $link = explode('|', $link);
        if (sizeof($link)!=2 || !in_array($link[0], self::$sites)) {
            header('HTTP/1.1 403 Access Denied');
            exit('403: Route Not Valid');
        } else {
            $this->site = $link[0];
            $this->route = $link[1];
        }
        if ($cdnAddress) {
            $this->cdn = $cdnAddress;
        }
    }

    public function handler()
    {
        if ($content = CDNCache::get($this->site, md5($this->route))) {
            return $content;
        } else {
            $content = $this->getContents(self::$addresses[$this->site].$this->route);
            CDNCache::put($this->site, md5($this->route), $content, self::$isText[$this->site]);
            return $content;
        }
    }

    private function getContents($url)
    {
        $data = Data::get($url);
        if ($data[1] == 200) {
            header('Content-type: '.$data[2]);
            foreach (self::$reReplaceTypes as $reReplaceType) {
                if (stripos($data[1], $reReplaceType)) {
                    $data[0] = $this->reReplace($data[0]);
                    break;
                }
            }
        } else {
            header('HTTP/1.1 '.$data[1]);
        }
        return $data[0];
    }

    private function reReplace($content)
    {
        $content = str_replace(
            [
                "//gravatar.com/",
                "//secure.gravatar.com",
                "//www.gravatar.com",
                "//0.gravatar.com",
                "//1.gravatar.com",
                "//2.gravatar.com",
                "//cn.gravatar.com"
            ],
            "//$this->cdn/gravatar|",
            $content
        );
        $content = str_replace("//fonts.googleapis.com", "//$this->cdn/fonts|", $content);
        $content = str_replace("//fonts.gstatic.com", "//$this->cdn/gs-fonts|", $content);
        $content = str_replace("//ajax.googleapis.com", "//$this->cdn/ajax|", $content);
        $content = str_replace("\\/\\/s.w.org", "\\/\\/$this->cdn\\/worg|", $content);
        $content = str_replace("//s.w.org", "//$this->cdn/worg|", $content);
        $content = str_replace(["//s0.wp.com", "//s1.wp.com"], "//$this->cdn/wpcom|", $content);
        $content = str_replace('url(\'/', 'url(\'/'.$this->site.'|/', $content);
        $content = str_replace(['http:','https:'], '', $content);
        return $content;
    }
}
