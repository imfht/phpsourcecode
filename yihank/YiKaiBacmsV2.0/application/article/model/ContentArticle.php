<?php
namespace app\article\model;
use think\Model;
/**
 * Class ContentArticle 文章内容信息模型
 * hongkai.wang 20161203  QQ：529988248
 */
class ContentArticle extends Model {

    // time 发布时间 读取器
    protected function getTimeAttr($time)
    {
        return date('Y-m-d H:i', $time);
    }
    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList($where = array(), $limit = 10, $order = 'A.time desc,A.content_id desc', $fieldsetId = 0){
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
        //获取最终结果
        $pageList = $model->field($field)
                    ->where($where)
                    ->order($order)
                    ->paginate($limit);
        if (!empty($pageList)){
            $i = 0;
            foreach ($pageList as $key=>$value){
                $pageList[$key]['app']=strtolower($value['app']);
                $pageList[$key]['i'] = $i++;
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
                ->join('content_article B',' A.content_id = B.content_id','left')
                ->join('category C','A.class_id = C.class_id','left')
                ->where($where)
                ->count();
    }
    public function countll($where = array()){
        return $this->where($where)->count();
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
    // 新增
    public function add(){
        Model::startTrans();
        $content_id=model('kbcms/Content')->add();
        if(!$content_id){
            Model::rollback();
            return false;
        }
        $model=new ContentArticle($_POST);
        $model->content_id=$content_id;
        $rs=$model->allowField(true)->save();
        if ($rs>0){
            Model::commit();
            return true;
        }else{
            Model::rollback();
            return false;
        }
    }
    // 修改
    public function edit(){
        $_POST['app']=request()->module();
        Model::startTrans();
        $rs=model('kbcms/Content')->edit();
        if(!$rs){
            Model::rollback();
            return false;
        }
        $model=new ContentArticle();
        $where = array();
        $where['content_id'] = input('post.content_id');
        $status = $model->allowField(true)->save($_POST,$where);
        if($status === false){
            Model::rollback();
            return false;
        }
        Model::commit();
        return true;
    }

    /**
     * 删除信息
     * @param int $content_id ID
     * @return bool 删除状态
     */
    public function del($content_id)
    {
        Model::startTrans();
        $status_content = model('kbcms/Content')->del($content_id);
        if(!$status_content){
            $this->rollBack();
            return false;
        }
        $map = array();
        $map['content_id'] = $content_id;
        $status = $this->where($map)->delete();
        if($status){
            Model::commit();
        }else{
            Model::rollback();
        }
        return $status;
    }

}
