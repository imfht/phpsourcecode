<?php
namespace app\articles\controller;

use app\articles\controller\Index;

/*用户类*/
class User extends Index
{
	public function _initialize()
    {
        parent::_initialize();
        $this->need_login();
    }

	public function edit()
	{
        $aId=input('id',0,'intval');
        $title=$aId?"编辑":"新增";

        if(request()->isPost()){
            $data = input();
            $aId&&$data['id'] = $aId;
            $data['status'] = 0;
            $res = model('Articles')->editData($data);

            if($res){
                $this->success($title.'成功！',Url('my'));
            }else{
                $this->error($title.'失败！',model('Articles')->getError());
            }
        }else{

            if($aId){
                $data=model('Articles')->getDataById($aId);
            }else{
                $data = null;
            }
            $category=model('ArticlesCategory')->getCategoryList(['status'=>['egt',0], 'can_post'=>1],1);
            
        $this->assign('title',$title);
        $this->assign('category',$category);
        $this->assign('data', $data);
        return $this->fetch(); 
        }
	}

	public function my($r=20)
	{
		$uid = is_login();
        $map['uid'] = $uid;
        // 查询数据集
        $list = model('Articles')->where($map)->order('id', 'desc')->paginate($r);
        foreach($list as &$val){
            $val['user']=query_user(['space_url','avatar32','nickname'],$val['uid']);
            if($val['status'] == 1) $val['status_text'] = '审核通过';
            if($val['status'] == 0) $val['status_text'] = '待审核';
            if($val['status'] == 2) $val['status_text'] = '未通过审核';
            if($val['status'] == -1) $val['status_text'] = '已删除';
            if(empty($val['reason'])) $val['reason'] = '未知原因，请联系管理员';
        }
        unset($val);
        /*作者信息*/
        $author=query_user(['uid','space_url','nickname','avatar32','avatar64','signature'],$uid);
        $author['articles_count']=model('Articles')->where(['uid'=>$uid])->count();
        /*用户所要文章访问量*/
        $author['articles_view']=model('Articles')->_totalView($uid);
        /* 模板赋值并渲染模板 */
        $this->assign('uid', $uid);
        $this->assign('author',$author);
        $this->assign('list', $list);
        return $this->fetch();

	}

	private function need_login()
	{
		if(!_need_login()){
			$this->error('需要登录');
		}
	}

}