<?php
namespace app\home\model;
use think\Model;

/**
 * Class Category 用户基础信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class User extends Model
{
    /**
     * 用户列表
     * @param 条件 $where
     * @param 用户id $user_id
     * @return 数组
     */
    public function loadList($where = array(), $user_id=0){
        
    }
    /**
     * 新增
     */
    public function add(){
        $_POST['password']=md5($_POST['password']);
        $_POST['add_time']=time();
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        $user_id=session('home_user.user_id');
        if (empty($user_id)){
            return false;
        }
        if (!empty(input('post.password'))){ //密码非空，处理密码加密
            $_POST['password'] = md5($_POST['password']);
        }else{
            unset($_POST['password']);
        }
        $where['user_id']=$user_id;
        $status=$this->allowField(true)->save($_POST,$where);
        if ($status>0&&(input('post.head_url')||input('post.nickname')||input('post.email'))){
            $user_info=$this->getInfo($user_id);
            $auth = array(
                'user_id'=> $user_info['user_id'],
                'head_url'=> $user_info['head_url'],
                'nickname'=> $user_info['nickname'],
                'email'=> $user_info['email']
            );
            session('home_user', $auth);
        }
        return $status;
    }
    /**
     * 修改密码
     */
    public function editPassword(){
        $user_id=session('home_user.user_id');
        $where['user_id']=$user_id;
        $_POST['password'] = md5($_POST['password']);
        return $this->allowField(true)->save($_POST,$where);
    }
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId){
        $map = array();
        $map['user_id'] = $classId;
        return $this->getWhereInfo($map);
    }
    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        $info = $this->where($where)->find();
        return $info;
    }
    /**
     * 登录用户
     * @param int $userId ID
     * @return bool 登录状态
     */
    public function setLogin($userInfo){
        // 更新登录信息
        $data = array(
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(),
        );
        $where['user_id']=['eq',$userInfo['user_id']];
        $this->save($data,$where);
        //设置cookie
        $auth = array(
            'user_id' => $userInfo['user_id'],
            'head_url'=> $userInfo['head_url'],
            'nickname'=> $userInfo['nickname'],
            'email'=> $userInfo['email']
        );
        session('home_user', $auth);
        return true;
    }
    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('home_user', null);
    }
    /**
     * 获取菜单面包屑
     * @param int $classId 菜单ID
     * @return array 菜单表列表
     */
    public function loadCrumb($classId){
        $data = model('kbcms/Category')->loadData();
        $cat = new \org\Category(array('user_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getPath($data, $classId);
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $data[$key] = $value;
                $data[$key]['url'] = $this->getUrl($value);
            }
        }
        return $data;
    }
    /**
     * 获取子用户ID
     * @param array $classId 当前用户ID
     * @return string 子用户ID
     */
    public function getSubClassId($classId)
    {
        $data = $this->loadList(array(), $classId);
        if(empty($data)){
            return;
        }
        $list = array();
        foreach ($data as $value) {
            if($value['show']){
                $list[]=$value['user_id'];
            }
        }
        return implode(',', $list);

    }
    /**
     * 获取用户URL
     * @param int $info 用户信息
     * @return bool 删除状态
     */
    public function getUrl($info){
        if (!empty($info['user_id'])){
            $tmp['user_id']=$info['user_id'];
        }
        if (!empty($info['urlname'])){
            $tmp['urlname']=$info['urlname'];
        }
        return match_url('home/'.strtolower($info['app']).'/index',$tmp);
    }
}
