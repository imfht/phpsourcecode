<?php
namespace app\articles\model;
use think\Model;

class Articles extends Model {

	//自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    public function editData($data)
    {
    	if(!mb_strlen($data['description'],'utf-8')){
            $data['description'] = msubstr(text($data['content']),0,200);
        }
        if(!isset($data['uid'])) $data['uid'] = is_login();
        if(isset($data['template'])) $detail['template'] = $data['template'];
        
        $detail['content'] = $data['content'];
        
        if($data['id']){
        	$data['update_time'] = time();
            $res = $this->allowField(true)->save($data,$data['id']);
            $detail['articles_id'] = $data['id'];
        }else{
        	$data['create_time'] = $data['update_time'] = time();
            $res = $this->allowField(true)->save($data);
            $detail['articles_id'] = $this->id;
        }
        if($res){
            model('articles/ArticlesDetail')->editData($detail);
        }
        return $res;
    }

    public function getDataById($id)
    {
        if($id>0){
            $map['id']=$id;
            $data=$this->get($map);
            if($data){
                $data['detail']=model('articles/ArticlesDetail')->getDataById($id);
            }
            return $data;
        }
        return null;
    }

    public function getListByMap($map, $limit=5,$order = 'create_time desc')
    {
    	$list = $this->where($map)->limit($limit)->order($order)->select();
    	$category=model('ArticlesCategory')->_category();

        foreach($list as &$val){
            $val['category_title']=$category[$val['category']]['title'];
        }
        unset($val);
    	return $list;
    }

    //获取用户文章数的总阅读量
    public function _totalView($uid=0)
    {
        $total = cache("article_total_view_uid_{$uid}");
        if(!$total){
            $res=$this->where(['uid'=>$uid])->select();
            $total=0;
            foreach($res as $value){ 
                $total=$total+$value['view'];
            }
            unset($value);
            cache("article_total_view_uid_{$uid}",$total,3600);
        }
        return $total;
    }
    

}