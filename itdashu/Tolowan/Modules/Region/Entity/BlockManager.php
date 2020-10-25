<?php
namespace Modules\Region\Entity;

use Modules\Entity\Entity\Manager;
use Core\Config;
use Phalcon\Exception;

class BlockManager extends Manager
{
    protected $_entityId = 'block';
    protected $_module = 'region';


    public function menuTabs()
    {
        $links = array();
        $contentModelList = $this->getContentModelList();
        foreach ($contentModelList as $key => $contentModel) {
            $links[] = array(
                'href' => array(
                    'for' => 'adminEntityAdd',
                    'entity' => $this->_entityId,
                    'contentModel' => $key
                ),
                'name' => $contentModel['modelName']
            );
        }
        return $links;
    }

    public function find($query = array())
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        return $modelClassName::find($query);
    }

    public function findFirst($query, $object = false)
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        $entityModel = $modelClassName::findFirst($query);
        if (!$entityModel) {
            throw new Exception('节点不存在');
        }
        return $entityModel;
    }

    public function save()
    {
        $data = $this->entityForm->getData();
        $formOptions = $this->entityForm->getUserOptions();
        $regionBlockList = Config::get('m.region.blockList-' . $data['region'], array());
        $region = $data['region'];
        $contentModel = $data['contentModel'];
        if (isset($formOptions['settings']['id'])) {
            $id = $formOptions['settings']['id'];
        } elseif (isset($data['id'])) {
            $id = $data['id'];
        } else {
            throw new Exception('参数错误');
        }
        $data['id'] = $id;
        $data['region'] = $region;
        $data['contentModel'] = $contentModel;
        $regionBlockList['data'][$id] = $data;
        $regionBlockList['hierarchy'][$id] = $id;
        if (Config::set('m.region.blockList-' . $region, $regionBlockList)) {
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
}