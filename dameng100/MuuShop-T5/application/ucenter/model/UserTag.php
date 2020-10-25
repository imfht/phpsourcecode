<?php
namespace app\ucenter\model;

use think\Model;

class UserTag extends Model {

    /**
     * 获得分类树
     * @param int $id
     * @param bool $field
     * @return array
     */
    public function getTree($id = 0, $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有分类 */
        $map  = array('status' => array('gt', -1));
        $list = $this->field($field)->where($map)->order('sort')->select();
        $list = collection($list)->toArray();

        //dump($list);exit;
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id);


        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }

    /**
     * 获得分类树型列表
     * @param int $id
     * @param bool $field
     * @return array
     */
    public function getTreeList($id = 0, $field = true){
        /* 获取当前分类信息 */
        if($id){
            $info = $this->info($id);
            $id   = $info['id'];
        }

        /* 获取所有分类 */
        $map  = array('status' => 1);
        $list = collection($this->field($field)->where($map)->order('sort')->select())->toArray();
        
        $list = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'tag_list', $root = $id);


        /* 获取返回数据 */
        if(isset($info)){ //指定分类则返回当前分类极其子分类
            $info['tag_list'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }
        return $info;
    }

    /**
     * 根据标签id列表获取标签列表树
     * @param string $ids
     * @param bool $field
     * @return array|null
     */
    public function getTreeListByIds($ids='',$field = true)
    {
        if($ids!=''){
            !is_array($ids)&&$ids=explode(',',$ids);
            $list_tags=$this->where(['id'=>['in',$ids],'status'=>1,'pid'=>['neq',0]])->field($field)->order('sort')->select();
            
            if(count($list_tags)){
                $cate_ids=array_column($list_tags,'pid');
                array_unique($cate_ids);
                $cate_list=$this->where(['id'=>['in',$cate_ids],'status'=>1,'pid'=>0])->field($field)->order('sort')->select();
                if(count($cate_list)){
                    $list=array_merge($list_tags,$cate_list);
                    $list=list_to_tree($list,$pk='id',$pid='pid',$child='tag_list');
                    return $list;
                }
            }
        }
        return null;
    }

    /**
     * 获取分类详细信息
     * @param $id
     * @param bool $field
     * @return mixed
     */
    public function info($id, $field = true){
        /* 获取分类信息 */
        $map = [];
        if(is_numeric($id)){ //通过ID查询
            $map['id'] = $id;
        } else { //通过标识查询
            $map['name'] = $id;
        }
        return $this->field($field)->where($map)->find();
    }
}