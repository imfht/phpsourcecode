<?php
namespace Modules\Entity\Entity;

use Core\Config;
use Exception;
use Modules\Entity\Entity\Fields;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Relation;

/**
 * Class Entity
 *
 * @package Core\Entity
 */
class EntityModel extends Model
{

    public $isNew = true;

    /**
     * entity base table name
     *
     * @var string
     */
    protected $_source;

    /**
     * entity type name
     *
     * @var string
     */
    protected $_entityId = 'user';

    protected $_connect = 'db';

    /**
     * entity label field name
     *
     * @var string
     */
    protected $_label = false;

    /**
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * entity field entity lists
     *
     * @var array[entity]
     */
    protected $_entitys;

    /**
     * entity field table object
     *
     * @var array[entity]
     */
    protected $_relation;

    /**
     * entity action links
     *
     * @var
     *
     */
    protected $_links;

    /**
     * entity module
     *
     * @var
     *
     */
    protected $_data = false;

    protected $_module;

    protected $_contentModelModule;

    protected $_types;

    protected $entityClassName;

    protected $_actions;

    /**
     * entity info
     *
     * @var array
     */
    protected $_entityInfo;

    protected $_entityName;

    public $id = false;

    public $isSave;

    public $saveState;

    protected $changed;

    protected $created;

    /**
     * entity content model
     *
     * @var string
     */
    public $contentModel;

    protected $contentModelList = false;

    public $thead;

    public $filterQuery = false;

    public $isChanged = true;

    public function initialize()
    {
        $this->_fields = fieldsInit($this->getDI()->getEntityManager()->get($this->_entityId)->getFields());
        foreach ($this->_fields as $fieldKey => $field) {
            if (isset($field['addition']) && $field['addition'] == true) {
                $fieldModelName = isset($field['model']) ? $field['model'] : '\Models\\' . ucfirst($this->_entityId) . 'Field' . ucfirst($fieldKey);
                if (isset($field['maxNum']) && $field['maxNum'] > 1) {
                    $this->hasMany('id', $fieldModelName, 'eid', array(
                        'alias' => $fieldKey,
                        'reusable' => true,
                        'foreignKey' => array(
                            'action' => Relation::ACTION_CASCADE,
                        ),
                    ));
                } else {
                    $this->hasOne('id', $fieldModelName, 'eid', array(
                        'alias' => $fieldKey,
                        'reusable' => true,
                        'foreignKey' => array(
                            'action' => Relation::ACTION_CASCADE,
                        ),
                    ));
                }
            }
        }
    }



    public function beforeValidationOnCreate()
    {
        if (!$this->created) {
            $this->created = time();
        }
        if(!$this->changed) {
            $this->changed = time();
        }
    }

    public function beforeValidationOnUpdate()
    {
        if (!$this->created) {
            $this->created = time();
        }
        if(!$this->changed) {
            $this->changed = time();
        }
    }

    public function getTitle()
    {
        $this->setFields();
        $entityForm = $this->getDI()->getForm()->create($this->_fields);
        foreach ($entityForm->getElements() as $key => $element) {
            $options = $element->getUserOptions();
            if (isset($options['isTitle']) && $options['isTitle'] === true) {
                return $this->{$key} ? $this->{$key}->value : '';
            }
        }
        return '没有设置默认标题';
    }

    public function getEntityId()
    {
        return $this->_entityId;
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function setLinks($links)
    {
        $this->_links = $links;
    }

    public function setContentModelList()
    {
        $this->contentModelList = Config::get('m.' . $this->_module . '.entity' . ucfirst($this->_entityId) . 'ContentModelList');
    }

    public function getContentModelList()
    {
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        return $this->contentModelList;
    }

    public function getContentModelInfo($contentModel = null, $key = null)
    {
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        if (is_null($contentModel)) {
            $contentModel = $this->contentModel;
        }
        if (!isset($this->contentModelList[$contentModel])) {
            throw new \Exception($this->_entityName . '实体：内容类型：' . $contentModel . '不存在');
        }
        if (is_null($key)) {
            return $this->contentModelList[$contentModel];
        }
        if (isset($this->contentModelList[$contentModel][$key])) {
            return $this->contentModelList[$contentModel][$key];
        }
        return null;
    }

    public function menuGroup($type = null)
    {
        global $di;
        $actionMenu = array();
        $nodeType = $this->getContentModelList();
        foreach ($nodeType as $key => $value) {
            $value['access'] = (string)$value['access'];
            if (isset($value['access']) && $value['access'][0] == 1) {
                $actionMenu[$key] = array(
                    'href' => $di->getShared('url')->get(array('for' => 'adminEntityEdit', 'entity' => 'node', 'contentModel' => $key, 'id' => 0)),
                    'name' => $value['modelName'],
                );
                if ($type && $type == $key) {
                    $actionMenu[$key]['active'] = true;
                } else {
                    $actionMenu[$key]['active'] = false;
                }
            }
        }
        return $actionMenu;
    }

    //获取实体信息
    public function getEntityInfo($key = null)
    {
        if ($key && isset($this->_entityInfo[$key])) {
            return $this->_entityInfo[$key];
        }
        return $this->_entityInfo;
    }

    public function setEntityName($entityName)
    {
        $this->_entityName = $entityName;
    }

    public function getEntityName()
    {
        return $this->_entityName;
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setModule($module)
    {
        $this->_module = $module;
    }

    public function filterForm()
    {
        $filterFormInfo = Config::get($this->_module . '.' . $this->_entityId . 'FilterForm', array());
        $filterForm = $this->getDI()->getForm()->create($filterFormInfo);
        if ($filterForm->isValid()) {
            $this->filterQuery = $this->_submitFilterForm($filterForm);
        }
        return $filterForm;
    }

    protected function _submitFilterForm($form)
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        $data = $form->getData();
        $query = array();
        $formEntity = $form->formEntity;
        $i = 0;
        foreach ($data as $key => $value) {
            if ($value && isset($this->_fields[$key])) {
                if (!isset($formEntity[$key]['settings']['conditions'])) {
                    $formEntity[$key]['settings']['conditions'] = 'andWhere';
                }
                $conditions = $formEntity[$key]['settings']['conditions'];
                $query[$conditions][$i][] = "%$key% = :$key:";
                $query[$conditions][$i][] = array($key => $value);
            }
        }
        return $query;
    }

    public function setFields($fields = array())
    {
        if (isset($this->contentModel) && $this->contentModel) {
            $this->_fields = $this->getDI()->getEntityManager()->get($this->_entityId)->getFields($this->contentModel);
        } else {
            $this->_fields = $this->getDI()->getEntityManager()->get($this->_entityId)->getFields();
        }
    }

    public function getFields()
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        return $this->_fields;
    }

