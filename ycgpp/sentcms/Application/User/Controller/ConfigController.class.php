<?php
namespace User\Controller;
use Common\Api\UserApi;

class ConfigController extends \Common\Controller\UserController{

	public function index(){
        $user = session('user_auth');
        $tab_hover = I('tab','base','trim');
        $member = D('Member');
        if ( IS_POST ) {
            $data = $member->create();
            $map = array('uid'=>$user['uid']);
            if ($data) {
                unset($data['salt']);
                unset($data['password']);
                $result = $member->where($map)->save($data);
                if ($result) {
                    $this->success('更新成功！');
                }else{
                    $this->error("更新失败！");
                }
            }else{
                $this->error($member->getError());
            }
        }else{
            $extend_data = $this->getTab();
            $data = $member->find($user['uid']);
            if ($extend_data) {
                $data = array_merge($data,$extend_data);
            }
            $data['username'] = $user['username'];

            $this->assign('tab_hover',$tab_hover);
            $this->assign('data',$data);
            $this->display();
        }
	}

    protected function getTab(){
        $user = session('user_auth');
        $group = D('MemberExtendGroup');
        $field = D('MemberExtendSetting');
        $info = D('MemberExtend');

        $info_data = D('MemberExtend')->where(array('uid'=>$user['uid']))->select();
        foreach ($info_data as $key => $value) {
            if ($value['field_type'] == 'array') {
                $value['field_data'] = json_decode($value['field_data']);
            }
            $data[$value['field_name']] = $value['field_data'];
        }

        $list = $group->where(array('status'=>'1'))->order('sort asc')->select();
        $field_list = $field->where(array('is_show' => 1))->select();
        foreach ($field_list as $key => $value) {
            //查询用户信息
            $find = $info->field($value['name'])->where(array('uid' => $user['uid']))->find();
            $value['value'] = $find[$value['name']];
            $fields[$value['extend_id']][] = $value;
        }
        $this->assign('fields',$fields);
        $this->assign('_tab',$list);
        return $data;
    }

    public function setuserinfo(){
        $user = session('user_auth');
        $info = D('MemberExtend');
        $post = I('post.');
        $uid = $user['uid'];

        if ( IS_POST ) {
            $data = $info->create();
            $find = $info->where(array('uid' => $uid))->find();
            if($find){
                $info->where(array('uid' => $uid))->save($data);
            }else{
                $data['uid'] = $uid;
                $info->add($data);
            }
            $this->success('更新成功!');
        }else{
            return;
        }
    }

	public function avatar(){
        $user = session('user_auth');
        if ( IS_POST ) {
            $result = array();
            $result['success'] = false;
            $success_num = 0;
            $msg = '上传失败';
            //上传目录
            $dir = "./Uploads/Avatar/".setavatardir($user['uid']);
            // 处理原始图片开始------------------------------------------------------------------------>
            //默认的 file 域名称是__source，可在插件配置参数中自定义。参数名：src_field_name
            $source_pic = $_FILES["__source"];
            $filename = 'avatar_';
            //如果在插件中定义可以上传原始图片的话，可在此处理，否则可以忽略。
            if ($source_pic){
                if ( $source_pic['error'] > 0 ){
                    $msg .= $source_pic['error'];
                }else{
                    //原始图片的文件名，如果是本地或网络图片为原始文件名、如果是摄像头拍照则为 *FromWebcam.jpg
                    $sourceFileName = $source_pic["name"];
                    //原始文件的扩展名(不包含“.”)
                    $sourceExtendName = substr($sourceFileName, strripos($sourceFileName, "."));
                    //保存路径
                    $savePath = $dir."/".$filename."real".$sourceExtendName;
                    //当前头像基于原图的初始化参数（只有上传原图时才会发送该数据，且发送的方式为POST），用于修改头像时保证界面的视图跟保存头像时一致，提升用户体验度。
                    //修改头像时设置默认加载的原图url为当前原图url+该参数即可，可直接附加到原图url中储存，不影响图片呈现。
                    $init_params = $_POST["__initParams"];
                    $result['sourceUrl'] = $savePath.$init_params;
                    move_uploaded_file($source_pic["tmp_name"], $savePath);
                    $success_num++;
                }
            }
            //处理原始图片结束处理头像图片开始
            //头像图片(file 域的名称：__avatar1,2,3...)。
            $avatars = array("__avatar1", "__avatar2", "__avatar3");
            $avatars_length = count($avatars);
            for ( $i = 0; $i < $avatars_length; $i++ ){ 
                $avatar = $_FILES[$avatars[$i]];
                $avatar_number = $i + 1;
                $avatar_name = array(
                    '1' => 'big',
                    '2' => 'middle',
                    '3' => 'small',
                );
                if ( $avatar['error'] > 0 ){
                    $msg .= $avatar['error'];
                }else{
                    $savePath = "$dir" .$filename.$avatar_name[$avatar_number]. ".jpg";
                    $result['avatarUrls'][$i] = $savePath;
                    move_uploaded_file($avatar["tmp_name"], $savePath);
                    $success_num++;
                }
            }

            $result['msg'] = $msg;
            if ($success_num > 0){
                $result['success'] = true;
            }           //返回图片的保存结果（返回内容为json字符串）
            echo json_encode($result);
        }else{
            $avatar = avatar($user['uid'],'real');
            $data = array(
                'avatar' => $avatar,
            );
            $this->assign($data);
            $this->display();
        }
	}

	/**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
	public function changepwd(){
        if ( IS_POST ) {
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
	}

    public function receiv(){
        $receiv = D('Receiv');
        $id = I('id','','trim,intval');
        if (IS_POST) {
            $data = $receiv->create();
            if ($data) {
                $result = $receiv->update();
                if ($result) {
                    $this->success('操作成功！',U('Config/receiv'));
                }else{
                    $this->error('操作失败！');
                }
            }else{
                $this->error($receiv->getError());
            }
        }else{
            $res = $receiv->select();
            foreach ($res as $key => $value) {
                $list[$value['id']] = $value;
            }

            $data['list'] = $list;
            if ($list[$id]) {
                $data['data'] = $list[$id];
            }

            $this->assign($data);
            $this->display();
        }
    }
}