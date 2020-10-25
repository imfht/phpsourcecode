<?php

namespace About\Model;

use Think\Model;

class AboutCategoryModel extends Model{

    protected $_auto = array(
        array('status', '1', self::MODEL_INSERT),
    );


    /**
     * 获取分类详细信息
     * @param $id
     * @param bool $field
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = array();
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }

    public function editData()
    {
        $data=$this->create();
        if($data['id']){
            $res=$this->save($data);
        }else{
            $res=$this->add($data);
        }
        return $res;
    }

    public function getCategoryList($map)
    {
        $list=$this->where($map)->field('id,title,sort,status')->order('sort asc')->select();
        return $list;
    }

    public function getCategory($map)
    {
        $res=$this->where($map)->field('id,title,sort,status')->find();
        return $res;
    }

} 