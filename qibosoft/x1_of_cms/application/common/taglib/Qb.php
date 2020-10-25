<?php
namespace app\common\taglib;

use think\template\TagLib;
class Qb extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
            'tag'      => ['attr' => 'name,type,time,rows,val,list,tpl,order,by,status,class,where,whereor,sql,mid,fid,js,union,field,conf', 'close' => 1],  //field 过滤循环不显示的字段,多个用,号隔开
            'hy'      => ['attr' => 'name,type,time,rows,val,list,tpl,order,by,status,class,where,whereor,sql,mid,fid,js,union,field,conf', 'close' => 1],  //圈子黄页店铺专用标签
            'url'      => ['attr' => 'name', 'close' => 0],
            'hy_url'      => ['attr' => 'name', 'close' => 0],
            'nav'      => ['attr' => 'name,title,url', 'close' => 0],
            'listpage'      => ['attr' => 'name,time,rows,val,list,order,by,tpl,status,where,field', 'close' => 1],  //field 过滤不显示的字段,多个用,号隔开
            'list_url'      => ['attr' => 'name', 'close' => 0],
            'showpage'      => ['attr' => 'name,time,type,tpl,val,field,f_array', 'close' => 1],    //field 过滤循环不显示的字段,多个用,号隔开 ,f_array是程序中自由定义的字段 
            'comment'      => ['attr' => 'name,time,rows,list,order,by,status,tpl,aid,sysid,where', 'close' => 1],  //这个是评论插件
            'reply'      => ['attr' => 'name,time,rows,list,order,by,status,tpl,aid,where', 'close' => 1],    //这个是论坛的回复,功能跟评论插件没太大区别
            'form'  => ['attr' => 'name,info,mid,field,mod,f_array', 'close' => 1],     //field 过滤循环不显示的字段,多个用,号隔开,f_array是程序中自由定义的字段
            'table' => ['attr' => 'name,listdb,mid,field,mod,f_array', 'close' => 1],   //后台列表 mod频道目录名
    ];
    
    /**
     * 面包屑导航
     * @param unknown $tag 可以定义一个链接
     * @return string
     */
    public function tagNav($tag)
    {
        $name = $tag['name'];
        $title = $tag['title'];
        $url = $tag['url'];
        $parse = '<?php '."fun('page@getNavigation','$title','$url',\$fid);".' ?>';
        return $parse;
    }
    
    /**
     * 通用标签AJAX获取更多页的地址
     * @param unknown $tag 标签名
     * @return string
     */
    public function tagUrl($tag)
    {
        $name = $tag['name'];
        $parse = '<?php fun("label@label_ajax_url","' . $name .'",__FILE__); ?>';
        return $parse;
    }
    
    /**
     * 圈子黄页标签AJAX获取更多页的地址
     * @param unknown $tag 标签名
     * @return string
     */
    public function tagHy_url($tag)
    {
        $name = $tag['name'];
        $parse = '<?php fun("label@label_hy_ajax_url","' . $name .'",__FILE__); ?>';
        return $parse;
    }
    
    /**
     * 列表页标签,获取更多页的地址
     * @param unknown $tag 标签名
     * @return string
     */
    public function tagList_url($tag)
    {
        $name = $tag['name'];
        $parse = '<?php '."fun('label@label_listpage_ajax_url','$name');".' ?>';
        return $parse;
    }
    
    /**
     * 通用标签
     * @param unknown $tag 标签名
     * @param unknown $content 各项参数
     * @return string
     */
    public function tagTag($tag, $content){     //$content 的内容就是 <!--###break###--!>
        if(empty($tag['name'])){
            return '******标签缺少命名*******'.$content;
        }
        $sql = $tag['sql'];   //SQL查询
        $type = $sql?'sql':$tag['type'];
        $name = $this->getName($tag['name']);
        if(preg_match('/^([\d]+),([\d]+)$/', $tag['rows'])){
            $rows = $tag['rows'];
        }else{
            $rows = intval($tag['rows']);
        }
        if( empty($rows) ){
            $rows = 5;  //取数据库的多少条记录
        }
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $val = $tag['val'];
        $conf = $this->get_conf($tag['conf']);
        $order = $tag['order']; //按什么排序
        //$rows = $tag['rows'];   //取数据库的多少条记录
        $by = $tag['by'];   //升序还是降序
        $status = empty($tag['status']) ? '' : "'status'=>'{$tag['status']}',";   //审核或推荐
        $where = $tag['where'];   //条件查询
        $mid = $tag['mid'];   //指定模型
        $fid = $tag['fid'];   //指定栏目
        $str_mid = $mid ? ",'mid'=>'$mid'" : '';
        $str_fid = $fid ? ",'fid'=>'$fid'" : '';
        $whereor = $tag['whereor'];   //条件查询
        $class = $tag['class']; //调取数据执行的类
        $tpl = $tag['tpl']; //指定默认模板
        $js = $tag['js']; //通过AJAX方式获取数据,这样就不影响页面打开速度
        $union = $this->union_live_parameter($tag['union'],$where);    //动态关联的参数
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--QB '."<!--$name\t$type\t$tpl-->";
        if(!empty($val)){   //只取得变量值的情况
            $parse .= $content;
        }elseif($type=='text'||$type=='image'||$type=='textarea'||$type=='ueditor'||$type=='link'){
            $parse .= $content;
        }else{
            $parse .= '{volist name="__LIST__" id="' . $list . '"}';
            $parse .= $content.'  ';
            $parse .= '{/volist}';
        }
        $parse .= ' QB--><?php endif; ?>';
        $where = addslashes($where);
        $whereor = addslashes($whereor);
        $parse .= '<?php '."\$$name = fun('label@run_label','$name',[$union'val'=>'$val',$conf'list'=>'$list','systype'=>'$type','tpl'=>'$tpl','ifdata'=>1,'dirname'=>__FILE__,'rows'=>'$rows','class'=>'$class','order'=>'$order','by'=>'$by',$status'where'=>'$where','whereor'=>'$whereor','sql'=>\"$sql\",'js'=>'$js','cache_time'=>'$cache_time' $str_mid $str_fid]);".' ?>';
        return $parse;
    }
    
    /**
     * 圈子黄页店铺专用标签
     * @param unknown $tag
     * @param unknown $content
     * @return string
     */
    public function tagHy($tag, $content){     //$content 的内容就是 <!--###break###--!>
        if(empty($tag['name'])){
            return '******标签缺少命名*******'.$content;
        }
        if(empty($tag['type'])){
            //return '******标签type参数不能为空*******'.$content;
        }
        $conf = $this->get_conf($tag['conf']);
        $sql = $tag['sql'];   //SQL查询
        $type = $sql?'sql':$tag['type'];
        $name = $this->getName($tag['name']);
        if(preg_match('/^([\d]+),([\d]+)$/', $tag['rows'])){
            $rows = $tag['rows'];
        }else{
            $rows = intval($tag['rows']);
        }
        if( empty($rows) ){
            $rows = 5;  //取数据库的多少条记录
        }
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $val = $tag['val'];
        $order = $tag['order']; //按什么排序
        //$rows = $tag['rows'];   //取数据库的多少条记录
        $by = $tag['by'];   //升序还是降序
        $status = empty($tag['status']) ? '' : "'status'=>'{$tag['status']}',";   //审核或推荐
        $where = $tag['where'];   //条件查询
        $mid = $tag['mid'];   //指定模型
        $fid = $tag['fid'];   //指定栏目
        $str_mid = $mid ? ",'mid'=>'$mid'" : '';
        $str_fid = $fid ? ",'fid'=>'$fid'" : '';
        $whereor = $tag['whereor'];   //条件查询
        $class = $tag['class']; //调取数据执行的类
        $tpl = $tag['tpl']; //指定默认模板
        $js = $tag['js']; //通过AJAX方式获取数据,这样就不影响页面打开速度
        if ($tag['class']==""&&$tag['type']&&$tag['type']!='labelmodel'&&config('system_dirname')=='qun') {
            $tag['union']=$tag['union']?$tag['union'].',uid=$info.uid,ext_id=$info.id':'uid=$info.uid,ext_id=$info.id'; //给圈子传变量
        }
        $union = $this->union_live_parameter($tag['union'],$where);    //动态关联的参数
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--QB '."<!--$name\t$type\t$tpl-->";
        if(!empty($val)){   //只取得变量值的情况
            $parse .= $content;
        }elseif($type=='text'||$type=='image'||$type=='textarea'||$type=='ueditor'||$type=='link'){
            $parse .= $content;
        }else{
            $parse .= '{volist name="__LIST__" id="' . $list . '"}';
            $parse .= $content.'  ';
            $parse .= '{/volist}';
        }
        $parse .= ' QB--><?php endif; ?>';
        $where = addslashes($where);
        $whereor = addslashes($whereor);
        $_info =$tag['type']=='labelmodel'?"'Info'=>\$info,'Id'=>\$id,'Fid'=>\$fid,'Mid'=>\$mid,":''; //传递缓存
        $parse .= '<?php '."\$$name = fun('label@run_hy','$name',[$union $_info 'hy_id'=>intval(\$info['id']&&config('system_dirname')=='qun'?\$info['id']:\$hy_id),'hy_tags'=>\$tags,'val'=>'$val',$conf'list'=>'$list','systype'=>'$type','tpl'=>'$tpl','ifdata'=>1,'dirname'=>__FILE__,'rows'=>'$rows','class'=>'$class','order'=>'$order','by'=>'$by',$status'where'=>'$where','whereor'=>'$whereor','sql'=>\"$sql\",'js'=>'$js','cache_time'=>'$cache_time' $str_mid $str_fid]);".' ?>';
        return $parse;
    }
    
    /**
     * 自定义表单标签
     * @param unknown $tag
     * @param unknown $content
     * @return string
     */
    public function tagForm($tag,$content){         //$content 的内容就是 <!--###break###--!>
        $name = $this->getName($tag['name']);
        $mid = $tag['mid'] ? ($tag['mid'][0]=='$'?substr($tag['mid'], 1):$tag['mid']) : 'mid';     //型模id变量名
        $_mid = is_numeric($mid) ? $mid : "\$$mid";
        $info = $tag['info'] ? ($tag['info'][0]=='$'?substr($tag['info'], 1):$tag['mid']) : 'info';     //内容信息变量名
        $mod = $tag['mod'];     //模块
        $field = $tag['field'];     //过滤的字段
        $_farray = $tag['f_array'] ? "'f_array'=>\$".($tag['f_array'][0]=='$'?substr($tag['f_array'], 1):$tag['f_array']).',' : '';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--QB '."<!--$name\t$mod--> ";
        $parse .= '{volist name="__LIST__" id="rs"}';
        $parse .= '{if $rs["ifhide"]}';
        $parse .= ' {$rs.value} ';
        $parse .= '{else /}';
        $parse .= $content.'  ';
        $parse .= '{/if}';
        $parse .= '{/volist}';
        $parse .= ' QB--><?php endif; ?>';
        $parse .= '<?php '."fun('label@run_form_label','$name',[$_farray'mid'=>$_mid,'info'=>\$$info,'field'=>'$field','mod'=>'$mod','dirname'=>__FILE__,]);".' ?>';
        return $parse;
    }
    
    
    private function get_conf($conf=''){
        if(substr($conf,0,1)=='$'){
            if(strstr($conf,'.')){
                $conf = str_replace('.','[\'',$conf).'\']';
            }
            $conf='\\'.$conf;
        }else{
            $conf="'{$conf}'";
        }
        return "'conf'=>".$conf.',';
    }
    
    /**
     * 关联动态变量
     * @param unknown $str
     * @return void|string
     */
    private function union_live_parameter($str='',$where=''){
        if(empty($str) && empty($where)){
            return ;
        }
        $str = str_replace('&', ',', $str);
        //if (empty($str) && $where!='') {
        if ($where!='') {
            $_str = fun('label@get_union',$where);
            if (empty($str)&&$_str) {
                $str = $_str;
            }elseif($_str){
                $str .= ",$_str";
            }
            if (empty($str)) {
                return ;
            }
        }
        $_str = '';
        $_par = [];
        $detail = explode(',',$str);
        foreach ($detail AS $value){
            if ($value=='') {
                continue;
            }
            list($a,$b) = explode('=',$value);
            $_par[] = $a;
            if (empty($b)) {
                $b=$a;
            }elseif(substr($b,0,1)=='$'){
                if(strstr($b,'.')){
                    $b = str_replace('.','[\'',$b).'\']';
                }
                $b = substr($b,1);
            }
            $_str .= "'$a'=>\$$b,";
        }
        return "'union'=>'".implode(',',$_par)."',".$_str;
    }
    
    /**
     * 每个标签的变量名
     * @param string $name
     * @return string
     */
    private function getName($name=''){
        $name = preg_match('/^([\w]+)$/',$name) ? $name : md5($name);
        $name = preg_match('/^([_a-z])/i',$name) ? $name : 'qb'.substr($name,2);
        return $name;
    }
    
    /**
     * 评论插件,给各个频道调用的评论接口
     * @param unknown $tag 标签名
     * @param unknown $content 各项参数
     * @return string
     */
    public function tagComment($tag='', $content='')
    {
        if(empty($tag['name'])){
            return '******标签缺少命名*******'.$content;
        }
        $val_info = empty($val_info )?'info':$val_info; //模块内容的变量名，比如文章系统常用 info 或 rsdb
        $sysid = empty($sysid)?'sysid':$sysid;  //模块系统ID变量名
        $aid = empty($aid)?'id':$aid;   //内容变量名
        $status = $tag['status'];   //审核或推荐
        $order = $tag['order']; //按什么排序
        $rows = $tag['rows'];   //取数据库的多少条记录
        $by = $tag['by'];   //升序还是降序
        $where = $tag['where'];   //条件查询
        //$class = $tag['class']; //调取数据执行的类
        $tpl = $tag['tpl'];
        $type = $tag['type'];
        $name = $this->getName($tag['name']);
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--COMMENT'."<!--$name\t$type\t$tpl-->";
        $parse .= $content;
        $parse .= ' COMMENT--><?php endif; ?>';
        $where = addslashes($where);
        //$whereor = addslashes($whereor);
        $parse .= '<?php '."fun('label@run_comment_label','$name',\$$val_info,['sysid'=>\$$sysid,'aid'=>\$$aid,'status'=>'$status','dirname'=>__FILE__,'tpl'=>'$tpl','cache_time'=>'$cache_time','rows'=>'$rows','where'=>'$where','order'=>'$order','by'=>'$by']);".' ?>';
        return $parse;
    }
    
    /**
     * 论坛回复标签,跟评论插件类似
     * @param unknown $tag
     * @param unknown $content
     * @return string
     */
    public function tagReply($tag, $content)
    {
        if(empty($tag['name'])){
            return '******标签缺少命名*******'.$content;
        }
        $val_info = empty($val_info )?'info':$val_info; //模块内容的变量名，比如文章系统常用 info 或 rsdb
        $aid = empty($aid)?'id':$aid;   //内容变量名
        $status = $tag['status'];   //审核或推荐
        $order = $tag['order']; //按什么排序
        $rows = $tag['rows'];   //取数据库的多少条记录
        $by = $tag['by'];   //升序还是降序
        $where = $tag['where'];   //条件查询
        //$class = $tag['class']; //调取数据执行的类
        $tpl = $tag['tpl'];
        $type = $tag['type'];
        $name = $this->getName($tag['name']);
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--REPLY'."<!--$name\t$type\t$tpl-->";
        $parse .= $content;
        $parse .= ' REPLY--><?php endif; ?>';
        $where = addslashes($where);
        //$whereor = addslashes($whereor);
        $parse .= '<?php '."reply_label('$name',\$$val_info,['aid'=>\$$aid,'status'=>'$status','dirname'=>__FILE__,'tpl'=>'$tpl','cache_time'=>'$cache_time','rows'=>'$rows','where'=>'$where','order'=>'$order','by'=>'$by']);".' ?>';
        return $parse;
    }
    
    /**
     * 内容页标签,这个标签用的并不多
     * @param unknown $tag 标签名
     * @param unknown $content
     * @return string
     */
    public function tagShowpage($tag, $content)
    {
        if(empty($tag['name'])){
            // return '******标签缺少命名*******'.$content;
        }
        $type = $tag['type'];
        $tpl = $tag['tpl'];
        $field = $tag['field'];     //过滤循环的字段
        $val = $tag['val']?$tag['val']:'info';
        $name = $this->getName($tag['name']);
        $rows = empty($tag['rows']) ?10: intval($tag['rows']);   //取数据库的多少条记录
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $order = empty($order)?'id':$order;
        $by = empty($by)?'desc':$by;
        $_farray = $tag['f_array'] ? "'f_array'=>\$".($tag['f_array'][0]=='$'?substr($tag['f_array'], 1):$tag['f_array']).',' : '';
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--SHOWPAGE '."<!--$name\t$type\t$tpl-->";
        if(($tag['field']||$tag['f_array'])&&empty($tag['val'])){
            $parse .= '{volist name="'.$val.'" id="rs"}';
            $parse .= $content.'  ';
            $parse .= '{/volist}';
        }else{
            $parse .= $content;
        }
        $parse .= ' SHOWPAGE--><?php endif; ?>';
        $parse .= '<?php '."fun('label@run_showpage_label','$name',\$info,[$_farray'page'=>\$page,'dirname'=>__FILE__,'tpl'=>'$tpl','field'=>'$field','val'=>'$val','cache_time'=>'$cache_time']);".' ?>';
        return $parse;
    }
    
    /**
     * 列表页标签
     * @param unknown $tag 标签名
     * @param unknown $content
     * @return string
     */
    public function tagListpage($tag, $content)
    {
        if(empty($tag['name'])){
            return '******标签缺少命名*******'.$content;
        }
        $type = $tag['type'];
        $field = $tag['field'];     //过滤循环的字段
        $val = $tag['val'];
        $tpl = $tag['tpl'];
        $by = $tag['by'];
        $order = $tag['order'];
        $name = $this->getName($tag['name']);
        $rows = empty($tag['rows']) ?10: intval($tag['rows']);   //取数据库的多少条记录
        $cache_time = empty($tag['time']) ?0: intval($tag['time']);
        $order = empty($order)?'id':$order;
        $by = empty($by)?'desc':$by;
        $status = $tag['status'];   //审核或推荐
        $where = $tag['where'];   //条件查询
        $union = $this->union_live_parameter($tag['union'],$where);    //动态关联的参数
        $list = $tag['list']?$tag['list']:'rs';
        $parse = '<?php if(defined(\'LABEL_DEBUG\')): ?><!--LISTPAGE '."<!--$name\t$type\t$tpl-->";
        if(!empty($val)){   //只取得变量值的情况
            $parse .= $content;
        }else{
            $parse .= '{volist name="__LIST__" id="' . $list . '"}';
            $parse .= $content.'  ';
            $parse .= '{/volist}';
        }
        $parse .= ' LISTPAGE--><?php endif; ?>';
        $where = addslashes($where);
        //$whereor = addslashes($whereor);
        $parse .= '<?php $__array__='."fun('label@run_listpage_label','$name',[$union'mid'=>\$mid,'fid'=>\$fid,'page'=>\$page,'dirname'=>__FILE__,'field'=>'$field','val'=>'$val','tpl'=>'$tpl','rows'=>'$rows','where'=>'$where','status'=>'$status','order'=>'$order','by'=>'$by','cache_time'=>'$cache_time']);";
        $parse .='$pages=$__array__[\'pages\'];$'.$name.'=$__array__[\'cfg\']; ?>';
        return $parse;
    }
    
}