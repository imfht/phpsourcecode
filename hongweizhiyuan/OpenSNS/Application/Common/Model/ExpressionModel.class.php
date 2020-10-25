<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-10
 * Time: PM9:01
 */

namespace Common\Model;

use Think\Model;

class ExpressionModel extends Model
{
    public $pkg = '';
    public  function _initialize()
    {
        parent:: _initialize();
        $this->pkg = modC('EXPRESSION','miniblog','EXPRESSION');
    }

    /**
     * 获取当前主题包下所有的表情
     * @param boolean $flush 是否更新缓存，默认为false
     * @return array 返回表情数据
     */
    public function getAllExpression()
    {
        define('ROOT_PATH', str_replace('/Application/Common/Model/ExpressionModel.class.php', '', str_replace('\\', '/', __FILE__)));
        $pkg = $this->pkg; //TODO 临时写死

        if($pkg =='all'){
            return $this->getAll();
        }else{
            return $this->getExpression($pkg);
        }

    }



    public function getExpression($pkg){
        define('ROOT_PATH', str_replace('/Application/Common/Model/ExpressionModel.class.php', '', str_replace('\\', '/', __FILE__)));
        if($pkg == 'miniblog'){
            $filepath =  "/Public/static/image/expression/" . $pkg;
        }else{
            $filepath =  "/Uploads/expression/" . $pkg;
        }
        $list = $this->myreaddir(ROOT_PATH.$filepath);
        $res = array();
        foreach ($list as $value) {
            $file = explode(".", $value);
            $temp['title'] = $file[0];
            $temp['emotion'] = $pkg=='miniblog'?'['.$file[0].']': '[' . $file[0] . ':' . $pkg . ']';
            $temp['filename'] = $value;
            $temp['type'] = $pkg;
            $temp['src'] = __ROOT__ . $filepath . '/' . $value;
            $res[$temp['emotion']] = $temp;
        }

        return $res;
    }

    /**
     * getAll 获取所有主题的所有表情
     * @return array
     * @author:xjw129xjt xjt@ourstu.com
     */
    public function getAll()
    {
        define('ROOT_PATH', str_replace('/Application/Common/Model/ExpressionModel.class.php', '', str_replace('\\', '/', __FILE__)));

        $res = $this->getExpression('miniblog');

        $ExpressionPkg = ROOT_PATH . "/Uploads/expression";
        $pkgList = $this->myreaddir($ExpressionPkg);
        foreach ($pkgList as $v) {
            $res =array_merge($res,$this->getExpression($v));
        }
        return $res;
    }

    public function myreaddir($dir)
    {
        $file = scandir($dir, 0);
        $i = 0;
        foreach ($file as $v) {
            if (($v != ".") and ($v != "..")) {
                $list[$i] = $v;
                $i = $i + 1;
            }
        }
        return $list;
    }


    /**
     * 将表情格式化成HTML形式
     * @param string $data 内容数据
     * @return string 转换为表情链接的内容
     */
    public function parse($data)
    {
        $data = preg_replace("/img{data=([^}]*)}/", "<img src='$1'  data='$1' >", $data);
        return $data;
    }


    public function getCount($dir){
        $list = $this->myreaddir($dir);
        return count($list);
    }
}















