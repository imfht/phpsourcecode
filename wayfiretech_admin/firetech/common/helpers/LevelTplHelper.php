<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-27 14:06:58
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-27 14:11:41
 */
 

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class LevelTplHelper extends BaseObject
{
    /**
     * @var 父级id
     */
    public $_pid;

    /**
     * @var 二级分类id
     */
    public $_cid;

    /**
     * @var 三级id
     */
    public $_id;

    /**
     * @var 分类名称
     */
    public $_title;

    public $_model;

    /**
     * @param 二级分类id $cid
     */
    public function setCid($cid)
    {
        $this->_cid = $cid;
    }

    /**
     * @return 二级分类id
     */
    public function getCid()
    {
        return $this->_cid;
    }

    /**
     * @param 父级id $pid
     */
    public function setPid($pid)
    {
        $this->_pid = $pid;
    }

    /**
     * @return 父级id
     */
    public function getPid()
    {
        return $this->_pid;
    }

    /**
     * @param 三级id $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }



    /**
     * @return 三级id
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param 分类名称 $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return 分类名称
     */
    public function getTitle()
    {
        return $this->_title;
    }

    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        $this->init();
    }

    public function init()
    {
        parent::init();
    }

    //声明查询的方法 一级
    public function courseCateMap()
    {
        $where = [];
        $where[$this->_pid]=0;
        $bloc_id = Yii::$app->params['bloc_id'];
        $store_id = Yii::$app->params['store_id'];
        
        if ($bloc_id && array_key_exists('bloc_id', $this->_model->attributes)) {
            $where['bloc_id'] = $bloc_id;
        }
        if ($store_id && array_key_exists('store_id', $this->_model->attributes)) {
            $where['store_id'] = $store_id;
        }
        $_data = $this->_model->find()->where($where)->select("{$this->_id},{$this->_title}")->all();
        $_data = ArrayHelper::map(array_merge($_data), $this->_id, $this->_title);
        return $_data;
    }
    //声明查询的方法 二级
    public function courseMap($cocateId)
    {
        $condition[$this->_id] = $cocateId;
        $_data = $this->_model->find()->select("{$this->_id},{$this->_title}")->where($condition)->all();
        $_data = ArrayHelper::map(array_merge($_data), $this->_id, $this->_title);
        return $_data;
    }
    //声明查询的方法 三级
    public function personMap($percateId, $cocateId = 0)
    {
        $condition[$this->_cid] = intval($cocateId);
        $_data = $this->_model->find()->select("{$this->_pid},{$this->_title}")->where($condition)->all();
        $_data = ArrayHelper::map(array_merge($_data), $this->_pid, $this->_title);
        return $_data;
    }
}
