<?php
namespace app\admin\model;
use think\Model;
/**
 * 用户操作
 */
class User extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 0){
        $data = $this->name('user')
                ->alias('A')
                ->field('A.*,B.type_name')
                ->join('user_type B','A.type_id= B.type_id')
                ->where($where)
                ->paginate($limit);
        return $data;
    }
    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        return $this->name('user')
                ->alias('A')
                ->join('user_type B','A.type_id = B.type_id')
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
        return $this->name('user')
                ->alias('A')
                ->field('A.*,B.type_status,B.type_name')
                ->join('user_type B','A.type_id= B.type_id')
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
     * 删除信息
     * @param int $userId ID
     * @return bool 删除状态
     */
    public function del($userId){
        $map = array();
        $map['user_id'] = $userId;
        return $this->where($map)->delete();
    }
}
