<?php
    function p($array)
    {
       dump($array,1,'<pre>',0); 
    }
    
    //格式化cateory元素(加url)
    function formatCat($data)
    {
        $Category=array();
        foreach ($data as $v)
        {   
            if(C('URL_MODEL')==0){
                $Category[]=array_merge($v,array('url'=>U('Wenbon/a',array('cid'=>$v['id']))));
            }
            else{
                if($v['urlname'])
                    $Category[]=array_merge($v,array('url'=>U('/cat/'.$v['urlname'])));
                else
                    $Category[]=array_merge($v,array('url'=>U('/cat/'.$v['id'])));
            }
        }
        return $Category;
    }
    //格式化commod(加url/合并user,member字段)
    function formatCon($data){
        $mod=array();
        foreach ($data as $v){
            if(C('URL_MODEL')==0){
                $v['url']=U('Wenbon/a',array('t'=>$v['module'],'id'=>$v['id']));
            }
            else{
                $v['url']=U('/'.$v['module'].'/'.$v['id']);
            }
            if($v['member']!=null)
            {
                $v['user']=$v['member'];
                unset($v['member']);
            }else{
                unset($v['member']);
            }
            $mod[]=$v;
        }
        return $mod;
    }
    //组合多维数组
    function node_merge($node,$access=null,$pid=0){
            $arr=array();
            foreach($node as $v){
                if(is_array($access)){
                    $v['access']=in_array($v['id'],$access)?1:0;
                }
                if($v['pid']==$pid){
                    $v['child']=node_merge($node,$access,$v['id']);
                    $arr[]=$v;
                }
        }
        return $arr;
    }
    //查找所有父级分类(一维数组，排序:高->低)
    function getparents($node,$id){
        $arr=array();
        foreach($node as $v)
        {
            if($v[id]==$id){
                $arr[]=$v;
                $arr=array_merge(getparents($node,$v['pid']),$arr);
            }
        }
        return $arr;
    }
    //查找所有后代分类id
    function getchidsid($node,$pid){
        $arr=array();
        foreach($node as $v){
            if($v['pid']==$pid){
                $arr[]=$v['id'];
                $arr=array_merge($arr,getchidsid($node,$v['id']));
            }
        }
        return $arr;
    }
    //查找所有后代分类
    function getchids($node,$pid){
        $arr=array();
        foreach($node as $v){
            if($v['pid']==$pid){
                $arr[]=$v;
                $arr=array_merge($arr,getchids($node,$v['id']));
            }
        }
        return $arr;
    }
    //字段9专用函数
    function selecttohtml($vl,$name,$nowval){//所有选项，select name,当前选中项
        $str='<select name="'.$name.'">';
        $fval=explode(',',$vl);
        foreach ($fval as $value){
            $tem=explode('|',$value);
            $selected='';
            if($nowval==$tem[1]){
                $selected="selected=selected";
            }
            $str=$str."<option ".$selected." value='".$tem[1]."'>".$tem[0]."</option>";
        }
        $str=$str."</select>";
        return $str;
    }
?>