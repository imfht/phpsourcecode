<?php
namespace Modules\Entity\Entity;

use Core\Config;
use Phalcon\Exception;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Paginator\Adapter\Model as PaginationModel;

/**
 * Class EntityManager
 * @package Core
 */
class Manager extends Plugin
{
    protected $_entityId = 'entity';
    protected $_module = 'entity';
    protected $_entityInfo;
    protected $_connect = 'db';
    protected $_query;
    protected $_isNew = false;
    protected $_isSaveSuccess = null;
    public $entityForm;
    public $entityModel;

    public function __construct()
    {
        $this->_entityInfo = $this->getDI()->getEntityManager()->getEntityInfo($this->_entityId);
    }

    public function isSaveSuccess()
    {
        return $this->_isSaveSuccess;
    }

    public function getEntityId()
    {
        return $this->_entityId;
    }

    public function getModule()
    {
        return $this->_module;
    }

    protected function paramsValidate($query)
    {
        if (!isset($query['entity'])) {
            throw new Exception('参数错误');
        }
        if (!isset($this->_entitys[$query['entity']])) {
            throw new Exception('实体类型不存在：' . $query['entity']);
        }
    }

    public function getContentModelList()
    {
        return Config::get('m.' . $this->_module . '.entity' . ucfirst($this->_entityId) . 'ContentModelList', array());
    }

