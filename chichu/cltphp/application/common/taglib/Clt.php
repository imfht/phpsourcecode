<?php
namespace app\common\taglib;
use think\template\TagLib;
class Clt extends TagLib {
    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'close'     => ['attr' => 'time,format', 'close' => 0], //闭合标签，默认为不闭合
        'open'      => ['attr' => 'name,type', 'close' => 1],

        'tinfo' => array('attr' => 'db,where,id','close' => 1),
        'tfield' => array('attr' => 'db,where,name','close' => 0),
        'clist'=> array('attr' => 'db,order,limit,where,id,key','close' => 1),
        'tlist' => array('attr' => 'db,order,limit,where,id,key','close' => 1),
    );
    /**
     * 这是一个闭合标签的简单演示
     */
    public function tagClose($tag)
    {
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("' . $format . '",' . $time . ');';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagOpen($tag, $content)
    {
        $type = empty($tag['type']) ? 0 : 1; // 这个type目的是为了区分类型，一般来源是数据库
        $name = $tag['name']; // name是必填项，这里不做判断了
        $parse = '<?php ';
        $parse .= '$test_arr=[[1,3,5,7,9],[2,4,6,8,10]];'; // 这里是模拟数据
        $parse .= '$__LIST__ = $test_arr[' . $type . '];';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    /**
     * 根据条件查询字段
     * @param $tag
     * @return string
     */
    public function tagTfield($tag){
        $db = $tag['db']; //要查询的数据表
        $where = isset($tag['where'])?$tag['where']:'';//查询条件
        $name = $tag['name'];
        $str = '<?php ';
        $str .= 'echo db("' . $db . '")->where("' . $where . '")->value("'.$name.'");';
        $str .= '?>';
        return $str;
    }

    /**
     * 根据条件查询一条数据
     * @param $attr
     * @param $content
     * @return string
     */
    public function tagTinfo($attr,$content){
        $db = $attr['db']; //要查询的数据表
        $where = isset($attr['where'])?$attr['where']:'';; //查询条件
        $id = $attr['id'];
        $str = '<?php ';
        $str .= '$'.$id.' =db("' . $db . '")->where("' . $where . '")->find();';
        $str .= '?>';
        $str .= $content;
        return $str;
    }
    public function tagClist($attr,$content) {
        $db = $attr['db']; //要查询的数据表
        $order = isset($attr['order'])?$attr['order']:' a.sort asc,a.createtime desc,a.id desc';    //排序
        $limit = isset($attr['limit'])?$attr['limit']:'15'; //多少条数据
        $where = isset($attr['where'])?$attr['where'].' and (status = 1 or (status = 0 and createtime <'.time().'))':' status = 1 or (status = 0 and createtime <'.time().') '; //查询条件
        $id = $attr['id'];
        $key = isset($attr['key'])?$attr['key']:'k';
        $str = '<?php ';
        $str.='$result = db("'.$db.'")->alias("a")->join("category c"," a.catid = c.id","left")
            ->where("'.$where.'")
            ->field("a.*,c.catdir,c.catname")
            ->limit('.$limit.')
            ->order("'.$order.'")
            ->select();';
        $str .= 'if($result){';
        $str .= 'foreach ($result as $'.$key.'=>$'.$id.'):';
        $str .= '$result[$'.$key.']["time"]= toDate($'.$id.'["createtime"],"Y-m-d");';
        $str .= '$result[$'.$key.']["thumb"]= $'.$id.'["thumb"]?$'.$id.'["thumb"]:"";';
        $str .= '?>';
        $str .= '<?php endforeach; ?>';
        $str .= '<?php ';
        $str .= 'foreach ($result as $'.$key.'=>$'.$id.'):';
        $str .= '?>';
        $str .= $content;
        $str .= '<?php endforeach; ?>';
        $str .= '<?php }else{echo "<div class=\'fly-none\'>没有相关数据</div>";}?>';
        /*$str .= '<?php dump($result);?>';*/
        return $str;
    }

    public function tagTlist($attr,$content) {
        $db = $attr['db']; //要查询的数据表
        $order = isset($attr['order'])?$attr['order']:'';    //排序
        $limit = isset($attr['limit'])?$attr['limit']:''; //多少条数据
        $where = isset($attr['where'])?$attr['where']:''; //查询条件
        $id = $attr['id'];
        $key = isset($attr['key'])?$attr['key']:'k';
        $str = '<?php ';
        $str.='$result = db("'.$db.'")->where("'.$where.'")->limit('.$limit.')->order("'.$order.'")->select();';

        $str .= 'foreach ($result as $'.$key.'=>$'.$id.'):';
        $str .='$result[$'.$key.']["time"]= isset($'.$id.'["createtime"])?toDate($'.$id.'["createtime"]):""';
        $str .= '?>';
        $str .= $content;
        $str .= '<?php endforeach?>';
        return $str;
    }



}