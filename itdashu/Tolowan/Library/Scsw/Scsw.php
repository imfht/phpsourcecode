<?php
namespace Library\Scsw;

use Library\Pinyin\Pinyin;
use Library\Scsw\PSCWS4;

class Scsw{
    public static function toInfo($word){
        $output = array();
        if(function_exists('scws_new')){
            $so = scws_new();
        }else {
            $so = new PSCWS4();
        }
        $so->set_charset('utf8');
        $so->set_ignore(true);
        $so->set_dict(__DIR__.'/etc/dict.utf8.xdb');
        $so->set_rule(__DIR__.'/etc/rules.utf8.ini');
        //$so->set_multi(8);
        // 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $so->send_text($word);
        while ($tmp = $so->get_result())
        {
            $output[] = $tmp;
        }
        $so->close();
        return $output;
    }
    public static function shortWord($word){
        $output = '';
        $word = strip_tags($word);
        if(function_exists('scws_new')){
            $so = scws_new();
        }else {
            $so = new PSCWS4();
        }
        $so->set_charset('utf8');
        $so->set_ignore(true);
        $so->set_dict(__DIR__.'/etc/dict.utf8.xdb');
        $so->set_rule(__DIR__.'/etc/rules.utf8.ini');
        //$so->set_multi(8);
// 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $so->send_text($word);
        while ($tmp = $so->get_result())
        {
            foreach ($tmp as $tw){
                $output .=  $tw['word'].' ';
            }
        }
        $so->close();
        $output = strtolower(trim(json_encode($output),'"'));
        return $output;
    }
    public static function toString($word)
    {
        $output = '';
        $word = strip_tags($word);
        if(function_exists('scws_new')){
            $so = scws_new();
        }else {
            $so = new PSCWS4();
        }
        $so->set_charset('utf8');
        $so->set_ignore(true);
        $so->set_dict(__DIR__ . '/etc/dict.utf8.xdb');
        $so->set_rule(__DIR__ . '/etc/rules.utf8.ini');
        //$so->set_multi(8);
// 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $so->send_text($word);
        foreach ($so->get_tops(600) as $tw) {
            $output .= $tw['word'] . ' ';
        }
        $so->close();
        $output = strtolower(trim(json_encode($output), '"'));
        return $output;
    }
}