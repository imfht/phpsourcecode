<?php
namespace app\article\model;
use app\base\model\BaseModel;
/**
 * 内容操作
 */
class ContentArticleModel extends BaseModel {
    //验证
    protected $_validate = array(
        array('content','require', '请填写文章内容', 1 ,'regex',3),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 0, $order = 'A.time desc,A.content_id desc', $fieldsetId = 0){
        //基础条件
        $where['C.app'] = 'article';
        $model =  $this->table("content as A")
                    ->join('{pre}content_article as B ON A.content_id = B.content_id')
					->join('{pre}category as C ON A.class_id = C.class_id');
        $field = 'A.*,B.*,C.name as class_name,C.app,C.urlname as class_urlname,C.image as class_image,C.parent_id';
        //查询扩展信息
        if(!empty($fieldsetId)){
            $fieldsetInfo = target('duxcms/FieldsetExpand')->getInfo($fieldsetId);
            if(!empty($fieldsetInfo)){
                //设置查询
                $model = $model->join('{pre}ext_'.$fieldsetInfo['table'].' as D ON A.content_id = D.data_id' , 'LEFT');
                $field .= ',D.*';
                //获取字段列表
                $whereExt = array();
                $whereExt['A.fieldset_id'] = $fieldsetId;
                $fieldList = target('duxcms/FieldExpand')->loadList($whereExt);
            }
        }
        //获取最终结果
        $pageList = $model->field($field)
                    ->where($where)
                    ->order($order)
                    ->limit($limit)
                    ->select();

        //处理数据结果
        $list=array();
        if(!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value) {
                //处理基础
                $list[$key]=$value;
                $list[$key]['app']=strtolower($value['app']);
                $list[$key]['aurl'] = target('duxcms/Content')->getUrl($value);
                $list[$key]['curl'] = target('duxcms/Category')->getUrl($value);
                $list[$key]['i'] = $i++;
                //处理扩展字段
                if(!empty($fieldList)){
                    foreach ($fieldList as $v) {
                        $list[$key][$v['field']] = target('duxcms/FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        $where['C.app'] = 'article';
        return $this->table("content as A")
                    ->join('{pre}content_article as B ON A.content_id = B.content_id')
                    ->join('{pre}category as C ON A.class_id = C.class_id')
                    ->where($where)
                    ->count();
    }

    /**
     * 获取信息
     * @param int $contentId ID
     * @return array 信息
     */
    public function getInfo($contentId)
    {
        $map = array();
        $map['A.content_id'] = $contentId;
        $info = $this->getWhereInfo($map);
        if(empty($info)){
            $this->error = '文章不存在！';
        }
        return $info;
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where,$order = '')
    {
        $info = $this->table("content as A")
                    ->join('{pre}content_article as B ON A.content_id = B.content_id')
					->join('{pre}category as C ON A.class_id = C.class_id')
                    ->field('A.*,B.content,C.name as class_name,C.app,C.urlname as class_urlname,C.image as class_image')
                    ->where($where)
                    ->order($order)
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
        $contentId = target('duxcms/Content')->saveData($type);
        if(!$contentId){
            $this->error = target('duxcms/Content')->getError();
            return false;
        }
        //分表处理
        $data = $this->create();
        if(!$data){
            $this->rollBack();
            return false;
        }
        if($type == 'add'){
            $data['content_id'] = $contentId;
            $status = $this->add($data);
            if($status){
                $this->commit();
            }else{
                $this->rollBack();
            }
            return $contentId;
        }
        if($type == 'edit'){
            $where = array();
            $where['content_id'] = $data['content_id'];
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
     * @param int $contentId ID
     * @return bool 删除状态
     */
    public function delData($contentId)
    {
        $this->beginTransaction();
        $model = target('duxcms/Content');
        $status = $model->delData($contentId);
        if(!$status){
            $this->error = $model->getError();
            $this->rollBack();
            return false;
        }
        $map = array();
        $map['content_id'] = $contentId;
        $status = $this->where($map)->delete();
        if($status){
            $this->commit();
        }else{
            $this->rollBack();
        }
        return $status;
    }

}
