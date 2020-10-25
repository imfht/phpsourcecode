<?php

/**
 * @author mr小卓X<mrxzx@wwsg18.top>
 * @copyright ©2018 wwsg18.top
 * @link http://Wetpl.wwsg18.top
 * @version 1.0.0
 */

class Parser{
    private $temp;
    private $lsym;
    private $rsym;

    private $T_P = array();
    private $T_R = array();

    public function __construct($tpl,$vars,$lsym,$rsym){

        $this->lsym = $lsym;
        $this->rsym = $rsym;

        $this->temp .= $lsym.'%json_decode(\''.json_encode($vars).'\',true) to $vars'.$rsym.PHP_EOL;
        $this->temp .= $lsym.'loop $k,$v in $vars'.$rsym.PHP_EOL;
        $this->temp .= $lsym.'php:$$k = $v;'.$rsym.PHP_EOL;
        $this->temp .= $lsym.'end'.$rsym.PHP_EOL;
        $this->temp .= $tpl;
    }

    private function compile_code(){

        $this->T_P[] = '/\<\?php(.+?)\?\>/';

        //变量输出
        $this->T_P[] = '/'.$this->lsym.'\s?\$(.*?)\s?'.$this->rsym.'/';

        //判断语句
        $this->T_P[] = '/'.$this->lsym.'\s?if (.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?elif (.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?else (.*?)\s?'.$this->rsym.'/';

        $this->T_P[] = '/'.$this->lsym.'\s?select (.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?case else:\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?case (.*?):\s?'.$this->rsym.'/';

        //循环语句
        $this->T_P[] = '/'.$this->lsym.'\s?while (.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?for (.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?loop (.+?),(.+?) in (.+?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?loop (.+?) in (.+?)\s?'.$this->rsym.'/';

        //跳转语句
        $this->T_P[] = '/'.$this->lsym.'\s?break\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?go (.+?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?point (.+?):\s?'.$this->rsym.'/';

        //函数调用
        $this->T_P[] = '/'.$this->lsym.'\s?\&(.+?)\((.*?)\)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?(.+?)\((.*?)\)\s?'.$this->rsym.'/';

        //赋值语句
        $this->T_P[] = '/'.$this->lsym.'\s?\%(.+?) to \$(\w+)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?\%(.+?) to (\+|\-|\*\/|\.) \$(\w+)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?\%\s?\$(\w+) \= (.+?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?\%\s?\$(\w+) (\+|\-|\*\/|\.)\= (.+?)\s?'.$this->rsym.'/';

        //函数操作
        $this->T_P[] = '/'.$this->lsym.'\s?func (\w+)\((.*?)\)\:(.+?)\s?'.$this->rsym.'/';

        //其他语句
        $this->T_P[] = '/'.$this->lsym.'\s?end\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?php:(.*?)\s?'.$this->rsym.'/';
        $this->T_P[] = '/'.$this->lsym.'\s?#(.*?)\s?'.$this->rsym.'/';


        $this->T_R[] = '';

        //变量输出
        $this->T_R[] = '<?php echo $$1; ?>';

        //判断语句
        $this->T_R[] = '<?php if($1){ ?>';
        $this->T_R[] = '<?php }elseif($1){ ?>';
        $this->T_R[] = '<?php }else{ ?>';

        $this->T_R[] = '<?php switch($1){ ?>';
        $this->T_R[] = '<?php default: ?>';
        $this->T_R[] = '<?php case $1: ?>';

        //循环语句
        $this->T_R[] = '<?php while($1){ ?>';
        $this->T_R[] = '<?php for($1){ ?>';
        $this->T_R[] = '<?php foreach($3 as $1 => $2){ ?>';
        $this->T_R[] = '<?php foreach($2 as $1){ ?>';

        //跳转语句
        $this->T_R[] = '<?php break; ?>';
        $this->T_R[] = '<?php goto $1; ?>';
        $this->T_R[] = '<?php $1: ?>';

        //函数调用
        $this->T_R[] = '<?php echo $1($2); ?>';
        $this->T_R[] = '<?php $1($2); ?>';

        //赋值语句
        $this->T_R[] = '<?php $$2 = $1; ?>';
        $this->T_R[] = '<?php $$3 $2= $1; ?>';
        $this->T_R[] = '<?php $$1 = $2; ?>';
        $this->T_R[] = '<?php $$1 $2= $3; ?>';

        //函数操作
        $this->T_R[] = '<?php function $1($2){$3} ?>';

        //其他语句
        $this->T_R[] = '<?php } ?>';
        $this->T_R[] = '<?php $1 ?>';
        $this->T_R[] = '<?php //$1 ?>';

    }

    private function compile_import(){
        $import_pattern = '/'.$this->lsym.'\s?import (.+?)\s?'.$this->rsym.'/';;

        preg_match_all($import_pattern,$this->temp,$result,PREG_SET_ORDER);
        foreach($result as $file){
            $file = $file[1];

            if(is_file($file)){
                $this->temp = preg_replace($import_pattern, file_get_contents($file), $this->temp, 1);
            }else{
                $this->temp = preg_replace($import_pattern,'', $this->temp, 1);
            }
        }
    }

    public function Parse(){
        $this->compile_code();
        $this->compile_import();
        $new_tpl = preg_replace($this->T_P,$this->T_R,$this->temp);
        return $new_tpl;
    }

}