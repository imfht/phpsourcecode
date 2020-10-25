<?php
namespace Modules\Config\Entity;

use Core\Config as CoreConfig;
use Modules\Config\Entity\Config;
use Phalcon\Exception;

/**
 */
class ConfigList extends Config
{

    protected $_source = 'term';

    protected $_module = 'config';

    protected $_list = false;

    protected $_entity = 'configList';

    protected $_entityId = 'configList';

    protected $_data;

    public $id = false;

    public function getChildren()
    {
        $query = array(
            'conditions' => 'parent = :parent:',
            'bind' => array(
                'parent' => $this->id,
            ),
            'order' => 'widget',
        );
        $output = self::find($query);
        return $output;
    }

    public function toArray(){
        return (array)$this;
    }

    public function editForm($fields = false)
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        $fields = $this->_fields;
        if ($this->id !== false) {
            unset($fields['id']);
        }
        return parent::editForm($fields);
    }

    public function menuGroup($type = null)
    {
        return false;
    }
    public static function find($query = array())
    {
        global $di;
        $fields = $di->getShared('entityManager')->get('configList')->getFields($query['contentModel']);
        $data = CoreConfig::get($fields['settings']['data']);
        $list = new \stdClass();
        foreach ($data as $key => $value) {
            $item = new ConfigList();
            foreach ($value as $k => $v) {
                $item->{$k} = $v;
                $item->setData($fields['settings']['data']);
                $item->contentModel = $query['contentModel'];
            }
            $list->{$key} = $item;
            unset($item);
        }
        return $list;
    }
    public function setData($data){
        $this->_data = $data;
    }
    public static function findFirst($query)
    {
        if(!is_array($query) || !isset($query['contentModel']) || !isset($query['id'])){
            throw new Exception('参数错误');
        }
        $contentModel = $query['contentModel'];
        $contentModelList = CoreConfig::get('m.config.entityConfigListContentModelList');
        if (!isset($contentModelList[$contentModel])) {
            throw new Exception('参数错误，内容类型不存在');
        }
        $configFields = CoreConfig::get($contentModelList[$contentModel]['fields'], array());
        if (!isset($configFields['settings']['data'])) {
            throw new Exception('参数错误，字段不存在');
        }
        $dataList = CoreConfig::get($configFields['settings']['data'], array());
        if(isset($query['id'])){
            if(isset($dataList[$query['id']])){
                $data = new ConfigList();
                foreach($dataList[$query['id']] as $key => $value){
                    $data->{$key} = $value;
                    $data->setData($configFields['settings']['data']);
                    $data->contentModel = $contentModel;
                }
                return $data;
            }
            return false;
        }else{
            $output = new \stdClass();
            foreach($dataList as $key => $item){
                $data = new ConfigList();
                foreach($item as $key => $value){
                    $data->{$key} = $value;
                    $data->setData($configFields['settings']['data']);
                    $data->contentModel = $contentModel;
                }
                $output->{$key} = $data;
            }
            return $output;
        }
        return false;
    }

    public function links()
    {
        $this->_links = array(
            'edit' => array(
                'href' => array(
                    'for' => 'adminConfigListEditor',
                    'contentModel' => $this->contentModel,
                    'id' => $this->id,
                ),
                'data-target' => 'right_handle',
                'icon' => 'info',
                'name' => '编辑',
            ),
            'delete' => array(
                'href' => array(
                    'for' => 'adminConfigListDelete',
                    'contentModel' => $this->contentModel,
                    'id' => $this->id,
                ),
                'data-target' => 'main',
                'icon' => 'danger',
                'name' => '删除',
            ),
        );
        if ($this->getDI()->getEventsManager()->fire('entity:links', $this) === false) {
            return false;
        }
        return $this->_links;
    }


    public function delete()
    {
        if(!isset($this->id) || !$this->_data){
            throw new Exception('参数错误');
        }
        $data = CoreConfig::get($this->_data);
        if(!isset($data[$this->id])){
            return false;
        }
        unset($data[$this->id]);
        return CoreConfig::set($this->_data,$data);
    }

    public function getData()
    {
        if ($this->_data) {
            return $this->_data;
        }
        if (!$this->_list) {
            $this->gets();
        }
        if ($this->id && isset($this->_list[$this->id])) {
            $this->_data = $this->_list[$this->id];
            return $this->_data;
        }
        return array();
    }

    public function setFields($fields = array())
    {
        parent::setFields($fields);
        $this->_dataConfig = $this->_fields['settings']['data'];
    }
}
