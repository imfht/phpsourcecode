<?php
namespace workerbase\classs\worker;
use workerbase\traits\BaseTool;

/**
 * worker消息封装
 */
class WorkerMessage
{
    use BaseTool;

    //消息id
    private $_id;
    //worker类型
    private $_workerType = '';
    //worker参数
    private $_params = [];
    //时间戳
    private $_timestamp = '';
    //消费次数
    private $_useNum = 0;
    //发送日期
    private $_date = '';

    public function __construct($srcData = '')
    {
        if (!empty($srcData)) {
            $this->unSerialize($srcData);
        }
    }

    /**
     * @return mixed
     */
    public function getWorkerType()
    {
        return $this->_workerType;
    }

    /**
     * @param mixed $workerType
     */
    public function setWorkerType($workerType)
    {
        $this->_workerType = $workerType;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->_params = $params;
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->_timestamp = $timestamp;
    }

    /**
     * @param $useNum
     */
    public function setUseNum($useNum)
    {
        $this->_useNum = $useNum;
    }

    public function getUseNum()
    {
        return $this->_useNum;
    }

    public function setId($id)
    {
        $this->_id = $id;
    }

    public function getMsgId()
    {
        return $this->_id;
    }

    public function setDate($date)
    {
        $this->_date = $date;
    }

    public function getDate()
    {
        return $this->_date;
    }

    /**
     * 序列化
     * @return string
     */
    public function serialize()
    {
        $data = [
            '_id' => $this->_id?$this->_id:uniqid().$this->createRandomStr(16),
            '_timestamp' => $this->_timestamp?$this->_timestamp:time(),
            '_date' => $this->_date?$this->_date:date('Y-m-d'),
            '_useNum' => $this->_useNum+1,
            'workerType' => $this->_workerType,
            'params' => $this->_params,
        ];
        return json_encode($data);
    }

    /**
     * 返序列化
     * @param string $srcData      - 原始数据
     * @throws \Exception
     */
    public function unSerialize($srcData)
    {
        if (empty($srcData)) {
            throw new WorkerMessageInvalidException("worker msg is empty");
        }
        $data = json_decode($srcData, true);
        if (empty($data)) {
            if (JSON_ERROR_SYNTAX == json_last_error()) {
                $jsonData = @unserialize($srcData);
                $data = json_decode($jsonData, true);
                if (empty($data)) {
                    throw new WorkerMessageInvalidException("WorkerMessage invalid. data={$srcData}");
                }
            } else {
                throw new WorkerMessageInvalidException("WorkerMessage invalid. data={$srcData}");
            }
        }
        if (!isset($data['workerType']) || !isset($data['params'])) {
            throw new WorkerMessageInvalidException("WorkerMessage invalid. data={$srcData}");
        }
        $this->setWorkerType($data['workerType']);
        $this->setParams($data['params']);
        $this->setTimestamp($data['_timestamp']);
        $this->setId($data['_id']);
        $this->setUseNum($data['_useNum']);
        $this->setDate($data['_date']);
    }
}