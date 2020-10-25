<?php
namespace app\home\model;
use think\Model;
/**
 * Class ContentArticle 文章内容信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class ContentArticle extends Model {
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 15, $order = 'A.time desc,A.content_id desc', $fieldsetId = 0){
        //基础条件
        $where['C.app'] = 'article';
        //语言判断
        if (get_lang_id()){
            $where['C.lang_id']=get_lang_id();
        }
        $model =  $this->name('content')
                        ->alias('A')
                        ->join('content_article B',' A.content_id = B.content_id')
                        ->join('category C','A.class_id = C.class_id');
        $field = 'A.*,B.*,C.name as class_name,C.app,C.urlname as class_urlname,C.image as class_image,C.parent_id';
        //查询扩展信息
        if(!empty($fieldsetId)){
            $fieldsetInfo = model('FieldsetExpand')->getInfo($fieldsetId);
            if(!empty($fieldsetInfo)){
                //设置查询
                $model = $model->join('ext_'.$fieldsetInfo['table'].' D','A.content_id = D.data_id' , 'LEFT');
                $field .= ',D.*';
                //获取字段列表
                $whereExt = array();
                $whereExt['A.fieldset_id'] = $fieldsetId;
                $fieldList = model('FieldExpand')->loadList($whereExt);
            }
        }

        //获取最终结果
        $pageList = $model->field($field)
                    ->where($where)
                    ->order($order)
                    ->paginate($limit);
        if (!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value){
                $pageList[$key]['app']=strtolower($value['app']);
                $pageList[$key]['aurl'] = model('Content')->getUrl($value);
                $pageList[$key]['curl'] = model('Category')->getUrl($value);
                $pageList[$key]['i'] = $i++;
                //处理扩展字段
                if(!empty($fieldList)){
                    foreach ($fieldList as $v) {
                        $pageList[$key][$v['field']] = model('FieldData')->revertField($value[$v['field']],$v['type'],$v['config']);
                    }
                }
            }
        }
        return $pageList;
    }
    /**
     * 获取数量
     * @return int 数量
     */
    public function countList($where = array()){
        $where['C.app'] = 'article';
        return $this->name("content")
                ->alias('A')
                ->join('content_article B',' A.content_id = B.content_id')
                ->join('category C','A.class_id = C.class_id')
                ->where($where)
                ->count();
    }
    /**
     * 获取信息
     * @param int $content_id ID
     * @return array 信息
     */
    public function getInfo($content_id)
    {
        $map = array();
        $map['A.content_id'] = $content_id;
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
        $info = $this->name("content")
                    ->alias('A')
                    ->join('content_article B',' A.content_id = B.content_id')
                    ->join('category C','A.class_id = C.class_id')
                    ->field('A.*,B.content,C.name as class_name,C.app,C.urlname as class_urlname,C.image as class_image')
                    ->where($where)
                    ->order($order)
                    ->find();
        if(!empty($info)){
            $info['app'] = strtolower($info['app']);
        }
        return $info;
    }
}