    public function getContentModelFields()
    {
        if (!$this->contentModel) {
            throw new \Exception('参数错误，内容模型没有被设置');
        }
        $fields = $this->getContentModelInfo($this->contentModel, 'fields');
        if (!$fields) {
            return array();
        }
        if (is_string($fields)) {
            $fields = Config::get($fields, array());
        }
        return $fields;
    }

    public function getLabel()
    {
        if (!$this->_label) {
            if (!$this->_fields) {
                $this->setFields();
            }
            // Config::printCode($this->_fields);
            foreach ($this->_fields as $key => $value) {
                if (isset($value['isLabel']) && $value['isLabel'] == true) {
                    $this->_label = $key;
                    break;
                }
            }
        }
        // echo $this->_label;
        if ($this->_label && isset($this->_fields['addition']) && $this->_fields['addition'] === true) {
            return $this->{$this->_label}->value;
        }
        return $this->{$this->_label};
    }

    public function url()
    {
        $this->getDI()
            ->getUrl()
            ->get(array(
                'for' => 'entity',
                'id' => $this->id,
            ));
    }

    public function listUrl()
    {
        $this->getDI()
            ->getUrl()
            ->get(array(
                'for' => 'entityList',
                'page' => 1,
                'entity' => $this->_entityId,
            ));
    }

    public function getEntity($name)
    {
        if (isset($this->_entitys[$name])) {
            return $this->_entitys[$name];
        }
        if (!isset($this->_fields)) {
            $this->setFields();
        }
        if (!isset($this->_fields[$name]) && !isset($this->_fields[$name]['entity'])) {
            throw new Exception('参数错误，字段不存在，或者该字段不是实体字段');
        }
        $entity = $this->_fields[$name]['entity'];
        if (!isset($this->_fields[$name]['number'])) {
            $this->_fields[$name]['number'] = 1;
        }
        $query = array();
        if ($this->_fields[$name]['number'] == 1) {
            $query['limit'] = 1;
            $this->_entitys[$name] = $this->getDI()->getEntityManager()->getEntity($entity)->gets($query);
        } else {
            foreach ($this->$name as $item) {
                $this->_entitys[$name] = $this->getDI()->getEntityManager()->getEntity($entity)->gets($query);
            }
        }
        return $this->_entitys[$name];
    }

    public function getRelationField($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        if (!isset($this->_fields[$name])) {
            return null;
        }
        if (!isset($this->_relation[$name])) {
            $this->getRelation($name);
        }
        if ($this->_relation[$name]) {
            return null;
        }
        $output = array();
        if (is_array($this->_relation[$name])) {
            foreach ($this->_relation[$name] as $relation) {
                $output[] = $relation->getValue();
            }
        } else {
            $output = $this->_relation[$name]->getValue();
        }
        $this->{$name} = $output;
        return $output;
    }

    public function getData()
    {
        if ($this->_data !== false) {
            return $this->_data;
        }
        if ($this->id === false) {
            return false;
        }
        $fields = $this->getFields();
        foreach ($fields as $fieldKey => $field) {
            $this->_data[$fieldKey] = $this->__get($fieldKey);
        }
    }

    public function links()
    {
        $this->_links = array(
            'edit' => array(
                'href' => array(
                    'for' => 'adminEntityEdit',
                    'entity' => $this->_entityId,
                    'contentModel' => $this->contentModel,
                    'id' => $this->id,
                ),
                'icon' => 'info',
                'name' => '编辑',
            ),
            'delete' => array(
                'href' => array(
                    'for' => 'adminEntityDelete',
                    'entity' => $this->_entityId,
                    'id' => $this->id,
                ),
                'icon' => 'danger',
                'name' => '删除',
            ),
        );
        if ($this->getDI()->getEventsManager()->fire('entity:links', $this) === false) {
            return false;
        }
        return $this->_links;
    }

}
