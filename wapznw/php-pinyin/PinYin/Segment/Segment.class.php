<?php
/**
 * User: 自娱自乐自逍遥
 * Date: 2014/12/8
 * Time: 16:49
 */

require_once 'phpanalysis/phpanalysis.class.php';

PhpAnalysis::$loadInit = false;

class Segment {

    /**
     * @var PhpAnalysis
     */
    protected static $PhpAnalysis;

    public function __construct(){
        if(empty(self::$PhpAnalysis)){
            $pa = new PhpAnalysis('utf-8', 'utf-8', false);
            //载入词典
            $pa->LoadDict();
            self::$PhpAnalysis = $pa;
        }
    }

    public function splitResult($str, $do_fork = false){
        self::$PhpAnalysis->SetSource($str);
        self::$PhpAnalysis->StartAnalysis($do_fork);
        return trim(self::$PhpAnalysis->GetFinallyResult(' '));
    }
} 