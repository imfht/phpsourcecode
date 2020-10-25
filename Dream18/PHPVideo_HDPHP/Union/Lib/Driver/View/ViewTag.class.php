<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * HDPHP模板引擎标签解析类
 *
 * @package          View
 * @subpackage       HDPHP模板
 * @author           后盾向军 <houdunwangxj@gmail.com>
 */
class ViewTag extends Tag
{
    /**
     * block 块标签
     * level 嵌套层次
     *
     * @var array
     */
    public $Tag = array(
            'foreach' => array('block' => 1, 'level' => 5),
            'list'    => array('block' => 1, 'level' => 5),
            'if'      => array('block' => 1, 'level' => 5),
            'elseif'  => array('block' => 0, 'level' => 0),
            'else'    => array('block' => 0, 'level' => 0),
            'js'      => array('block' => 0, 'level' => 0),
            'css'     => array('block' => 0, 'level' => 0),
            'include' => array('block' => 0, 'level' => 0),
            'jsconst' => array('block' => 0, 'level' => 1),
            'empty'   => array('block' => 1, 'level' => 5),
            'noempty' => array('block' => 0, 'level' => 0),
        );

    //构造函数
    public function __init()
    {
    }

    //css标签
    public function _css($attr, $content, &$hd)
    {
        return "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$attr['file']}\"/>";
    }

    //js标签
    public function _js($attr, $content, &$hd)
    {
        return "<script type=\"text/javascript\" src=\"{$attr['file']}\"></script>";
    }

    //list标签
    public function _list($attr, $content, &$hd)
    {
        //变量
        $from = $attr['from'];
        //name名
        $name = substr($attr['name'],1);
        //默认值
        $empty = isset($attr['empty']) ? $attr['empty'] : '';
        //显示条数
        $row = isset($attr['row']) ? $attr['row'] : 100;
        //间隔
        $step = isset($attr['step']) ? $attr['step'] : 1;
        //开始数
        $start = isset($attr['start']) ? $attr['start'] : 0;
        $php
               = <<<php
        <?php
        //初始化
        \$hd['list']['$name'] = array(
            'first' => false,
            'last'  => false,
            'total' => 0,
            'index' => 0
        );
        if (empty($from)) {
            echo '$empty';
        } else {
            \$listId = 0;
            \$listShowNum=0;
            \$listNextId=$start;
            foreach ($from as \$$name) {
                //开始值
                if (\$listId<$start) {
                    \$listId++;
                    continue;
                }
                //步长
                if(\$listId!=\$listNextId){\$listId++;continue;}
                //显示条数
                if(\$listShowNum>=$row)break;
                //第几个值
                \$hd['list'][$name]['index']++;
                //第1个值
                \$hd['list'][$name]['first']=(\$listId == $start);
                //最后一个值
                \$hd['list'][$name]['last']= (count($from)-1 <= \$listId);
                //总数
                \$hd['list'][$name]['total']++;
                //增加数
                \$listId++;
                \$listShowNum++;
                \$listNextId+=$step
                ?>
php;
        $php .= $content;
        $php .= "<?php }}?>";

        return $php;
    }

    //标签处理
    public function _foreach($attr, $content)
    {
        $php
            = "<?php foreach ({$attr['from']} as {$attr['key']}=>{$attr['value']}){?>";
        $php .= $content;
        $php .= '<?php }?>';

        return $php;
    }

    //加载模板文件
    public function _include($attr, $content, &$hd)
    {
        //替换常量
        $const = get_defined_constants(true);
        foreach ($const['user'] as $k => $v) {
            $attr['file'] = str_replace($k, $v, $attr['file']);
        }
        //删除域名
        $file = str_replace(__ROOT__ . '/', '', trim($attr['file']));
        $view = new ViewHd();
        $view->fetch($file);

        return file_get_contents($view->compileFile);
    }

    //if标签
    public function _if($attr, $content, &$hd)
    {
        $php
            = <<<php
    <?php if({$attr['value']}){ ?>$content<?php } ?>
php;

        return $php;
    }

    //elseif标签
    public function _elseif($attr, $content, &$hd)
    {
        $php = "<?php }else if({$attr['value']}){ ?>";

        return $php;
    }

    //else标签
    public function _else($attr, $content, &$hd)
    {
        return "<?php }else{ ?>";
    }

    //empty标签
    public function _empty($attr, $content, $res)
    {
        $php = "<?php if (empty({$attr['value']})){ ?>";
        $php .= $content;
        $php .= '<?php } ?>';

        return $php;
    }

    //noempty标签
    public function _noempty($attr, $content)
    {
        return '<?php }else{ ?>';
    }

    //将URL常量定义为JS变量
    public function _jsconst($attr, $content, &$hd)
    {
        //所有常量
        $const = get_defined_constants(true);
        //查找所以以http开始的常量
        $arr = preg_grep("/^http/i", $const['user']);
        $str
             = "<script type='text/javascript'>\n";
        foreach ($arr as $k => $v) {
            $k = str_replace('__', '', $k);
            $str .= $k . " = '$v';\n";
        }
        $str .= "</script>";

        return $str;
    }
}