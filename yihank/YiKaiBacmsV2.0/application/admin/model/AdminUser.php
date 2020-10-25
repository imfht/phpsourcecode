<?php
namespace app\admin\model;
use think\Model;
/**
 * 用户操作
 */
class AdminUser extends Model {

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 0){
        $data = $this->name('admin_user')
                ->alias('A')
                ->field('A.*,B.name group_name')
                ->join('admin_group B','A.group_id= B.group_id')
                ->where($where)
                ->paginate($limit);
        return $data;
    }

    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        return $this->name('admin_user')
                ->alias('A')
                ->join('admin_group B','A.group_id = B.group_id')
                ->where($where)
                ->count();
    }

    /**
     * 获取信息
     * @param int $userId ID
     * @return array 信息
     */
    public function getInfo($userId = 1){
        $map = array();
        $map['user_id'] = $userId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        return $this->name('admin_user')
                ->alias('A')
                ->field('A.*,B.status as group_status,B.name as group_name,B.base_purview,B.menu_purview')
                ->join('admin_group B','A.group_id= B.group_id')
                ->where($where)
                ->find();
    }

    /**
     * 新增
     */
    public function add(){
        $_POST['password']=md5($_POST['password']);
        return $this->allowField(true)->save($_POST);
    }
    /**
     * 更新
     */
    public function edit(){
        if (empty(input('post.user_id'))){
            return false;
        }
        if (!empty(input('post.password'))){ //密码非空，处理密码加密
            $_POST['password'] = md5($_POST['password']);
        }else{
            unset($_POST['password']);
        }
        $where['user_id']=input('post.user_id');
        return $this->allowField(true)->save($_POST,$where);
    }

    /**
     * 更新权限
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function savePurviewData(){
        $this->_auto = array();
        $data = $this->create();
        $this->menu_purview = serialize($this->menu_purview);
        $this->base_purview = serialize($this->base_purview);
        $status = $this->save();
        if($status === false){
            return false;
        }
        return true;
    }

    /**
     * 删除信息
     * @param int $userId ID
     * @return bool 删除状态
     */
    public function del($userId){
        $map = array();
        $map['user_id'] = $userId;
        return $this->where($map)->delete();
    }

    /**
     * 登录用户
     * @param int $userId ID
     * @return bool 登录状态
     */
    public function setLogin($userId){
        // 更新登录信息
        $data = array(
            'last_login_time' => time(),
            'last_login_ip' => get_client_ip(),
        );
        $where['user_id']=['eq',$userId];
        $this->save($data,$where);
        //写入系统记录
        api('admin','AdminLog','addLog','登录系统');
        //设置cookie
        $auth = array(
            'user_id' => $userId,
        );
        session('admin_user', $auth);
        session('admin_user_sign', data_auth_sign($auth));
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('admin_user', null);
        session('admin_user_sign', null);
        session('admin', null);
    }

}
