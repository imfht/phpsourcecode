<?php
namespace Home\Service;

/**
 * CommonService
 */
abstract class CommonService {
    /**
     * 得到数据行数
     * @param  array $where
     * @return int
     */
    public function getCount(array $where) {
        return $this->getM()->where($where)->count();
    }

    /**
     * 是否存在
     * @param $id
     */
    public function existById($id){
        $model = $this->getById($id);
        return !is_null($this->getById($id));
    }

    public function getById($id){
        return $this->getM()->getById($id);
    }

    /**
     * 得到分页数据
     * @param  array $where    分页条件
     * @param  int   $firstRow 起始行
     * @param  int   $listRows 行数
     * @return array
     */
    public function getPagination($where, $fields=null,$order=null,$firstRow,$listRows) {
        // 是否关联模型
        $M = $this->isRelation() ? $this->getD()->relation(true)
                                 : $this->getM();

        // 需要查找的字段
        if (isset($fields)) {
            $M = $M->field($fields);
        }

        // 条件查找
        if (isset($where)) {
            $M = $M->where($where);
        }

        // 数据排序
        if (isset($order)) {
            $M = $M->order($order);
        }

        // 查询限制
        if (isset($firstRow) && isset($listRows)) {
            $M = $M->limit($firstRow . ',' . $listRows);
        } else if (isset($listRows) && isset($firstRow)) {
            $M = $M->limit($listRows);
        }

        return $M->select();
    }

    /**
     * 删除文件
     * @param  array $files 需要删除的文件路径
     * @return
     */
    protected function unlinkFiles($files) {
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * 判断是否为空
     * @param  mixed  $mixed 需要检查的值
     * @return boolean
     */
    protected function isEmpty($mixed) {
        if (is_array($mixed)) {
            $mixed = array_filter($mixed);
            return empty($mixed);
        } else {
            return empty($mixed);
        }
    }

    /**
     * 返回结果值
     * @param  int   $status
     * @param  fixed $data
     * @return array
     */
    protected function resultReturn($status, $data=null) {
        return array('status' => $status,
                     'data' => $data);
    }

    /**
     * 返回错误的结果值
     * @param  string $error 错误信息
     * @return array         带'error'键值的数组
     */
    protected function errorResultReturn($error) {
        return $this->resultReturn(false, array('error' => $error));
    }

    /**
     * 得到M
     * @return Model
     */
    protected function getM() {
        return M($this->getModelName());
    }

    /**
     * 得到D
     * @return Model
     */
    protected function getD() {
        return D($this->getModelName());
    }

    /**
     * 是否关联查询
     * @return boolean
     */
    protected function isRelation() {
        return true;
    }

    /**
     * 得到模型的名称
     * @return string
     */
    protected abstract function getModelName();
}
