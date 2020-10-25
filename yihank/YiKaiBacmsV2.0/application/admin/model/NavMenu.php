<?php
namespace app\admin\model;
use think\Model;

/**
 * Class Category 栏目基础信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class NavMenu extends Model
{
    /**
     * 栏目列表
     * @param 条件 $where
     * @param 栏目id $id
     * @return 数组
     */
    public function loadList($where = array(), $id=0){
        $data=$this->loadData($where);
        $cat = new \org\Category(array('id', 'parent_id', 'name', 'cname'));
        $data = $cat->getTree($data, intval($id));
        return $data;
    }
    /**
     * 栏目数据
     * @param 条件 $where
     * @param 显示数量 $limit
     * @return 数组
     */
    public function loadData($where = array(), $limit = 0){
        if (get_lang_id()){
            $where['lang_id']=get_lang_id();
        }
        $pageList=$this->name('nav_menu')->field('nm.*')->alias('nm')->join('nav n','nm.nav_id=n.nav_id')->where($where)->order('nm.sort ASC , nm.id ASC')->limit($limit)->select();

        return $pageList;
    }
    /**
     * 获取数据
     */
    public function countList($where){
        return $this->where($where)->count();
    }
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId){
        $map = array();
        $map['id'] = $classId;
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
     * 新增
     * @return  栏目id id |false
     */
    public function add(){
        $rs=$this->allowField(true)->save($_POST);
        if ($rs>0){
            return $rs;
        }else{
            return false;
        }
    }
    /**
     * 修改
     * @return true|false
     */
    public function edit(){
        $id=input('post.id');
        if(empty($id)){
            return false;
        }
        $status = $this->allowField(true)->save($_POST,array('id'=>$id));
        if($status === false){
            return false;
        }
        return true;
    }
    /**
     * 删除
     * @param 栏目id $id
     * @return 1|0
     */
    public function del($id){
        $map = array();
        $map['id'] = $id;
        return $this->where($map)->delete();
    }
    /**
     * 获取菜单面包屑
     * @param int $classId 菜单ID
     * @return array 菜单表列表
     */
    public function loadCrumb($classId)
    {
        $data = $this->loadData();
        $cat = new \org\Category(array('id', 'parent_id', 'name', 'cname'));
        $data = $cat->getPath($data, $classId);
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $data[$key] = $value;
                $data[$key]['url'] = getUrl($value);
            }
        }
        return $data;
    }
}
