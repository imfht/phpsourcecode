<?php
namespace app\duxcms\model;
use app\base\model\BaseModel;
/**
 * 推荐位表操作
 */
class PositionModel extends BaseModel {
    //完成
    protected $_auto = array (
        array('sequence','intval',3,'function'),
        array('position_id','intval',2,'function'),
     );
    //验证
    protected $_validate = array(
        array('name','require', '推荐位名称不能为空', 1),
    );

    /**
     * 获取列表
     * @return array 列表
     */
    public function loadList(){
        return  $this->select();
    }

    /**
     * 获取信息
     * @param int $positionId ID
     * @return array 信息
     */
    public function getInfo($positionId = 1)
    {
        $map = array();
        $map['position_id'] = $positionId;
        return $this->where($map)->find();
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
            if(empty($data['position_id'])){
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
     * @param int $positionId ID
     * @return bool 删除状态
     */
    public function delData($positionId)
    {
        $map = array();
        $map['position_id'] = $positionId;
        return $this->where($map)->delete();
    }

}
