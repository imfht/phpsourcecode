<?php
namespace app\ucenter\Model;

use think\Model;
use think\Db;

class UserTagLink extends Model
{
    /**
     * 获取用户标签列表
     * @param int $uid
     * @return null
     */
    public function getUserTag($uid=0)
    {
        !$uid&&$uid=is_login()?is_login():session('temp_login_uid');
        $tag_ids=$this->where(['uid'=>$uid])->value('tags');
        if($tag_ids!=''){
            $tag_ids=str_replace('[','',$tag_ids);
            $tag_ids=str_replace(']','',$tag_ids);
            $tag_ids=explode(',',$tag_ids);
            $tags=Db::name('UserTag')->where(array('id'=>array('in',$tag_ids),'status'=>1,'pid'=>array('neq',0)))->order('sort desc')->select();
            if(count($tags)){
                return $tags;
            }
        }
        return null;
    }

    /**
     * 编辑用户标签链接
     * @param string $tags
     * @return bool|mixed|null
     */
    public function editData($tags='')
    {
        $uid=is_login()?is_login():session('temp_login_uid');
        if($tags!=''){
            $tags=explode(',',$tags);
            sort($tags);
            foreach($tags as &$tag){
                $tag='['.$tag.']';
            }
            unset($tag);
            $tags=implode(',',$tags);
            if($this->where(['uid'=>$uid])->count()){
                $result=$this->saveData($tags);
            }else{
                $result=$this->addData($tags);
            }
        }else{
            $result=$this->where(['uid'=>$uid])->delete();
        }
        return $result;
    }

    public function saveData($tags='')
    {
        $uid=is_login()?is_login():session('temp_login_uid');
        $result=$this->where(array('uid'=>$uid))->setField('tags',$tags);
        return $result;
    }

    public function addData($tags='')
    {
        $uid=is_login()?is_login():session('temp_login_uid');
        $data['tags']=$tags;
        $data['uid']=$uid;
        $result=$this->save($data);
        return $result;
    }

    public function getListByMap($map)
    {
        $list=$this->where($map)->limit(999)->select();
        !count($list)&&$list=array();
        return $list;
    }

} 