<?php
namespace Api\Controller;
use Addons\Avatar\AvatarAddon;
use Addons\Tianyi\TianyiAddon;

class UserController extends ApiController
{

    /**
     * 上传临时头像
     */
    public function uploadTempAvatar()
    {
        $this->requireLogin();//判断用户已登录
        //读取上传的图片
        $image = $this->getImageFromForm();//判断确实有文件上传
        
        //保存临时头像
        $uid = $this->getUid();
        $addon = new AvatarAddon();
        $result = $addon->uploadTemp($uid, $image);
        if (!$result) {
            $this->apiError(0, $addon->getError());
        }
        
        //获取临时头像
        $image = $addon->getTempAvatar($uid);
        //返回成功消息
        $this->apiSuccess('头像保存成功', null, array('image' => $image));//经过此函数变成格式化后的json
    }

    /**
     * 裁剪，保存头像
     * @param null $crop
     */
    public function applyAvatar($crop = null)
    {
        $this->requireLogin();
        //裁剪、保存头像
        $addon = new AvatarAddon();
        $result = $addon->apply($this->getUid(), $crop);
        if (!$result) {
            $this->apiError(0, $addon->getError());
        }
        //返回成功消息
        $this->apiSuccess('头像保存成功,页面将刷新');
    }
	
	
    public function changePassword($old_password, $new_password)
    {
        $this->requireLogin();
        //检查旧密码是否正确
        $this->verifyPassword($this->getUid(), $old_password);
        //更新用户信息
        $model = D('User/UcenterMember');
        $data = array('password' => $new_password);
        $data = $model->create($data);
        if (!$data) {
            $this->apiError(0, $this->getRegisterErrorMessage($model->getError()));
        }
        $model->where(array('id' => $this->getUid()))->save($data);
        //返回成功信息
        clean_query_user_cache($this->getUid(),'password');//删除缓存
        D('user_token')->where('uid='.$this->getUid())->delete();

        $this->apiSuccess("密码修改成功");
    }

    private function getImageFromForm()
    {
        $image = $_FILES['image'];
        if (!$image) {
            $this->apiError(1103, '图像不能为空');
        }
        return $image;
    }


    
    



    public function getProfile($uid = null)
    {
        //默认查看自己的详细资料
        if (!$uid) {
            $this->requireLogin();
            $uid = $this->getUid();
        }
        //读取数据库中的用户详细资料
        $map = array('uid' => $uid);
        $user1 = D('Home/Member')->where($map)->find();
        $user2 = D('User/UcenterMember')->where(array('id' => $uid))->find();

        //获取头像信息
        $avatar = new AvatarAddon();
        $avatar_path = $avatar->getAvatarPath($uid);
        $avatar_url = getRootUrl() . $avatar->getAvatarPath($uid);

        //缩略头像
        $avatar128_path = getThumbImage($avatar_path, 128);
        $avatar128_path = '/' . $avatar128_path['src'];
        $avatar128_url = getRootUrl() . $avatar128_path;

        //获取等级
        $title = D('Usercenter/Title')->getTitle($user1['score']);

        //只返回必要的详细资料
        $this->apiSuccess("获取成功", null, array(
            'uid' => $uid,
            'avatar_url' => $avatar_url,
            'avatar128_url' => $avatar128_url,
            'signature' => $user1['signature'],
            'email' => $user2['email'],
            'mobile' => $user2['mobile'],
            'score' => $user1['score'],
            'name' => $user1['name'],
            'sex' => $this->encodeSex($user1['sex']),
            'birthday' => $user1['birthday'],
            'title' => $title,
            'username' => $user2['username'],
        ));
    }

    public function setProfile($signature = null, $email = null, $name = null, $sex = null, $birthday = null)
    {
        $this->requireLogin();
        //获取用户编号
        $uid = $this->getUid();
        //将需要修改的字段填入数组
        $fields = array();
        if ($signature !== null) $fields['signature'] = $signature;
        if ($email !== null) $fields['email'] = $email;
        if ($name !== null) $fields['name'] = $name;
        if ($sex !== null) $fields['sex'] = $sex;
        if ($birthday !== null) $fields['birthday'] = $birthday;

        foreach($fields as $key=> $field)
        {
            clean_query_user_cache($this->getUid(),$key);//删除缓存
        }
        //将字段分割成两部分，一部分属于ucenter，一部分属于home
        $split = $this->splitUserFields($fields);
        $home = $split['home'];
        $ucenter = $split['ucenter'];
        //分别将数据保存到不同的数据表中
        if ($home) {
            /*if (isset($home['sex'])) {
                $home['sex'] = $this->decodeSex($home['sex']);
            }*/
            $home['uid'] = $uid;
            $model = D('Home/Member');
            $home = $model->create($home);
            $result = $model->where(array('uid' => $uid))->save($home);
            if (!$result) {
                $this->apiError(0, '设置失败，请检查输入格式!');
            }
        }
        if ($ucenter) {
            $model = D('User/UcenterMember');
            $ucenter['id'] = $uid;
            $ucenter = $model->create($ucenter);
            $result = $model->where(array('id' => $uid))->save($ucenter);
            if (!$result) {
                $this->apiError(0, '设置失败，请检查输入格式!');
            }
        }
        //返回成功信息
        $this->apiSuccess("设置成功!");
    }


    public function bindMobile($verify)
    {
        $this->requireLogin();
        //确认用户未绑定手机
        $uid = $this->getuid();
        $user = D('User/UcenterMember')->where(array('id' => $uid))->find();
        if ($user['mobile']) {
            $this->apiError(1801, "您已经绑定手机，需要先解绑");
        }
        //确认手机验证码正确
        $mobile = getMobileFromSession();
        $addon = new TianyiAddon();
        if (!$addon->checkVerify($mobile, $verify)) {
            $this->apiError(1802, "手机验证码错误");
        }
        //确认手机号码没有重复
        $user = D('User/UcenterMember')->where(array('mobile' => $mobile, 'status' => 1))->find();
        if ($user) {
            $this->apiError(1803, '该手机号码已绑定到另一个账号，不能重复绑定');
        }
        //修改数据库
        $uid = $this->getUid();
        D('User/UcenterMember')->where(array('id' => $uid))->save(array('mobile' => $mobile));
        write_query_user_cache($uid, 'mobile', $mobile);
        //返回成功结果
        $this->apiSuccess("绑定成功");
    }

    public function unbindMobile($verify)
    {
        $uid = $this->getUid();

        clean_query_user_cache($uid, 'mobile');
        $this->requireLogin();
        //确认用户已经绑定手机
        $model = D('User/UcenterMember');
        $user = $model->where(array('id' => $this->getUid()))->find();
        if (!$user['mobile']) {
            $this->apiError(1901, "您尚未绑定手机");
        }
        //确认被验证的手机号码与用户绑定的手机号相符
        $mobile = getMobileFromSession();
        if ($mobile != $user['mobile']) {
            $this->apiError(1902, "验证的手机与绑定的手机不符合");
        }
        //确认验证码正确
        $addon = new TianyiAddon;
        if (!$addon->checkVerify($mobile, $verify)) {
            $this->apiError(1903, "手机验证码错误");
        }
        //写入数据库

        $model->where(array('uid' => $uid))->save(array('mobile' => ''));

        //返回成功结果
        $this->apiSuccess("解绑成功");
    }
}