<?php
namespace Core;

use Phalcon\Mvc\User\Plugin;

class Entity extends Plugin
{
    protected $_fields;
    protected $_entity = null;
    protected $_contentModel = null;
    protected $_module;

    public function __construct($entity)
    {
        $this->_entity = $entity;
    }

    public function editForm($id)
    {
        global $di;
        $data = $this->findFirst($id);
        $contentModel = $data['contentModel'];
        $fields = $this->entityManags->getEntityFields($this->_entity, $contentModel);
        $fields['settings']['contentModel'] = $contentModel;
        $fields['settings']['id'] = $id;
        $fields['contentModel']['settings']['default'] = $contentModel;
        $entityForm = $di->getShared('form')->create($fields, $data);
        if ($entityForm->isValid()) {
            $entityForm->save();
        }
        return $entityForm;
    }

    public function addForm($contentModel)
    {
        global $di;
        $fields = $this->entityManags->getEntityFields($this->_entity, $contentModel);
        $fields['settings']['contentModel'] = $contentModel;
        $fields['settings']['entity'] = $this->_entity;
        $fields['contentModel']['settings']['default'] = $contentModel;
        $entityForm = $di->getShared('form')->create($fields);
        if ($entityForm->isValid()) {
            $entityForm->save();
        }
        return $entityForm;

    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setEntity($entity)
    {
        if (is_null($this->_entity)) {
            $this->_entity = $entity;
        }
        return $this;
    }

    public function find()
    {

    }

    public function findFirst($id)
    {
        $query = new MongoDB\Driver\Query(['_id'=>$id]);
        $cursor = $this->mongodbManager->executeQuery('dashu.'.$this->_entity, $query);
        return $cursor;
    }

    public function delete($id)
    {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete(['_id' => $id]);
        $state = $this->mongodbManager->executeBulkWrite('dashu.'.$this->_entity,$bulk);
        if($state){
            return $state;
        }else{
            return false;
        }
    }

    public function update($id,$data)
    {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(['_id'=>$id],$data);
        $state = $this->mongodbManager->executeBulkWrite('dashu.'.$entity,$bulk);
        if($state){
            return $state;
        }else{
            return false;
        }
    }

    public static function save($form)
    {
        global $di;
        $data = $form->getData();
        $formOptions = $form->getUserOptions();
        $entity = $formOptions['entity'];
        $contentModel = $formOptions['contentModel'];
        $data['contentModel'] = $contentModel;
        $bulk = new MongoDB\Driver\BulkWrite;
        if(isset($formOptions['id'])){
            $bulk->update(['_id'=>$formOptions['id']],$data);
        }else{
            $data['_id'] = 'getNextSequenceValue("'.$entity.'_id")';
            $nid = $bulk->insert($data);
        }
        $state = $di->getShared('mongodbManager')->executeBulkWrite('dashu.'.$entity,$bulk);
        if($state){
            return $state;
        }else{
            return false;
        }
    }
}