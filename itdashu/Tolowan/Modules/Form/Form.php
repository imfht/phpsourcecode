<?php
namespace Modules\Form;

use Core\Config;
use Modules\Form\Forms\FormInit;
use Modules\Form\Forms\RenderElement;
use Phalcon\Exception;
use Phalcon\FilterInterface;
use Phalcon\Forms\Form as Pform;
use Phalcon\Security\Random;

class Form extends Pform
{
    public $formEntity;
    public $formId;
    public $action;
    public $formError = array();
    public $formName;
    public $layout;
    protected $_hasSubmit = false;
    protected $_canValid = false;
    protected $_eventsManager;
    protected $_renderElement;

    protected $_attributes = array();

    //ajax-submit
    public function __construct($formEntity = false, $data = array(), $options = array())
    {
        $this->_eventsManager = $this->getDI()->getEventsManager();
        if (is_string($formEntity)) {
            $formEntity = Config::get($formEntity, array());
        }
        if (!$formEntity) {
            throw new Exception('表单数据不合法');
        } else {
            $this->formEntity = $formEntity;
        }

        $this->formEntity['form'] = array_merge(array(
            'method' => 'post',
            'class' => '',
            'accept-charset' => 'utf-8',
        ), $this->formEntity['form']);
        if (empty($this->formEntity['form']['action'])) {
            $this->formEntity['form']['action'] = $this->getDI()->getRequest()->getURI();
        }
        $this->_attributes = $this->formEntity['form'];
        $this->_action = $this->formEntity['form']['action'];
        if (isset($this->formEntity['layout'])) {
            $this->layout = $this->formEntity['layout'];
        } else {
            $this->layout = 'default';
        }
        if (isset($this->formEntity['formName'])) {
            $this->formName = $this->formEntity['formName'];
        }
        $this->formId = $formEntity['formId'];
        $this->_options = array_merge(array(
            'checkToken' => true,
            'validation' => true,
        ), $this->formEntity['settings'], $options);
        if (isset($this->_options['checkToken']) && $this->_options['checkToken'] == true) {
            $this->_attributes['data-toggle'] = 'validator';
        }
        if (is_object($data)) {
            $data = (array) $data;
        }
        if (empty($data)) {
            switch ($this->formEntity['form']['method']) {
                case 'post':
                    $data = $this->getDI()->getRequest()->getPost();
                    break;
                default:
                    $data = $this->getDI()->getRequest()->getQuery();
                    break;
            }
        }
        if (empty($data) && isset($this->_options['configId'])) {
            $data = Config::get($this->_options['configId'], array());
        }
        $this->_data = $data;
        if ($this->_eventsManager->fire('form:initialize', $this) === false) {
            return false;
        }
        $this->addField();
    }

    public function setAttribute($attribute, $value)
    {
        $this->_attributes[$attribute] = $value;
    }

    public function getAttribute($attribute)
    {
        if (isset($this->_attributes[$attribute])) {
            return $this->_attributes[$attribute];
        }
        return false;
    }

    public function setAttributes($attributes)
    {
        $this->_attributes = array_merge($this->_attributes, $attributes);
    }

