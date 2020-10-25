<?php
/**
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;

class Translate {

    private $lib = [];

    public function __construct( $lang = '' ) {
        if( empty($lang) ){
            $lang = Config::get('DEFAULT_LANGUAGE');
        }
        $libFile = DOCUMENT_ROOT.'/PhalApi/Language/'.$lang.'.php';
        if( file_exists( $libFile ) ){
            $this->lib = include ($libFile);
        }else{
            throw new PAException('语言库不存在!!!');
        }
    }

    public function get( $item ){
        $str = '';
        $itemArr = explode('.', $item);
        foreach ( $itemArr as $item ){
            if( isset($this->lib[$item]) ){
                $str .= $this->lib[$item];
            }else{
                throw new PAException('翻译项'.$item.'不存在!');
            }
        }
        return $str;
    }

}