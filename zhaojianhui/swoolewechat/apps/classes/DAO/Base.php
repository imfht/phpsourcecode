<?php
namespace App\DAO;

class Base
{
    protected $primaryId;//主键ID
    protected $modelName;//模型名称
    protected $dbKey = 'master';
    /**
     * 模型对象
     * @var
     */
    protected $modelObj;

    public function __construct($primaryId)
    {
        $this->primaryId = $primaryId;
        $this->modelObj = model($this->modelName, $this->dbKey);
    }

    /**
     * 获取数据
     * @return \Swoole\Record
     */
    public function get()
    {
        return $this->modelObj->get($this->primaryId);
    }

    /**
     * 更新数据
     * @param $data
     * @return bool
     */
    public function update($data)
    {
        return $this->modelObj->set($this->primaryId, $data);
    }

    /**
     * 删除数据
     * @return true
     */
    public function delete()
    {
        return $this->modelObj->del($this->primaryId);
    }
}
