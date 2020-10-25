<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 碎片表操作
 */
class FragmentModel extends BaseModel {
    //完成
    protected $_auto = array (
         array('content','html_in',3,'function'),
     );
    //验证
    protected $_validate = array(
        array('name','require', '碎片名称不能为空', 1),
        array('label','require', '碎片标识不能为空', 1),
        array('label','', '碎片标识不能重复', 1,'unique'),
        array('content','require', '碎片内容不能为空', 1),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        return  $this->select();
    }

    /**
     * 获取统计
     * @return int 数量
     */
    public function countList(){
        return  $this->count();
    }

    /**
     * 获取信息
     * @param int $fragmentId ID
     * @return array 信息
     */
    public function getInfo($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->getWhereInfo($map);
    }

    /**
     * 获取信息
     * @param array $where 条件
     * @return array 信息
     */
    public function getWhereInfo($where)
    {
        return $this->where($where)->find();
    }

    /**
     * 更新信息
     * @param string $type 更新类型
     * @return bool 更新状态
     */
    public function saveData($type = 'add'){
        $data = $this->create();
        if(!$data){
            return false;
        }
        if($type == 'add'){
            return $this->add();
        }
        if($type == 'edit'){
            if(empty($data['fragment_id'])){
                return false;
            }
            $status = $this->save();
            if($status === false){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * 删除信息
     * @param int $fragmentId ID
     * @return bool 删除状态
     */
    public function delData($fragmentId)
    {
        $map = array();
        $map['fragment_id'] = $fragmentId;
        return $this->where($map)->delete();
    }

}
