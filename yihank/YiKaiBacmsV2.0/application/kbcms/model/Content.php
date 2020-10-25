<?php
namespace app\kbcms\model;
use think\Model;

/**
 * Class Content 内容基础模型
 * hongkai.wang 20161203  QQ：529988248
 */
class Content extends Model
{
    protected $type       = [
        'time' => 'timestamp',
    ];
    protected $insert = ['time'];
    protected function setTimeAttr(){
        return time();
    }
    //栏目列表
    public function loadList($where = array(), $class_id=0){
        $data=$this->loadData($where);
        $cat = new \org\Category(array('class_id', 'parent_id', 'name', 'cname'));
        $data = $cat->getTree($data, intval($class_id));
        return $data;
    }
    //栏目数据
    public function loadData($where = array(), $limit = 0){
        $list=$this->name('category')->where($where)->order('sequence ASC , class_id ASC')->limit($limit)->select();
        return $list;
    }
    //新增
    public function add(){
        if (!empty($_POST['position'])){
            $_POST['position']=implode(',',$_POST['position']);
        }
        $model=new Content($_POST);
        $contentId=$model->allowField(true)->save();
        if (!$contentId){
            return false;
        }
        //保存扩展表
        $_POST['content_id']=$model->content_id;
        if(!$this->saveExtData($_POST)){
            return false;
        }
        return $model->content_id;
    }
    //修改
    public function edit(){
        $content_id=input('post.content_id');
        $model=new Content();
        if(empty($content_id)){
            return false;
        }
        if (!empty($_POST['position'])){
            $_POST['position']=implode(',',$_POST['position']);
        }
        $status = $model->allowField(true)->save($_POST,array('content_id'=>$content_id));
        if($status === false){
            return false;
        }
        //保存扩展表
        if(!$this->saveExtData($_POST)){
            return false;
        }

        return true;
    }
    //删除
    public function del($content_id){
        $map = array();
        $map['content_id'] = $content_id;
        return $this->where($map)->delete();
    }

    /**
     * 更新扩展信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveExtData($data){
        //查询栏目信息
        $classId = $data['class_id'];
        //获取字段集信息
        $fieldsetInfo = model('kbcms/Fieldset')->getInfoClassId($classId);
        //保存扩展字段
        if(!empty($fieldsetInfo)){
            $expandModel = model('kbcms/FieldData');
            //设置模型信息
            $expandModel->setTable(config('database.prefix').'ext_'.$fieldsetInfo['table']);

            $_POST['data_id'] = $data['content_id'];
            if($expandModel->getInfo($data['content_id'])){
                $type = 'edit';
            }else{
                $type = 'add';
            }
            if(!$expandModel->saveData($type,$fieldsetInfo)){
                $this->error = '保存失败';
                return false;
            }
        }
        return true;
    }
}
