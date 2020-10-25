<?php
namespace Modules\Config\Entity;

use Core\Config;
use Modules\Entity\Entity\Manager;

class ConfigManager extends Manager
{
    protected $_entityId = 'config';
    protected $_module = 'config';

    public function getThead()
    {
        $thead = array(
            'modelName' => '配置名',
            'description' => '项目',
        );
        return $thead;
    }

    public function find($query = array())
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        return $modelClassName::find();
    }

    public function findFirst($query, $object = false)
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        $entityModel = $modelClassName::findFirst($query);
        return $entityModel;
    }

    public function editForm($contentModel = null, $id = null)
    {
        global $di;
        $addFormInfo = $this->getFields($contentModel);
        $addFormInfo['settings']['contentModel'] = $contentModel;
        $addFormInfo['settings']['id'] = $contentModel;
        $addFormInfo['contentModel']['settings']['default'] = $contentModel;
        $data = $this->findFirst($contentModel);
        if (is_object($data)) {
            $data = (array) $data;
        }
        $this->entityForm = $di->getShared('form')->create($addFormInfo, $data);
        if($this->entityForm->isValid()){
            $this->save();
        }
        return $this->entityForm;
    }

    public function save()
    {
        $data = $this->entityForm->getData();
        $options = $this->entityForm->getUserOptions();
        if (!isset($options['data'])) {
            $this->getDI()->getFlash()->error('保存失败，没有设置保存位置');
            return false;
        }
        if (!is_string($options['data'])) {
            $this->getDI()->getFlash()->error('保存失败，保存位置必须是字符串');
            return false;
        }
        if(Config::set($options['data'], $data)){
            $this->getDI()->getFlash()->success('保存成功');
            return true;
        }else{
            $this->getDI()->getFlash()->error('保存失败');
            return false;
        }
    }
}