    public function getAttributes()
    {
        return $this->_attributes;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function isValid($data = null, $entity = null)
    {
        if ($this->hasSubmit() !== true) {
            return false;
        }
        $this->_canValid = true;
        if (!isset($this->_options['checkToken'])) {
            $this->_options['checkToken'] = true;
        }
        if ($this->_options['checkToken'] === true) {
            if ($this->checkToken() == false) {
                $this->getDI()->getFlash()->error('表单提交失败，您的访问被认为是跨站攻击，请刷新重试');
                return false;
            }
        }
        $this->filterData();
        return parent::isValid($data, $entity);
    }

    protected function filterData()
    {
        $filter = null;
        $data = array();
        if (strtolower($this->_attributes['method']) == 'get') {
            $method = 'getQuery';
        } else {
            $method = 'get' . ucfirst($this->formEntity['form']['method']);
        }
        $this->_data = $this->getDI()->getRequest()->{$method}();
        foreach ($this->_elements as $key => $element) {
            $elementOptions = $element->getUserOptions();
            if (isset($elementOptions['isCanAccess']) && $elementOptions['isCanAccess'] === false) {
                $value = isset($elementOptions['default']) ? $elementOptions['default'] : '';
            } else {
                //Get the element
                $value = $element->getValue();
                if (is_null($value)) {
                    continue;
                }
                //Apply filters
                $filters = $element->getFilters();
                if (isset($filters) === true) {
                    if (is_object($filter) === false) {
                        //@note no further validation
                        $filter = $this->getDi()->getShared('filter');
                        if (is_object($filter) === false ||
                            $filter instanceof FilterInterface === false
                        ) {
                            throw new Exception('Wrong filter service.');
                        }
                    }

                    $value = $filter->sanitize($value, $filters);
                }
            }

            $data[$key] = $value;
        }
        $this->_data = $data;
    }

    public function hasSubmit()
    {
        if ($this->_hasSubmit === true) {
            return true;
        }
        $method = 'is' . ucfirst($this->formEntity['form']['method']);
        if ($this->getDI()->getRequest()->{$method}() && $this->getDI()->getRequest()->has($this->formId)) {
            $this->_hasSubmit = true;
            return true;
        }
        return false;
    }

    public function hasError($element)
    {
        if ($this->_canValid !== true) {
            return false;
        }
        if (isset($this->_messages[$element])) {
            return $this->_messages[$element];
        } else {
            return false;
        }
    }

    public function addField($entity = null)
    {
        if (is_null($entity)) {
            $entity = $this->formEntity;
        }
        foreach ($entity as $fkey => $fvalue) {
            if (isset($fvalue['widget'])) {
                $this->_addField($fkey, $fvalue);
            }
        }
    }

    private function _addField($name, $element)
    {
        if (isset($element['settings']['display'])) {
            if ($element['settings']['display'] === false) {
                return false;
            }
        }
        if (isset($element['access'])) {
            if ($this->getDI()->getSecurity()->isCanAccess($element['access']) === false) {
                $element['isCanAccess'] = false;
                return false;
            }
        }
        $element['name'] = $name;
        if (!isset($this->_data[$name])) {
            $this->_data[$name] = '';
        }
        if (FormInit::callField($name, $element) === false) {
            return false;
        }
        if (isset(FormInit::$element[$element['widget']])) {
            $field = FormInit::callElement($this, $element);
            if ((!isset($this->_data[$name]) || empty($this->_data[$name])) && isset($element['default'])) {
                $this->_data[$name] = $element['default'];
            }
            $field->setLabel($element['label']);
            $field->setDefault($this->_data[$name]);
            $field->setUserOptions($element);
            if (isset($element['required']) && $element['required'] == true) {
                $element['validate'][] = array(
                    'v' => 'PresenceOf',
                );
            }
            if (isset($element['validate'])) {
                foreach ($element['validate'] as $validate) {
                    FormInit::callValidate($field, $validate);
                }
            }
            if (!isset($element['filter'])) {
                $element['filter'] = array();
            }
            foreach ($element['filter'] as $value) {
                $field->addFilter($value);
            }
            $this->add($field);
        }
    }

    public function isExistElement($element)
    {
        return isset($this->_elements[$element]);
    }

    public function renderForm($module = 'form')
    {
        global $di;
        $di->getShared('assets')
            ->addJs('jquery-validate', '//cdn.bootcss.com/jquery-validate/1.15.1/jquery.validate.min.js', 'footer');

        $output = array(
            '#templates' => array(
                'form',
                'form-' . $this->layout,
                'form-' . $this->formId,
            ),
            '#module' => $module,
            'formName' => $this->formName,
            'layout' => $this->layout,
            'id' => $this->formId,
            'method' => $this->formEntity['form']['method'],
            'action' => $this->formEntity['form']['action'],
            'data' => $this,
        );
        return $output;
    }

    public function renderElement($name)
    {
        if (isset($this->_renderElement[$name])) {
            return $this->_renderElement[$name];
        } else {
            $this->_renderElement[$name] = new RenderElement($name, $this);
        }
        return $this->_renderElement[$name];
    }

    public function checkToken()
    {
        $tokenKey = $this->session->get('form:' . $this->formId . 'Key');
        $tokenValue = $this->session->get('form:' . $this->formId . 'Value');
        if ($this->request->has($tokenKey)) {
            if ($tokenValue == $this->request->get($tokenKey)) {
                return true;
            }
        }
        return false;
    }

    /*
     * 获取表单令牌
     */
    public function getToken()
    {
        $random = new Random();
        $token = $random->hex(10);
        $this->session->set('form:' . $this->formId . 'Value', $token);
        return $token;
    }

    public function getTokenKey()
    {
        $random = new Random();
        $token = $random->hex(10);
        $this->session->set('form:' . $this->formId . 'Key', $token);
        return $token;
    }

    public function csrf()
    {
        return '<input type="hidden" name="' . $this->getTokenKey() . '" value="' . $this->getToken() . '" />';
    }

    public function saveBefore($hook = null)
    {
        if (is_null($hook)) {
            $hook = 'form:save';
        }
        $this->getDI()->getEventsManager()->fire($hook, $this);
    }

    public function save()
    {
        $formSave = false;
        $modelsList = Config::cache('modelsManager');
        if (isset($this->formEntity['settings']['save'])) {
            if (is_callable($this->formEntity['settings']['save'])) {
                $formSave = call_user_func($this->formEntity['settings']['save'], $this);
            } elseif (isset($modelsList[$this->formEntity['settings']['save']])) {
                $formSave = $this->saveModel();
            } elseif (isset($this->formEntity['settings']['configId']) && $this->formEntity['settings']['save'] == 'config') {
                $formSave = $this->saveConfig();
            }
        }
        // 保存表单项目
        if ($formSave !== false) {
            foreach ($this->_elements as $name => $element) {
                $element->setUserOption('id', $formSave);
                $settings = $element->getUserOptions();
                if (isset($settings['save']) && isset($this->_data[$name]) && is_callable($settings['save'])) {
                    $elementSave = call_user_func_array($settings['save'], array(&$element));
                    if (!$elementSave) {
                        $formSave = false;
                        break;
                    }
                }
            }
        }
        if ($formSave === false) {
            if (!isset($this->formEntity['settings']['error']) || empty($this->formEntity['settings']['error'])) {
                $this->formEntity['settings']['error'] = '提交失败';
            }
            $this->flash->error($this->formEntity['settings']['error']);
        } else {
            if (!isset($this->formEntity['settings']['success']) || empty($this->formEntity['settings']['success'])) {
                $this->formEntity['settings']['success'] = '提交成功';
            }
            $this->flash->success($this->formEntity['settings']['success']);
        }
        return $formSave;
    }

    protected function saveModel()
    {
        $modelsList = Config::cache('modelsManager');
        $save = $modelsList[$this->formEntity['settings']['save']]['entity'];
        if (!class_exists($save)) {
            $this->getDI()->getFlash()->error('模型：' . $save . '不是一个可访问的模型。');
            return false;
        }
        $db = $this->getDI()->getShared('db');
        $db->begin();
        if (isset($this->formEntity['settings']['id'])) {
            if (($entity = $save::findFirst($this->formEntity['settings']['id'])) === false) {
                return false;
            }
        } else {
            $entity = new $save();
        }
        //Config::printCode($this->_data);
        foreach ($this->_data as $key => $value) {
            $entity->{$key} = $value;
        }
        $save = $entity->save();
        if ($save) {
            $db->commit();
            return $save;
        } else {
            $db->rollback();
            $output = '';
            foreach ($entity->getMessages() as $message) {
                $output .= $message . '<br>';
            }
            $this->getDI()->getFlash()->error($output);
        }
    }

    protected function saveConfig()
    {
        if (!isset($this->formEntity['settings']['saveType'])) {
            $this->formEntity['settings']['saveType'] = 'cover';
        }
        $name = $this->formEntity['settings']['configId'];
        switch ($this->formEntity['settings']['saveType']) {
            case 'cover':
                return Config::set($name, $this->_data);
                break;
            case 'update':
                $config = Config::get($name);
                if (isset($this->formEntity['settings']['id'])) {
                    $id = $this->formEntity['settings']['id'];
                } elseif (isset($this->_data['id'])) {
                    $id = $this->_data['id'];
                } elseif (isset($this->_data['machine'])) {
                    $id = $this->_data['machine'];
                } else {
                    return false;
                }
                $config[$id] = $this->_data;
                return Config::set($name, $config);
                break;
            case 'merge':
                $config = Config::get($name, array());
                $config = array_merge($config, $this->_data);
                return Config::set($name, $config);
                break;
        }
        return false;
    }

    public function getElement($name)
    {
        return parent::get($name);
    }

    public function start($attributes = array())
    {
        $this->_attributes = array_merge($this->_attributes, $attributes);
        if (!isset($this->_attributes['id'])) {
            $this->_attributes['id'] = $this->formId;
        }
        if (isset($this->_options['validation']) && $this->_options['validation'] === true) {
            $this->getDI()->getAssets()->addInlineJs('formValidate' . ucfirst($this->formId), '$(\'#' . $this->_attributes['id'] . '\').validate({validClass: "has-success",});', 'footer');
        }
        $output = '<form ';
        $output .= renderAttributes($this->_attributes);
        $output .= '>';
        if (!isset($this->_options['checkToken']) || $this->_options['checkToken'] === true) {
            $output .= $this->csrf();
        }
        $output .= '<input type="hidden" name="' . $this->formId . '" value="' . $this->formId . '" />';
        return $output;
    }

    public function end()
    {
        return '</form>';
    }

    public function error($key)
    {
        if (isset($this->formError[$key])) {
            return 'error';
        } else {
            return '';
        }
    }

    public function submit()
    {
        return '<input type="hidden" value="' . $this->formId . '"><input type="submit" class="submit" value="提交">';
    }
}
