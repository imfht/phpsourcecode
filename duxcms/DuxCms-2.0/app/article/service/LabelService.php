<?php
namespace app\article\service;
/**
 * 标签接口
 */
class LabelService{
	/**
	 * 栏目列表
	 */
	public function categoryList($data){
        $where='';
        //上级栏目
        if(isset($data['parent_id'])){
            $where['A.parent_id'] = $data['parent_id'];
        }
        //指定栏目
        if(!empty($data['class_id'])){
            $where[] = 'A.class_id in ('.$data['class_id'].')';
        }
        //栏目属性
        if(isset($data['type'])){
            if($data['type']){
                $where['A.type'] = 1;
            }else{
                $where['A.type'] = 0;
            }
        }
        //其他条件
        if(!empty($data['where'])){
            $where[] = $data['where'];
        }
        //其他属性
        $where['show'] = 1;
        $model = target('article/CategoryArticle');
        return $model->loadData($where,$data['limit']);
	}

    /**
     * 内容列表
     */
    public function contentList($data){
        $where=array();
        //指定栏目内容
        if(!empty($data['class_id'])){
            $classWhere = 'A.class_id in ('.$data['class_id'].')';
        }
        //指定栏目下子栏目内容
        if ($data['sub']&&!empty($data['class_id'])) {
            $classIds = target('duxcms/Category')->getSubClassId($data['class_id']);
            if(!empty($classIds)){
                $classWhere = "A.class_id in ({$classIds})";
            }
        }
        if(!empty($classWhere)){
            $where[] = $classWhere;
        }
        //是否带形象图
        if (isset($data['image'])) {
            if($data['image'] == true)
            {
                $where[] = 'A.image <> ""';
            }else{
                $where['A.image'] = '';
            }
        }
        //排除ID
        if(!empty($data['not_id'])){
            $where[] = 'A.content_id not in('.$data['not_id'].')';
        }
        //调用推荐位
        if(!empty($data['pos_id'])){
            $where[] = 'find_in_set('.$data['pos_id'].',A.position) ';
        }
        //调用扩展字段
        if(!empty($data['expand_id'])){
            $expand_id = intval($data['expand_id']);
        }
        //其他条件
        if (!empty($data['where'])) {
            $where[] = $data['where'];
        }
        //调用数量
        if (empty($data['limit'])) {
            $data['limit'] = 10;
        }
        //内容排序
        if(empty($data['order'])){
            $data['order']='A.time DESC,A.content_id DESC';
        }
        //其他属性
        $where['status'] = 1;
        return target('article/ContentArticle')->loadList($where,$data['limit'],$data['order'],$expand_id);
    }
	


}
