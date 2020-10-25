<?php
/**
* tree 类
* @param array 2维数组，例如：
* array(
*      1 => array('id'=>'1','parent_id'=>0,'name'=>'一级栏目一'),
*      2 => array('id'=>'2','parent_id'=>0,'name'=>'一级栏目二'),
*      3 => array('id'=>'3','parent_id'=>1,'name'=>'二级栏目一'),
*      4 => array('id'=>'4','parent_id'=>1,'name'=>'二级栏目二'),
*      5 => array('id'=>'5','parent_id'=>2,'name'=>'二级栏目三'),
*      6 => array('id'=>'6','parent_id'=>3,'name'=>'三级栏目一'),
*      7 => array('id'=>'7','parent_id'=>3,'name'=>'三级栏目二')
*      )
*/
namespace framework\libraries;

class Tree
{
    
    private $config=array(
        // 生成树型结构所需修饰符号，可以换成图片
        'icon'      => array('│','├','└'),
        'nbsp'      => "&nbsp;",
        'id'        =>'id',
        'parent_id' =>'parent_id',
        'title'     =>'title',
        'select'    =>'selected'  //selected,checked
    );
    private $arr = array();
    private $str ='';
    public function __construct(&$arr=array(),$config=array()){
        if(is_array($arr))
            $this->arr =$arr;
        if(is_array($config))
            $this->config =array_merge($this->config,$config);
        $this->str='';
    }
    public function __get($name){
        if(isset($this->config[$name]))
            return $this->config[$name];
        return NULL;
    }
    public function __set($name,$value){
        if(array_key_exists($name,$this->config))
            $this->config[$name] = $value;
        
    }
    public function __isset($name){
        return isset($this->config[$name]);
    }
    /**
     * 获取子数组
     */
    public function get_child($parent_id){
        $child = array();
        if(is_array($this->arr)){
            foreach($this->arr as $val){
                if($val[$this->parent_id]==$parent_id){
                   $child[$val[$this->id]]= $val;
                }
            }
        }
        //print_r($child);
        return $child?$child:false;
    }

    /**
     * 得到树型结构
     * @param int $parent_id ID，表示获得这个ID下的所有子级
     * @param string $rule 生成树型结构的基本代码，例如："<option value=\$id \$selected>\$spacer\$name</option>"
     * @param int|string $select_id 被选中的ID，比如在做树型下拉框的时候需要用到
     * @param string $indentation
     * @return string
     * @internal param $
     */
    public function get_tree($parent_id=0, $rule='', &$select_id = '', $indentation = ''){
        $n =1;
        $child =$this->get_child($parent_id);
        if(is_array($child)){
            $total =count($child);
            $_id =$this->id;
            foreach($child as $$_id=>$val){
                $i = $j = '';
                if($n==$total){
                    $j .=$this->icon[2];  
                }else{
                    $j .= $this->icon[1];
                    $i = $indentation?$this->icon[0]:'';
                }
                $spacer = $indentation?$indentation.$j:'';
                if(is_array($select_id)){
                    $selected   = in_array($$_id, $select_id)? $this->select : '';
                }else{
                    $selected   = ($$_id==$select_id)? $this->select : '';
                }
                @extract($val);
                eval("\$nstr = \"$rule\";");
                $this->str .= $nstr;
                $nbsp=$this->nbsp;
                $this->get_tree($val[$this->id], $rule, $select_id,$indentation.$i.$nbsp);
                $n++;
            }
        }
        return $this->str;
    }
}