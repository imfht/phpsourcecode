<?php
namespace app\kbcms\model;
use think\Model;

/**
 * Class Category 栏目基础信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class Category extends Model
{
    /**
     * 栏目列表
     * @param 条件 $where
     * @param 栏目id $class_id
     * @return 数组
     */
    public function loadList($where = array(), $class_id=0){
        $data=$this->loadData($where);
        $cat = new \org\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getTree($data, intval($class_id));
        $modelList = get_page_type();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $modelInfo = $modelList[$value['app']];
                $data[$key]['model_name'] = $modelInfo['name'];
            }
        }
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
        $pageList=$this->name('category')->where($where)->order('sequence ASC , class_id ASC')->limit($limit)->select();
        $list = array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['app'] = strtolower($value['app']);
                $list[$key]['curl'] = model('home/Category')->getUrl($value);
                $list[$key]['i'] = $i++;
            }
        }
        return $list;
    }
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($classId){
        $map = array();
        $map['class_id'] = $classId;
        return $this->getWhereInfo($map);
    }
    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where){
        $info = $this->where($where)->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }
    /**
     * 新增
     * @return  栏目id class_id |false
     */
    public function add(){
        $model=new Category($_POST);
        $model->app=request()->module();
        $model->lang_id=get_lang_id();
        if ($model->allowField(true)->save()>0){
            return $model->class_id;
        }else{
            return false;
        }
    }
    /**
     * 修改
     * @return true|false
     */
    public function edit(){
        $class_id=input('post.class_id');
        $model=new Category();
        if(empty($class_id)){
            return false;
        }
        $status = $model->allowField(true)->save($_POST,array('class_id'=>$class_id));
        if($status === false){
            return false;
        }
        return true;
    }
    /**
     * 删除
     * @param 栏目id $class_id
     * @return 1|0
     */
    public function del($class_id){
        $map = array();
        $map['class_id'] = $class_id;
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
        $cat = new \org\Category(array('class_id', 'parent_id', 'name', 'cname'));
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
