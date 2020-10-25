<?php
/**
 * User: 自娱自乐自逍遥
 * Date: 2014/12/8
 * Time: 15:18
 */
require_once 'Segment/Segment.class.php';

class PinYin {

    /**
     * 拼音字典
     * @var array
     */
    protected static $_PINYIN_DICT;

    /**
     * 带音标字符
     * @var array
     */
    protected static $_PHONETIC_SYMBOL;

    protected static $_PHRASES_DICT;

    protected static $re_phonetic_symbol_source = '';

    protected static $CHARSET = 'utf-8';

    /**
     * @var Segment
     */
    protected static $segment;

    public static function init(){
        if(empty(self::$_PINYIN_DICT)){
            self::$_PINYIN_DICT = include 'dict/dict-zi.php';
        }
        if(empty(self::$_PHONETIC_SYMBOL)){
            self::$_PHONETIC_SYMBOL = include 'dict/phonetic-symbol.php';
            foreach(self::$_PHONETIC_SYMBOL as $key=>$val){
                self::$re_phonetic_symbol_source .= $key;
            }
        }

        if(empty(self::$segment)){
            self::$segment = new Segment();
        }
    }

    public static function single_pinyin($han, $style){
        if(mb_strlen($han, self::$CHARSET) > 1){
            return self::single_pinyin(self::substr($han,0,1), $style);
        }
        self::init();
        $code = self::unicodeChar($han);
        if(empty(self::$_PINYIN_DICT[$code])){
            return array($han);
        }

        $pys = explode(',', self::$_PINYIN_DICT[$code]);

        return self::toFixed($pys[0], $style);
    }

    public static function phrases_pinyin($phrases, $style){
        if(empty(self::$_PHRASES_DICT)){
            self::$_PHRASES_DICT = include 'dict/phrases-dict.php';
        }
        $py = array();
        if(isset(self::$_PHRASES_DICT[$phrases])){
            $p = self::$_PHRASES_DICT[$phrases];
            foreach ($p as $key => $item) {
                $py[$key] = [];
                foreach($item as $k=>$v){
                    $py[$key] = self::toFixed($v, $style);
                }
            }
        }else{
            for($i = 0; $i < self::strlen($phrases); $i++){
                $word = self::substr($phrases, $i, 1);
                $py[] = self::single_pinyin($word, $style);
            }
        }
        return $py;
    }

    /**
     * 汉字转拼音
     * @param $hans string 字符串
     * @param int $style 是否显示声调
     * @return array
     */
    public static function toPinyin($hans, $style = 0){
        self::init();
        $phrases = self::participle($hans, true);

        $pys = array();
        $nohans = '';
        for($i=0;$i<count($phrases);$i++){
            $words = $phrases[$i];
            $firstChar = self::unicodeChar(self::substr($words, 0, 1));
            if(isset(self::$_PINYIN_DICT[$firstChar])){
                if(self::strlen($nohans) > 0){
                    $pys[] = $nohans;
                    $nohans = '';
                }
                if(self::strlen($words) === 1){
                    $pys[] = self::single_pinyin($words, $style);
                }else{
                    $pys = array_merge($pys, self::phrases_pinyin($words, $style));
                }
            }else{
                $nohans .= $words;
            }
        }
        if(self::strlen($nohans) > 1){
            $pys[] = $nohans;
            $nohans = '';
        }
        return $pys;
    }

    public static function toFixed($pinyin, $style = 0){
        if($style === 0){
            foreach(self::$_PHONETIC_SYMBOL as $key=>$val){
                if(false !== ($pos = strpos($pinyin, $key))){
                    $k = mb_substr($pinyin,$pos,1,self::$CHARSET);
                    $pinyin = str_replace($k, preg_replace('/([aeoiuvnm])([0-4])$/','$1',$val), $pinyin);
                }
            }
        }
        return $pinyin;
    }

    /**
     * 分词
     * @param string $string
     * @param bool $returnArray 是否返回数组
     * @return string
     */
    public static function participle($string = '', $returnArray = false){
        $result = self::$segment->splitResult($string, true);
        if($returnArray){
            $result = explode(' ', $result);
        }
        return $result;
    }

    public static function unicodeChar($str = ''){
        return hexdec(substr(json_encode($str),3,-1));
    }

    public static function strlen($str){
        return mb_strlen($str, self::$CHARSET);
    }

    public static function substr($str, $start, $len){
        return mb_substr($str, $start, $len, self::$CHARSET);
    }
} 