<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 1/16/14
 * Time: 9:40 PM
 */

namespace Api\Controller;

use Addons\Avatar\AvatarAddon;

//use Addons\LocalComment\LocalCommentAddon;
//use Addons\Favorite\FavoriteAddon;
use Addons\Tianyi\TianyiAddon;

class UserController extends ApiController
{
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

    /**
     * 上传头像并裁剪保存。
     * @param null $crop 字符串。格式为x,y,width,height，单位为像素
     */
    public function uploadAvatar($crop = null)
    {
        $this->requireLogin();
        //读取上传的图片
        $image = $this->getImageFromForm();
        //保存临时头像、裁剪、保存头像
        $uid = $this->getUid();
        $addon = new AvatarAddon();
        $result = $addon->upload($uid, $image, $crop);
        if (!$result) {
            $this->apiError(0, $addon->getError());
        }
        //返回成功消息
        $this->apiSuccess('头像保存成功');
    }

    /**
     * 上传临时头像
     */
    public function uploadTempAvatar()
    {
        $this->requireLogin();
        //读取上传的图片
        $image = $this->getImageFromForm();
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
        $this->apiSuccess('头像保存成功', null, array('image' => $image));
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
        $this->apiSuccess('头像保存成功');
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

    /**
     * 测试API的上传头像
     */
    public function testUpload()
    {
        $this->display();
    }

    public function listTopic($uid = 0, $offset = 0, $count = 10, $comment_count = 2)
    {
        //默认获取自己的主题
        if (!$uid) {
            $this->requireLogin();
            $uid = $this->getUid();
        }
        //确认参数正确
        if ($offset < 0 || $count < 0 || $comment_count < 0) {
            $this->apiError(1401, '参数错误');
        }
        //获取指定的主题列表
        $weibo_model_id = D('Admin/Model')->getIdByName('weibo');
        if (!$weibo_model_id) {
            $this->apiError(1402, '后台配置错误，找不到微博模型');
        }
        $map = array('status' => 1, 'model_id' => $weibo_model_id, 'root' => '0');
        if ($uid) {
            $map['uid'] = $uid;
        }
        $model = D('Home/Document');
        $list = $model->where($map)->order('create_time desc')->limit("$offset,$count")->field('id')->select();
        $totalCount = $model->where($map)->order('create_time desc')->field('id')->count();
        if (!$list) {
            $list = array();
        }
        //获取每个主题的详细资料
        foreach ($list as &$e) {
            $e = $this->getTopicStructure($e['id'], $comment_count);
        }
        //返回结果
        $this->apiSuccess("获取成功", null, array('total_count' => $totalCount, 'list' => $list));
    }

    public function listTakePartIn($uid = 0, $offset = 0, $count = 10, $comment_count = 2)
    {
        //默认UID
        if (!$uid) {
            $this->requireLogin();
            $uid = $this->getUid();
        }
        //确认参数正确
        if ($uid <= 0 || $offset < 0 || $count < 0 || $comment_count < 0) {
            $this->apiError(400, '参数错误');
        }
        //读取指定任务的评论列表
        $addon = new LocalCommentAddon();
        $map = array('uid' => $uid, 'status' => 1);
        $model = $addon->getCommentModel();
        $result = $model->where($map)->order('create_time desc')->field('DISTINCT document_id')->limit("$offset,$count")->select();
        $totalCount = $model->where($map)->order('create_time desc')->count('DISTINCT document_id');
        if (!$result) {
            $result = array();
        }
        //获取主题的详细信息
        foreach ($result as &$e) {
            $e = $this->getTopicStructure($e['document_id'], $comment_count);
        }
        //返回成功结果
        $this->apiSuccess("获取成功", null, array('total_count' => $totalCount, 'list' => $result));
    }

    public function listFavorite($uid = 0, $offset = 0, $count = 10, $comment_count = 2)
    {
        //默认UID
        if (!$uid) {
            $this->requireLogin();
            $uid = $this->getUid();
        }
        //确认参数正确
        if ($uid <= 0 || $offset < 0 || $count < 0 || $comment_count < 0) {
            $this->apiError(400, '参数错误');
        }
        //获取收藏列表
        $addon = new FavoriteAddon;
        $model = $addon->getFavoriteModel();
        $map = array('uid' => $uid, 'status' => 1);
        $result = $model->where($map)->order('create_time desc')->limit("$offset,$count")->select();
        $totalCount = $model->where($map)->order('create_time desc')->count();
        if (!$result) {
            $result = array();
        }
        //获取主题的详细资料
        foreach ($result as &$e) {
            $e = array(
                'favorite_id' => $e['id'],
                'document' => $this->getTopicStructure($e['document_id'], $comment_count),
                'create_time' => $e['create_time'],
            );
        }
        //返回成功结果
        $this->apiSuccess("获取成功", null, array('total_count' => $totalCount, 'list' => $result));
    }

    public function deleteFavorite($favorite_id)
    {
        $this->requireLogin();
        //确认指定的收藏是自己的
        $addon = new FavoriteAddon;
        $model = $addon->getFavoriteModel();
        $favorite = $model->getFavoriteById($favorite_id);
        if (!$favorite) {
            $this->apiError(1702, "该编号的收藏不存在，无法删除");
        }
        if ($favorite['uid'] != $this->getUid()) {
            $this->apiError(1701, '权限不足');
        }
        //删除数据库中的数据库
        $model->deleteFavoriteById($favorite_id);
        //减小收藏数
        $document = D('Home/Document')->detail($favorite['document_id']);
        $model_name = D('Admin/Model')->getNameById($document['model_id']);
        if ($model_name == 'weibo') {
            D('Home/Weibo', 'Logic')->where(array('id' => $document['id']))->save(array('bookmark' => $document['bookmark'] - 1));
        }
        // 返回成功结果
        $this->apiSuccess("删除成功");
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