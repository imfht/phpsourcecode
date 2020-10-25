<?php
namespace Home\Model;

use Think\Model;
class TagRelationModel extends Model
{
    public function getObjectIdsByTagId($id = 0){
        if(empty($id)){
            return ;
        }
        $where['tag_id'] = $id;
        $where['type']   = 'article';
        $res = $this->field('object_id')->where($where)->select();
        $objectIds = array();
        foreach ($res as $row){
            $objectIds[] = $row['object_id'];
        }
        return $objectIds;
    }
}

?>