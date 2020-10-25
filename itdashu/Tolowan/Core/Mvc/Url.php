<?php
namespace Core\Mvc;

use Core\Config;
use Phalcon\Mvc\Url as Purl;

class Url extends Purl
{
    public function get($uri = null, $args = null, $local = null, $baseUri = null)
    {
        //echo "string";
        global $di;
        $t = $di->getShared('translate');
        $translate = Config::get('translate');
        if($translate['translate']) {
            $language = $t->getBestLanguage();
            switch ($translate['translate_type']) {
                case 1:
                    $baseUri = $translate['domain'][$language];
                    break;
                case 2:
                    if(is_array($uri)){
                        $uri['language'] = $language;
                    }
                    break;
                case 3:
                    $args = array('language'=>$language);
                    break;
                case 4:
                    break;
            }
        }
        //print_r($uri);
        return parent::get($uri, $args, $local, $baseUri);
    }
}
