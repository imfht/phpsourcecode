<?php

class ipbanModel extends Model {

	
	 protected $_validate = array(
        array('name', 'require', '不能为空'), //不能为空
        array('name', '', '已存在该名称', 1, 'unique', 3), //检测重复
    );
    /**
     * 清理过期记录
     */
    public function clear() {
        $this->where('expires_time <> 0 AND expires_time < '.time())->delete();
    }

    /**
     * 检测分类是否存在
     *
     * @param string $name
     * @param int $pid
     * @return bool
     */
    public function name_exists($name, $id=0)
    {
        $pk = $this->getPk();
        $where = "name='" . $name . "'  AND ". $pk ."<>'" . $id . "'";
        $result = $this->where($where)->count($pk);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}