    public function addForm($contentModel, $data = array())
    {
        global $di;
        $addFormInfo = $this->getFields($contentModel);
        $addFormInfo['settings']['contentModel'] = $contentModel;
        $data['contentModel'] = $contentModel;
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
                $data = (array)$data;
            }
        }
        if (is_null($contentModel) && isset($data['contentModel'])) {
            $contentModel = $data['contentModel'];
        } elseif (is_null($contentModel)) {
            throw new Exception('参数错误');
        }
        $addFormInfo = $this->getFields($contentModel);
        $addFormInfo['settings']['contentModel'] = $contentModel;
        $addFormInfo['settings']['id'] = $id;
        $addFormInfo['contentModel']['settings']['default'] = $contentModel;
        $this->entityForm = $di->getShared('form')->create($addFormInfo, $data);
        if ($this->entityForm->isValid()) {
            $this->save();
        }
        return $this->entityForm;
    }

    public function saveBefore()
    {
        $this->_isSaveSuccess = false;
        $this->eventsManager->fire("entity:saveBefore", $this);
        return $this->entityForm;
    }

    public function save()
    {
        $this->saveBefore();
        if ($this->entityForm === false) {
            return false;
        }
        $options = $this->entityForm->getUserOptions();
        $entityModelName = $this->_entityInfo['entityModel'];
        $connectName = 'get' . ucfirst($this->_connect);
        $db = $this->getDI()->{$connectName}();
        $db->begin();
        if (isset($options['id'])) {
            $this->entityModel = $entityModelName::findFirst($options['id']);
            if (!$this->entityModel) {
                throw new Exception('内容不存在');
            }
        } else {
            $this->entityModel = new $entityModelName();
        }
        $data = $this->entityForm->getData();
        $this->entityModel->contentModel = $options['contentModel'];
        foreach ($this->entityForm->getElements() as $fieldKey => $field) {
            $elementOptions = $field->getUserOptions();
            if (isset($elementOptions['baseField']) && $elementOptions['baseField'] == true) {
                //添加基本字段
                if (is_array($data[$fieldKey])) {
                    $data[$fieldKey] = json_encode($data[$fieldKey]);
                }
                if (isset($data[$fieldKey])) {
                    $this->entityModel->{$fieldKey} = $data[$fieldKey];
                } elseif (isset($elementOptions['default'])) {
                    $this->entityModel->{$fieldKey} = $elementOptions['default'];
                }
            } elseif (isset($elementOptions['addition']) && $elementOptions['addition'] == true) {
                if (!isset($data[$fieldKey]) && isset($elementOptions['default']) && isset($elementOptions['required']) && $elementOptions['required'] == false) {
                    $data[$fieldKey] = $elementOptions['default'];
                }
                if (isset($data[$fieldKey])) {
                    if (!isset($elementOptions['model'])) {
                        $elementOptions['model'] = '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($fieldKey);
                    }
                    $elementOptions['entityId'] = $this->_entityId;
                    $elementOptions['contentModel'] = $options['contentModel'];
                    $elementOptions['fieldName'] = $fieldKey;
                    $model = $elementOptions['model'];
                    if (isset($elementOptions['maxNum']) && $elementOptions['maxNum'] > 1) {
                        $fieldModelList = array();
                        if (is_string($data[$fieldKey])) {
                            $data[$fieldKey] = explode(',', trim($data[$fieldKey], ' ,'));
                        }
                        $newData = $data[$fieldKey];
                        $fieldList = $model::findByEid($this->entityModel->id);
                        foreach ($fieldList as $i => $fieldModel) {
                            if (isset($newData[$i])) {
                                $fieldModel->setOptions($elementOptions);
                                $fieldModel->setValue($newData[$i]);
                                $fieldModelList[] = $fieldModel;
                                unset($newData[$i]);
                            } else {
                                $fieldModel->delete();
                            }
                        }
                        foreach ($newData as $value) {
                            if ($value) {
                                $fieldModel = new $model();
                                $fieldModel->eid = $this->entityModel->id;
                                $fieldModel->setOptions($elementOptions);
                                $fieldModel->setValue($value);
                                $fieldModelList[] = $fieldModel;
                            }
                        }
                    } else {
                        $fieldModel = $model::findFirstByEid($this->entityModel->id);
                        if (!$fieldModel) {
                            $fieldModel = new $model();
                            $fieldModel->eid = $this->entityModel->id;
                        }
                        $fieldModel->setOptions($elementOptions);
                        $fieldModel->setValue($data[$fieldKey]);
                        $fieldModelList = $fieldModel;
                    }
                    $this->entityModel->{$fieldKey} = $fieldModelList;
                }
            }
        }
        if ($this->entityModel->save()) {
            $this->getDI()->getFlash()->success('内容保存成功');
            $db->commit();
            $this->saveAfter();
            return $this->entityModel;
        } else {
            $error = '';
            foreach ($this->entityModel->getMessages() as $message) {
                $error .= $message . "<br>";
            }
            $this->getDI()
                ->getFlash()
                ->error('内容保存失败<br>' . $error);
            $db->rollback();
            return false;
        }
    }

    public function saveAfter()
    {
        $this->_isSaveSuccess = true;
        $this->eventsManager->fire("entity:saveAfter", $this);
    }

    public function getContentModelInfo($contentModel, $key = null)
    {
        $contentModelList = $this->getContentModelList();
        if (!isset($contentModelList[$contentModel])) {
            return false;
        }
        if (is_null($key) && isset($contentModelList[$contentModel][$key])) {
            return $contentModelList[$contentModel][$key];
        }
        return $contentModelList[$contentModel];
    }

    public function menuTabs()
    {
        return false;
    }

    public function hasContentModel($contentModel)
    {
        $contentModelList = Config::get('m.' . $this->_module . '.entity' . ucfirst($this->_entityId) . 'ContentModelList');
        if (isset($contentModelList[$contentModel])) {
            return true;
        }
        return false;
    }

    public function getFields($contentModel = null)
    {
        $baseFields = Config::get($this->_module . '.' . $this->_entityId . 'BaseFields', array());
        $contentModelList = Config::get('m.' . $this->_module . '.entity' . ucfirst($this->_entityId) . 'ContentModelList');
        if (is_null($contentModel)) {
            foreach ($contentModelList as $model) {
                $fields = array();
                if (isset($model['fields'])) {
                    if (is_array($model['fields'])) {
                        $fields = $model['fields'];
                    } elseif (is_string($model['fields'])) {
                        $fields = Config::get($model['fields'], array());
                    }
                }
                $baseFields = array_merge($fields, $baseFields);
            }
        } else {
            if (isset($contentModelList[$contentModel])) {
                $model = $contentModelList[$contentModel];
                $fields = array();
                if (isset($model['fields'])) {
                    if (is_array($model['fields'])) {
                        $fields = $model['fields'];
                    } elseif (is_string($model['fields'])) {
                        $fields = Config::get($model['fields'], array());
                    }
                }
                $baseFields = array_merge($fields, $baseFields);
            } else {
                throw new Exception('参数错误，实体类型不存在');
            }
        }
        if (!is_null($contentModel)) {
            $baseFields['settings']['contentModel'] = $contentModel;
        }
        return $baseFields;
    }

    public function getEntityInfo($infoKey = null)
    {
        if (is_null($infoKey)) {
            return $this->_entityInfo;
        } else {
            return $this->_entityInfo[$infoKey];
        }
    }

    public function find($query = array())
    {
        //Config::printCode($query);
        $fields = $this->getFields();
        $fieldElements = fieldsInit($fields);
        $modelClassName = $this->_entityInfo['entityModel'];
        $this->_query = $modelClassName::query();
        if (!isset($query['all'])) {
            $query['andWhere'][] = array(
                'conditions' => '%created% < :now:',
                'bind' => array(
                    'now' => time()
                )
            );
        }
        foreach (array('join', 'leftJoin', 'rightJoin', 'innerJoin') as $condition) {
            if (isset($query[$condition]) && !empty($query[$condition]) && is_array($query[$condition])) {
                foreach ($query[$condition] as $item) {
                    $field = $item['id'];
                    if (isset($modelList[$field])) {
                        $this->_query = $this->_query->{$condition}($modelList[$field]['entity'], $item['conditions'], $field);
                        if (isset($item['columns']) && is_array($item['columns'])) {
                            $columns = array_merge($columns, $item['columns']);
                        } else {
                            $joinColumns = $modelList[$field]['columns'];
                            if (isset($item['exColumns']) && is_array($item['exColumns'])) {
                                foreach ($item['exColumns'] as $ec) {
                                    $ek = array_search($ec, $joinColumns);
                                    if ($ek !== false) {
                                        unset($joinColumns[$ek]);
                                    }
                                }
                            }
                            $columns = array_merge($columns, $joinColumns);
                        }

                    }
                }
                unset($joinColumns);
            }
        }
        /*
         * array('conditions'=>'','bind'=>'','type'=>'')
         */
        foreach (array('where', 'andWhere', 'orWhere', 'inWhere', 'notInWhere') as $condition) {
            if (isset($query[$condition]) && !empty($query[$condition]) && is_array($query[$condition])) {
                foreach ($query[$condition] as $item) {
                    $item['conditions'] = preg_replace_callback('|%([a-zA-Z_]+)%|', function ($matches) use ($fieldElements, $modelClassName,$condition) {
                        $fieldName = $matches[1];
                        if (isset($fieldElements[$fieldName])) {
                            $fieldOptions = $fieldElements[$fieldName];
                                if (isset($fieldOptions['addition']) && $fieldOptions['addition'] === true) {
                                    $model = isset($fieldOptions['model']) ? $fieldOptions : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($matches[1]);
                                    $this->_query->leftJoin($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                                    return $matches[1] . '.value';
                                }
                        }
                        return $matches[1];
                    }, $item['conditions']);
                    if ($condition == 'inWhere' || $condition == 'notInWhere') {
                        $this->_query = $this->_query->{$condition}($item['conditions'], $item['bind']);
                    } else {
                        if (!isset($item['type'])) {
                            $item['type'] = null;
                        }
                        if (isset($item['conditions']) && isset($item['bind'])) {
                            $this->_query = $this->_query->{$condition}($item['conditions'], $item['bind'], $item['type']);
                        }
                    }
                }
            }
        }

        foreach (array('match') as $condition) {
            if (isset($query[$condition]) && !empty($query[$condition]) && is_array($query[$condition])) {
                foreach ($query[$condition] as $item) {
                    $item['conditions'] = preg_replace_callback('|%([a-zA-Z_]+)%|', function ($matches) use ($fieldElements, $modelClassName) {
                        $fieldName = $matches[1];
                        if (isset($fieldElements[$fieldName])) {
                            $fieldOptions = $fieldElements[$fieldName];
                            if (isset($fieldOptions['addition']) && $fieldOptions['addition'] === true) {
                                $model = isset($fieldOptions['model']) ? $fieldOptions : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($matches[1]);
                                $this->_query->join($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                                return $matches[1] . '.full_text';
                            }
                        }
                        return $matches[1];
                    }, $item['conditions']);
                    if (isset($item['in']) && $item['in'] === true) {
                        $this->_query = $this->_query->andWhere($item['conditions'], $item['bind']);
                    } else {
                        $this->_query = $this->_query->orWhere($item['conditions'], $item['bind']);
                    }
                }
            }
        }
        //$this->_query = $this->_query->columns($columns);
        if (isset($query['order'])) {
            $query['order'] = preg_replace_callback('|%([a-zA-Z_]+)%|', function ($matches) use ($fieldElements, $modelClassName) {
                $fieldName = $matches[1];
                if (isset($fieldElements[$fieldName])) {
                    $fieldOptions = $fieldElements[$fieldName];
                    if (isset($fieldOptions['addition']) && $fieldOptions['addition'] === true) {
                        $model = isset($fieldOptions['model']) ? $fieldOptions : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($matches[1]);
                        if (isset($fieldOptions['required']) && $fieldOptions['required'] === true) {
                            $this->_query->join($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                        } else {
                            $this->_query->leftJoin($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                        }
                        return $matches[1] . '.value';
                    }
                }
                return $matches[1];
            }, $query['order']);
            $this->_query = $this->_query->orderBy($query['order']);
        }
        if (isset($query['group'])) {
            $query['group'] = preg_replace_callback('|%([a-zA-Z_]+)%|', function ($matches) use ($fieldElements, $modelClassName) {
                $fieldName = $matches[1];
                if (isset($fieldElements[$fieldName])) {
                    $fieldOptions = $fieldElements[$fieldName];
                    if (isset($fieldOptions['addition']) && $fieldOptions['addition'] === true) {
                        $model = isset($fieldOptions['model']) ? $fieldOptions : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($matches[1]);
                        if (isset($fieldOptions['required']) && $fieldOptions['required'] === true) {
                            $this->_query->join($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                        } else {
                            $this->_query->leftJoin($model, $modelClassName . '.id = ' . $matches[1] . '.eid', $matches[1]);
                        }
                        return $matches[1] . '.value';
                    }
                }
                return $modelClassName . '.' . $matches[1];
            }, $query['group']);
            $this->_query = $this->_query->groupBy($query['group']);
        }

        if (isset($query['paginator']) && $query['paginator'] == true) {
            if (!isset($query['limit'])) {
                $query['limit'] = 20;
            }
            if (!isset($query['page'])) {
                $query['page'] = 1;
            }

            $output = new PaginationModel(array(
                'data' => $this->_query->execute(),
                'limit' => $query['limit'],
                'page' => $query['page'],
            ));

            return $output->getPaginate();
        }
        if (isset($query['limit']) === true) {
            if ($query['limit'] === 1) {
                return $this->_query->execute();
            } else {
                $this->_query = $this->_query->limit($query['limit']);
            }
        }
        return $this->_query->execute();
    }

    public function findFirst($query, $object = false)
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        $entityModel = $modelClassName::findFirst($query);
        if (!$entityModel) {
            return false;
        }
        if ($object === true) {
            return $entityModel;
        }
        $contentModel = $entityModel->contentModel;
        $output = $entityModel->toArray();
        $fields = fieldsInit($this->getFields($contentModel));
        foreach ($fields as $key => $field) {
            if (isset($field['addition']) && $field['addition'] == true && isset($entityModel->{$key})) {
                $fieldModelName = isset($field['model']) ? $field['model'] : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($key);
                $output[$key] = call_user_func_array($fieldModelName . '::filterValue', array($entityModel->{$key}, $field));

            }
        }
        return $output;
    }

    public function filterForm()
    {
        $filterFormInfo = Config::get($this->_module . '.' . $this->_entityId . 'FilterForm', array());
        $filterForm = $this->getDI()->getForm()->create($filterFormInfo);
        return $filterForm;
    }

    public function submitFilterForm($filterForm, $query)
    {
        $data = $filterForm->getData();
        foreach ($data as $key => $value) {
            if ($value != 'null' && !empty($value)) {
                $query['andWhere'][] = array(
                    'conditions' => "%$key% = :$key:",
                    'bind' => array(
                        $key => $value,
                    ),
                );
            }
        }
        return $query;
    }

    public function handleForm()
    {
        $handleFormInfo = Config::get($this->_module . '.' . $this->_entityId . 'HandleForm', array());
        $handleForm = $this->getDI()->getForm()->create($handleFormInfo);
        if ($handleForm->isValid()) {
            $this->handleSubmit($handleForm);
        }
        return $handleForm;
    }

    public function handleSubmit($form)
    {
        global $di;
        $data = $form->getData();
        $nodeList = $di->getShared('request')->getPost('checkAll');
        $temNodeList = [];
        foreach ($nodeList as $key => $value){
            $temNodeList[] = $key;
        }
        $query = array(
            'all' => true,
            'inWhere' => array(
                array(
                    'conditions' => '%id%',
                    'bind' => $temNodeList,
                ),
            ),
        );
        $entityList = $this->find($query);
        $output = ['delete' => 0, 'handle' => 0, 'error' => 0, 'deleteError' => 0];
        foreach ($entityList as $item) {
            if (isset($data['delete']) && $data['delete'] == true) {
                if ($item->delete()) {
                    $output['delete']++;
                } else {
                    $output['deleteError']++;
                }
            } else {
                $fields = $this->getFields();
                foreach ($data as $key => $value) {
                    if ($value !== 'null') {
                        $item->{$key} = $value;
                    }

                }
                if ($item->save()) {
                    $output['handle']++;
                } else {
                    $output['error']++;
                }
            }
        }

        $flash = '';
        foreach ($output as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'error':
                        $flash .= $value . '项内容更改属性失败；';
                        break;
                    case 'handle':
                        $flash .= $value . '项内容更改属性成功；';
                        break;
                    case 'delete':
                        $flash .= $value . '项内容删除成功；';
                        break;
                    case 'deleteError':
                        $flash .= $value . '项内容删除失败';
                        break;
                }
            }
        }
        if($flash){
            $di->getShared('flash')->notice($flash);
        }

        return $form;
    }

    public function delete($id)
    {
        $modelClassName = $this->_entityInfo['entityModel'];
        $entity = $modelClassName::findFirst($id);
        if ($entity && $entity->delete()) {
            return true;
        } else {
            return false;
        }
    }
}
