<?php

/**
 * @author 暮雨秋晨
 * @copyright 2014
 */

class TemplateCompile
{
    private static $dir; //模板路径
    private static $tpl = 'default'; //模板名
    private static $rules = array(
        /*数组输出*/
        '!\{(\$\w+)\.(\d+)\.(\d+)\}!Ui' => '<?php echo $1[$2][$3];?>',
        '!\{(\$\w+)\.(\d+)\}!Ui' => '<?php echo $1[$2];?>',
        
        '!\{(\$\w+)\.(\w+)\.(\w+)\}!Ui' => '<?php echo $1[\'$2\'][\'$3\'];?>',
        '!\{(\$\w+)\.(\w+)\}!Ui' => '<?php echo $1[\'$2\'];?>',
        
        /*
        if(1<2)
        
        */
        '!\{if(.+)\}!Ui' => '<?php if($1):?>',
        '!\{elseif(.+)\}!Ui' => '<?php elseif($1):?>',
        '!\{else\}!Ui' => '<?php else:?>',
        '!\{\/if\}!Ui' => '<?php endif;?>',

        /*
        $id=100
        {from 0/$id to $id --}
        {/from}
        */
        '!\{from\s+(.+)\s+to\s+(.+)\s+\-\-\}!Ui' => '<?php $i=$1; $limit=$2; for($i;$i>$limit;$i--):?>',
        '!\{from\s+(.+)\s+to\s+(.+)\s+\+\+\}!Ui' => '<?php $i=$1; $limit=$2; for($i;$i<$limit;$i++):?>',
        '!\{from\s+(.+)\s+to\s+(.+)\s+\-(\d+)\}!Ui' => '<?php $i=$1; $limit=$2; $step=$3; for($i;$i>$limit;$i-=$step):?>',
        '!\{from\s+(.+)\s+to\s+(.+)\s+\+(\d+)\}!Ui' => '<?php $i=$1; $limit=$2; $step=$3; for($i;$i<$limit;$i+=$step):?>',
        '!\{\/from\}!Ui' => '<?php endfor;?>',

        /*
        {loop $arr}
        {loop $arr as $val}
        {loop $arr as $key=>$val}
        {/loop}
        */
        '!\{loop\s+(.+)\s+as\s+(\$\w+)\=\>(\$\w+)\}!Ui' => '<?php foreach($1 as $2=>$3):?>',
        '!\{loop\s+(.+)\s+as\s+(\$\w+)\}!Ui' => '<?php foreach($1 as $2):?>',
        '!\{loop\s+(.+)\}!Ui' => '<?php foreach($1 as $key=>$val):?>',
        '!\{\/loop\}!Ui' => '<?php endforeach;?>',
        
        /*
        {switch $fuck}
        {case abc}
        {/case}
        {default}
        {/switch}
        */
        '!\{switch\s+(.+)\}!Ui' => '<?php switch($1):?>',
        '!\{case\s+(.+)\}!Ui' => '<?php case \'$1\':?>',
        '!\{\/case\}!Ui' => '<?php break;?>',
        '!\{default\}!Ui' => '<?php default:?>',
        '!\{\/switch\}!Ui' => '<?php endswitch;?>',

        /*{include 'common/fuck.html'}*/
        '!\{include\s+[\'|"](.+)[\'|"]\}!Ui' => '<?php include $this->_include(\'$1\');?>',
        
        /*系统全局变量输出*/
        '!\{\$\.get\.(\w+)\}!Ui' => '<?php echo $_GET[\'$1\'];?>',
        '!\{\$\.post\.(\w+)\}!Ui' => '<?php echo $_POST[\'$1\'];?>',
        '!\{\$\.request\.(\w+)\}!Ui' => '<?php echo $_REQUEST[\'$1\'];?>',
        '!\{\$\.cookie\.(\w+)\}!Ui' => '<?php echo $_COOKIE[\'$1\'];?>',
        '!\{\$\.session\.(\w+)\}!Ui' => '<?php echo $_SESSION[\'$1\'];?>',

        /*输出变量*/
        '!\{(\$\w+)\}!Ui' => '<?php echo $1;?>',
        
        /*dump函数*/
        '!\{dump\((.+)\)\}!Ui' => '<?php dump($1);?>',
        /*PHP自带date函数*/
        '!\{date\([\'|"](.+)[\'|"],(\$?\w+)\)\}!Ui' => '<?php echo date(\'$1\',$2);?>',
        /*框架自带时间格式化输出函数，如：38秒前*/
        '!\{dateFormat\((.+)\)\}!Ui' => '<?php echo dateFormat($1);?>',
        /*框架自带字符串截取函数*/
        '!\{strCut\((.+),(\d+),(\d+)\)\}!Ui' => '<?php echo strCut($1,$2,$3);?>',
        /*字符统计*/
        '!\{total\((.+),(.+)\)\}!Ui' => '<?php echo substr_count($1,$2);?>',
        /*数组统计*/
        '!\{total\((.+)\)\}!Ui' => '<?php echo count($1);?>',
        /*字符串大小写转换*/
        '!\{upper\((.+)\)\}!Ui' => '<?php echo strtoupper($1);?>',
        '!\{lower\((.+)\)\}!Ui' => '<?php echo strtolower($1);?>',
        /*缓存功能调用*/
        '!\{cache:full\}!Ui' => '<?php echo Cache::getFull();?>',
        '!\{cache:block\/start\((.+),(\d+)\)\}!Ui' => '<?php Cache::block($1,$2);?>',
        '!\{cache:block\/catch\((.+)\)\}!Ui' => '<?php Cache::catchBlock($1);?>',
        '!\{cache:block\/get\((.+)\)\}!Ui' => '<?php echo Cache::getBlock($1);?>',
        '!\{cache:data\/set\((.+),(.*),(\d+)\)\}!Ui' => '<?php Cache::data($1,$2,$3);?>',
        '!\{cache:data\/get\((.+)\)\}!Ui' => '<?php echo Cache::getData($1);?>',
        );

    private static $replace = array();

    public function __construct($d, $t)
    {
        self::$dir = $d;
        self::$tpl = $t;
        self::$replace = array('{__STATIC__}' => '/' . APP . '/' . STC, );
    }

    /**
     * 编译模板
     */
    public function compile($res)
    {
        $res = str_replace(array_keys(self::$replace), self::$replace, $res);
        $res = preg_replace(array_keys(self::$rules), self::$rules, $res);
        $res = "<?php defined('ROOT_DIR') or die('Access denied!');?>\r\n" . $res;
        return $res;
    }
}

?>