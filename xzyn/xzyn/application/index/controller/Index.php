<?php
namespace app\index\controller;

use app\common\controller\BaseHome;
use app\common\model\Guestbook;

class Index extends BaseHome {
    public function initialize(){
        parent::initialize();
    }

    public function index() {
        return $this->fetch();
    }

    public function newarc($page){
        $archive = new \app\common\model\Archive();
        $dataList = $archive->where(['status'=>1])->order('id desc')->page($page.', 5')->select();
        $this->assign('dataList', $dataList);
        return $this->fetch('inc/new_arc');
    }

    public function create() {	//提交留言
        if (request()->isPost()){
    		$GuestbookModel = new Guestbook();
            $data = input('post.');
			if(empty($this->uid)){
				return ajaxReturn('请登录留言','',2);
			}
			$result = $this->validate($data,'Guestbook.add');
			if( true !== $result ){
				return ajaxReturn($result);
			}else{
				$result = $GuestbookModel->allowField(true)->save($data);
			}
            if ($result){
                return ajaxReturn('留言成功', url('index'));
            }else{
                return ajaxReturn('操作失败');
            }
        }else{
            return $this->fetch();
        }
    }


}
