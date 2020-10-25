<?php
namespace Modules\User\Entity;

use Modules\Entity\Entity\Manager;
use Core\Config;

class UserManager extends Manager
{
    protected $_entityId = 'user';
    protected $_module = 'user';
    public $columns = array(
        'id','name','email','phone','password','created','active','email_validate','phone_validate','changed'
    );


    public function menuTabs(){
        $links = array();
        $contentModelList = $this->getContentModelList();
        foreach($contentModelList as $key => $contentModel){
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

    public function addForm($contentModel, $data = array())
    {
        global $di;
        $addFormInfo = Config::get('user.userBaseFields');
        $addFormInfo['settings']['contentModel'] = $contentModel;
        $addFormInfo['roles']['settings']['default'] = array($contentModel=>$contentModel);
        $this->entityForm = $di->getShared('form')->create($addFormInfo, $data);
        if ($this->entityForm->isValid()) {
            $this->save();
        }
        return $this->entityForm;
    }

    public function editForm($contentModel = null, $id = null)
    {
        global $di;
        $data = $this->findFirst($id);
        if (is_object($data)) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = (array) $data;
            }
        }
        if(isset($data['password'])){
            unset($data['password']);
        }
        $addFormInfo = Config::get('user.userBaseFields');
        $addFormInfo['password']['required'] = false;
        $addFormInfo['confirmPassword']['required'] = false;
        $addFormInfo['settings']['contentModel'] = $contentModel;
        $addFormInfo['settings']['id'] = $id;
        $this->entityForm = $di->getShared('form')->create($addFormInfo, $data);
        if ($this->entityForm->isValid()) {
            $this->save();
        }
        return $this->entityForm;
    }
}