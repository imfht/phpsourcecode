<?php
namespace Modules\Config\Entity;

use Core\Config as CoreConfig;
use Modules\Entity\Entity\Fields;
use Phalcon\Exception;
use Phalcon\Mvc\User\Plugin;

/**
 */
class Config extends Plugin
{

    protected $_module = 'config';

    protected $_list = false;

    protected $_source = 'm.config.entityConfigContentModelList';

    protected $entityId = 'config';

    public $isNew = true;

    /**
     * entity type name
     *
     * @var string
     */
    protected $_entity;

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

    protected $_contentModelModule;

    protected $_types;

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

    /**
     * entity content model
     *
     * @var string
     */
    public $contentModel;

    public $contentModelList;

    public $thead;

    public function getLabel()
    {
        if (!$this->contentModel) {
            throw new Exception('参数错误，没有设置内容模型');
        }
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        if (!isset($this->contentModelList[$this->contentModel])) {
            throw new \Exception('参数错误，内容类型不存在');
        }
        return $this->contentModelList[$this->contentModel]['modelName'];
    }

    public function getEntityId()
    {
        return $this->_entityId;
    }
    public static function findFirst($query)
    {
        if(is_string($query)){
            $query = array('contentModel' => $query);
        }
        $contentModel = $query['contentModel'];
        $contentModelList = CoreConfig::get('m.config.entityConfigContentModelList');
        if (!isset($contentModelList[$contentModel])) {
            throw new Exception('参数错误，字段不存在');
        }
        $configFields = CoreConfig::get($contentModelList[$contentModel]['fields'], array());
        if (!isset($configFields['settings']['data'])) {
            throw new Exception('参数错误，字段不存在');
        }
        $data = new Config();
        foreach (CoreConfig::get($configFields['settings']['data'], array()) as $key => $value) {
            $data->{$key} = $value;
            $data->contentModel = $contentModel;
        }
        return $data;
    }

    public function setEntityInfo($info)
    {
        $this->_entityInfo = $info;
        $this->_module = $info['module'];
        $this->_entityName = $info['name'];
        $this->_source = $info['source'];
        $this->_entity = $info['id'];
        $this->setContentModelList();
    }

    public function getLinks()
    {
        return $this->_links;
    }

    public function setLinks($links)
    {
        $this->_links = $links;
    }

