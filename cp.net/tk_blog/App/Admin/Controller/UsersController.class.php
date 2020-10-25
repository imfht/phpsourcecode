<?php
namespace Admin\Controller;
class UsersController extends AdminBaseController {
    /**
     * 本地用户列表
     */
    public function originalUser(){
        $userModel = new \Common\Model\UsersModel();
        $this->assign('data',$userModel->getListData(5));
        $this->display();
    }

    /**
     * 用户是否拉黑
     */
    public function LablackUser(){
        if (IS_AJAX) {
            $UsersModel = new \Common\Model\UsersModel();
            $uid = I('post.id',0,'intval');
            if ($uid) {
                if ($UsersModel->execBlackData($uid)) {
                    exit(json_encode(array('status'=>1,'msg'=>'操作成功')));
                } else {
                    exit(json_encode(array('status'=>0,'msg'=>'操作失败')));
                }
            } else {
                exit(json_encode(array('status'=>0,'msg'=>'操作失败,请重试...')));
            }
        }
    }


    /**
     * 第三方用户列表
     */
    public function foreignUser(){
        $oauthModel = M('Oauth_user');
        $count = $oauthModel->count();   // 查询满足要求的总记录数
        $Page = new \Think\Page($count,5); // 实例化分页类 传入总记录数和每页显示的记录数
        //设置分页显示
        $Page->setConfig('prev','Prev');
        $Page->setConfig('next','Next');
        $show = $Page->show(); // 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $oauthModel->order('id ASC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $result = array(
            'list' => $list,
            'page' => $show
        );
        $this->assign('data',$result);
        $this->display();
    }

    /**
     * 修改密码
     */
    public function editPassword(){
        if (IS_AJAX) {
            $UsersModel = new \Common\Model\UsersModel();
            if ($UsersModel->editPass()) {
                exit(json_encode(array('status'=>1,'msg'=>'操作成功','url'=>'/admin/login')));
            } else {
                exit(json_encode(array('status'=>0,'msg'=>$UsersModel->getError())));
            }
        }
        $this->display();
    }

    /**
     * 修改头像
     */
    public function editFace(){
        $this->display();
    }

    /**
     * AJAX修改头像
     */
    public function send_FaceUpload(){
        $faceName = 'face_' . date('YmdHis');
        $upload = $this->uploads('./Uploads/','/Face/',$faceName);
        if (is_array($upload)) {
            //上传成功
            //上传成功 组合Url
            $upload['file']['url'] = substr(C('FILE_ROOT_PATH'),1) . substr($upload['file']['savepath'],1) . $upload['file']['savename'];
            $json = array(
                'status' => 1,
                'msg'    => '上传成功',
                'name'   => $upload['file']['name'],
                'url'    => $upload['file']['url']
            );
            M('Users')->where(array('uid'=>$_SESSION['admin_user']['uid'],'user_type'=>1))->save(array('face'=>$upload['file']['url']));
            exit(json_encode($json));
        } else {
            //上传成功
            exit(json_encode(array('status'=>0,'msg'=>$upload)));
        }

    }
}