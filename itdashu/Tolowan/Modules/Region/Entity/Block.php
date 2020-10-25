<?php
namespace Modules\Region\Entity;

use Core\Config as CoreConfig;
use Modules\Config\Entity\Config;
use Phalcon\Exception;

/**
 */
class Block extends Config
{

    protected $_module = 'region';

    protected $_list = false;

    protected $_dataConfig;

    public $region = false;

    public $id = false;

    public function editForm($fields = false)
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        $fields = $this->_fields;
        if ($this->id !== false) {
            unset($fields['id']);
        }
        return parent::editForm($fields);
    }

    public static function find($query=array(),$options = array()){
        $region = $query['region'];
        $regionBlockList = CoreConfig::get('m.region.blockList-'.$region);
        return $regionBlockList;
    }

    public static function findFirst($query)
    {
        if (!isset($query['region']) || !isset($query['block'])) {
            throw new \Exception('参数错误，所属区域没有设置');
        }
        $output = array();
        $blockList = CoreConfig::get('m.region.blockList-' . $query['region'], array());
        if (isset($blockList['data'][$query['block']])) {
            return $blockList['data'][$query['block']];
        }
        return false;
    }
    public function gets($query = array())
    {
        if (isset($query['region'])) {
            $region = $query['region'];
        } elseif (!$this->region) {
            throw new \Exception('参数错误，所属区域没有设置');
        } elseif ($this->region) {
            $region = $this->region;
        }
        $regionList = CoreConfig::get('m.region.blockList-' . $region, array());
        $this->_list = $regionList;
        return $regionList;
    }

    public function setFields($fields = array())
    {
        if (!$this->contentModel) {
            throw new \Exception('参数错误，内容类型没有设置');
        }
        if ($fields) {
            $this->_fields = $fields;
        } else {
            $baseModel = CoreConfig::get($this->_module . '.' . $this->_entityId . 'BaseFields', array());
            if ($this->contentModel) {
                $contentModel = $this->getContentModelFields();
                $this->_fields = array_merge($contentModel, $baseModel);
            } else {
                //获取该实体下全部字段
                $entityContentModelList = $this->getContentModelList();
                foreach ($entityContentModelList as $model) {
                    $fields = array();
                    if (isset($model['fields'])) {
                        if (is_array($model['fields'])) {
                            $fields = $model['fields'];
                        } elseif (is_string($model['fields'])) {
                            $fields = CoreConfig::get($model['fields'], array());
                        }
                    }
                    $baseModel = array_merge($baseModel, $fields);
                }
                $this->_fields = $baseModel;
            }
        }
        if ($this->_fields) {
            return $this->_fields;
        } else {
            return false;
        }
    }

    public function getThead()
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        if (isset($this->_fields['settings']['thead'])) {
            return $this->_fields['settings']['thead'];
        }
        return array(
            'id' => '机读名',
            'name' => '项目',
        );
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
        $this->getDI()
            ->getPlugin()
            ->fire('entityLinks:' . $this->_entity . ':' . $this->contentModel, $this);
        return $this->_links;
    }

    public function setContentModelList()
    {
        $contentModelListCache = CoreConfig::cache('blockContentModelList');
        $contentModelList = CoreConfig::get('m.' . $this->_module . '.contentModelList');
        $this->contentModelList = array_merge(
            $contentModelList,
            $contentModelListCache
        );
    }

    public function getContentModelList()
    {
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        return $this->contentModelList;
    }
    public function saveEntity($form)
    {
        $this->isSave = true;
        $regionBlockList = CoreConfig::get('m.region.blockList-' . $this->region, array());

        $data = $form->getData();

        if ($this->id) {
            $id = $this->id;
        } else {
            $id = $data['id'];
        }
        $data['id'] = $id;
        $data['contentModel'] = $this->contentModel;
        $regionBlockList['data'][$id] = $data;
        $regionBlockList['hierarchy'][$id] = $id;
        if (CoreConfig::set('m.region.blockList-' . $this->region, $regionBlockList)) {
            $this->getDI()
                ->getFlash()
                ->success('保存成功');
            $this->saveState = true;
            return true;
        } else {
            $this->getDI()
                ->getFlash()
                ->error('保存失败');
            $this->saveState = false;
            return false;
        }
    }

    public function delete($id = null)
    {
        $regionList = CoreConfig::get('m.region.blockList-' . $this->region, array());
        if (!$id) {
            $id = $this->id;
        }
        if (isset($regionList[$id])) {
            unset($regionList[$id]);
            $this->getDI()
                ->getFlash()
                ->success('删除成功');
            // $this->getDI()->getFlash()->success(CoreConfig::printCode($this->_list,false).$this->_dataConfig);
            return CoreConfig::set($this->_dataConfig, $this->_list);
        }
        $this->getDI()
            ->getFlash()
            ->error('删除失败');
        return false;
    }

    public function getData()
    {
        if ($this->_data) {
            return $this->_data;
        }
        if (!$this->_list) {
            $this->gets();
        }
        if ($this->id && isset($this->_list['data'][$this->id])) {
            $this->_data = $this->_list['data'][$this->id];
            return $this->_data;
        }
        return array();
    }
}
