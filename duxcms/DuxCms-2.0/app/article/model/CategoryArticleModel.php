<?php
namespace app\article\model;
use app\base\model\BaseModel;
/**
 * 栏目操作
 */
class CategoryArticleModel extends BaseModel {
    //验证
    protected $_validate = array(
        array('content_tpl','1,200', '内容模板未选择', 0 ,'length',3),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $classId=0){
        
        $data = $this->loadData($where);
        $cat = new \framework\ext\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getTree($data, intval($classId));
        return $data;
    }

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadData($where = array(), $limit = 0){
        $pageList = $this->table("category as A")
                    ->join('{pre}category_article as B ON A.class_id = B.class_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->limit($limit)
                    ->order("A.sequence ASC , A.class_id ASC")
                    ->select();
        //处理数据类型
        $list=array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                $list[$key]=$value;
                $list[$key]['app']=strtolower($value['app']);
                $list[$key]['curl'] = target('duxcms/Category')->getUrl($value);
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
    public function getInfo($classId)
    {
        $map = array();
        $map['A.class_id'] = $classId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        $info = $this->table("category as A")
                    ->join('{pre}category_article as B ON A.class_id = B.class_id')
                    ->field('B.*,A.*')
                    ->where($where)
                    ->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        //事务总表处理
        $this->beginTransaction();
        $classId = target('duxcms/Category')->saveData($type);
        if(!$classId){
            $this->error = target('duxcms/Category')->getError();
            $this->rollBack();
            return false;
        }
        //分表处理
        $data = $this->create();
        if(!$data){
            $this->rollBack();
            return false;
        }
        if($type == 'add'){
            $data['class_id'] = $classId;
            $status = $this->add($data);
            if($status){
                $this->commit();
            }else{
                $this->rollBack();
            }
            return $status;
        }
        if($type == 'edit'){
            $where = array();
            $where['class_id'] = $data['class_id'];
            $status = $this->where($where)->save($data);
            if($status === false){
                $this->rollBack();
                return false;
            }
            $this->commit();
            return true;
        }
        $this->rollBack();
        return false;
    }

    /**
     * 删除信息
     * @param int $classId ID
     * @return bool 删除状态
     */
    public function delData($classId)
    {
        //总表
        target('duxcms/Category')->delData($classId);
        //分表
        $map = array();
        $map['class_id'] = $classId;
        return $this->where($map)->delete();
    }
    

}
