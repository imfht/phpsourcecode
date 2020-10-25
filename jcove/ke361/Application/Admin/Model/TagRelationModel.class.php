<?php
namespace Admin\Model;

use Think\Model;
class TagRelationModel extends Model
{
    public function update($id = 0){
        $tag = I('post.tag','');
        
        if(empty($id)){
            $id = I('id',0);
        }
        if(is_string($tag) && empty($tag)){
            $where['object_id'] = $id;
            $where['type'] = 'article';
            $this->where($where)->delete();
        }elseif (is_array($tag)) {
            $objectId = $id;
            $oldTags = get_tag_ids_by_article_id($id);//旧的标签
            $newTags = $tag;//新的标签
            $needAdd = array_diff($newTags, $oldTags);//需要添加标签集
            $needDel = array_diff($oldTags, $newTags);//需要删除的标签集
            
            $where['object_id'] = $objectId;
            $where['type'] = 'article';
            //添加新的标签
            
            foreach ($needAdd as $k=>$v){
                $where['tag_id']    = $v;
                $count = $this->where($where)->count();
                if($count <= 0){
                    $this->add($where);
                }
            }
            foreach ($needDel as $k=>$v){
                $where['tag_id']    = $v;
                $this->where($where)->delete();
            }
        } 
    }
}

?>