    public function getContentModelInfo($contentModel = null, $key = null)
    {
        if (is_null($contentModel)) {
            $contentModel = $this->contentModel;
        }
        if (!$this->contentModelList) {
            $this->setContentModelList();
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

    public function links()
    {
        $this->_links = array(
            'edit' => array(
                'href' => array(
                    'for' => 'adminConfigEdit',
                    'contentModel' => $this->contentModel,
                ),
                'icon' => 'info',
                'name' => '编辑',
            ),
        );
        if ($this->getDI()->getEventsManager()->fire('entity:links', $this) === false) {
            return false;
        }
        return $this->_links;
    }

    public function filterForm()
    {
        return false;
    }

    public function menuGroup($type = null)
    {
        return false;
    }

    public static function find($query = array())
    {
        $contentModelList = CoreConfig::get('m.config.entityConfigContentModelList', array());
        $list = new \stdClass();
        foreach ($contentModelList as $key => $value) {
            $item = new Config();
            foreach ($value as $k => $v) {
                $item->{$k} = $v;
            }
            $item->contentModel = $key;
            $list->{$key} = $item;
        }
        return $list;
    }

    public function saveEntity($form)
    {
        $this->isSave = true;
        $data = $form->getData();
        if (Cconfig::set($this->_dataConfig, $data)) {
            $this->getDI()
                ->getFlash()
                ->success('保存成功');
            return true;
        } else {
            $this->getDI()
                ->getFlash()
                ->error('保存失败');
            return false;
        }
    }

    public function delete()
    {
        if (!$this->_list) {
            $this->gets();
        }
        if (!$id) {
            $id = $this->id;
        }
        if (isset($this->_list[$id])) {
            unset($this->_list[$id]);
            $this->getDI()
                ->getFlash()
                ->success('删除成功');
            return Cconfig::set($this->_dataConfig, $this->_list);
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
        if ($this->id && isset($this->_list[$this->id])) {
            $this->_data = $this->_list[$this->id];
            return $this->_data;
        }
        return array();
    }

    public function setFields($fields = array())
    {
        if (!$this->contentModel) {
            throw new Exception("参数错误，内容类型没有被设置");
        }
        if (!$this->contentModelList) {
            $this->setContentModelList();
        }
        if (!isset($this->contentModelList[$this->contentModel])) {
            throw new \Exception('参数错误，内容类型不存在');
        }
        $contentModelInfo = $this->contentModelList[$this->contentModel];
        if (is_array($contentModelInfo['fields'])) {
            $this->_fields = $contentModelInfo['fields'];
        } elseif (is_string($contentModelInfo['fields'])) {
            $this->_fields = CoreConfig::get($contentModelInfo['fields'], array());
        }
        if (isset($this->_fields['settings']['data'])) {
            $this->_dataConfig = $this->_fields['settings']['data'];
        } else {
            throw new Exception("参数错误，保存位置没有被设置：");
        }
        return $this->_fields;
    }

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

    public function setContentModelList()
    {
        $this->contentModelList = CoreConfig::get('m.' . $this->_module . '.entity' . ucfirst($this->_entityId) . 'ContentModelList');
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setModule($module)
    {
        $this->_module = $module;
    }

    public function getSource()
    {
        return $this->_source;
    }

    public function setSource($source)
    {
        $this->_source = $source;
    }

    public function get($name)
    {
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        if (!$this->_fields) {
            $this->setFields();
        }
        if (!isset($this->_fields[$name])) {
            return null;
        }
        if (isset($this->_fields[$name]['addition'])) {
            $this->getRelation($name);
        }
        if (isset($this->{$name})) {
            return $this->{$name};
        }
        return null;
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
            $fields = CoreConfig::get($fields, array());
        }
        return $fields;
    }

    public function rendersLabel()
    {
        return '<a href="#" data-toggle="tooltip" target="_blank" data-placement="right" title="访问{{ item.title }}">测试</a>';
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
                'entity' => $this->_entity,
            ));
    }

    public function saveEntityBefore()
    {
    }

    public function handleForm()
    {
        $handleFormInfo = CoreConfig::get($this->_module . '.' . $this->_entity . 'HandleForm');
        $handleForm = $this->getDI()->getForm->create($handleFormInfo);
        if ($handleForm->isValid()) {
            $handleForm->_submitHandleForm($handleForm);
        }
        return $handleForm;
    }

    public function editForm($fields = false)
    {
        if (!$this->_fields) {
            $this->setFields();
        }
        if (!$this->_data) {
            $this->getData();
        }
        if ($this->_data !== false) {
            $this->isNew = false;
        }
        if ($fields === false) {
            $baseForm = $this->_fields;
        } else {
            $baseForm = $fields;
        }

        if (!isset($baseForm['form'])) {
            $baseForm['form'] = array(
                'method' => 'post',
                'class' => array(),
                'accept-charset' => 'utf-8',
                'role' => 'form',
            );
        }
        if (!$this->id) {
            $baseForm['formId'] = 'nodeAdd';
        } else {
            $baseForm['formId'] = 'nodeEdit';
        }
        $formInfo = Fields::toFormEntity($baseForm);
        $form = $this->getDI()->getForm()->create($formInfo, $this->_data);
        if ($form->isValid()) {
            $this->saveEntity($form);
        }
        return $form;
    }

    public function handle()
    {

    }

    public function deleteEntity()
    {

        $connectName = 'get' . ucfirst($this->_connect);
        $db = $this->getDI()->{$connectName}();
        $db->begin();
        $state = true;
        if (!$this->_fields) {
            $this->setFields();
        }
        foreach ($this->_fields as $fieldKey => $field) {
            if (isset($field['addition']) && $field['addition'] == true) {
                if (!isset($field['model'])) {
                    $modelName = ucfirst($this->_entity) . 'Field' . ucfirst($fieldKey);
                    $field['model'] = '\Models\\' . $modelName;
                }

                $model = $field['model'];
                $fieldModel = $model::findFirstByEid($this->id);
                if ($fieldModel) {
                    if (!$fieldModel->delete()) {
                        $state = false;
                        break;
                    }
                }
            }
        }
        if ($state === true) {
            if ($this->delete()) {
                $this->getDI()->getFlash()->success('内容删除成功');
                $db->commit();
            } else {
                $error = '';
                foreach ($this->getMessages() as $message) {
                    $error .= $message . "<br>";
                }
                $this->getDI()->getFlash()->error('内容主体删除失败<br>' . $error);
                $db->rollback();
            }
        } else {
            $error = '';
            foreach ($fieldModel->getMessages() as $message) {
                $error .= $message . "<br>";
            }
            $this->getDI()->getFlash()->error('内容字段删除失败<br>' . $error);
            $db->rollback();
        }
    }

    public function __get($valueName)
    {
        $valueName = strtolower($valueName);
        // 返回私有属性
        if (method_exists($this, 'get' . ucfirst($valueName))) {
            $methodName = 'get' . ucfirst($valueName);
            return $this->{$methodName}();
        }
        return null;
    }
}
