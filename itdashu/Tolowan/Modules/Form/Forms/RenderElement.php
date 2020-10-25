<?php
namespace Modules\Form\Forms;

use Core\Config;

class RenderElement
{
    public $templates;
    public $form;
    public $widget;
    public $name;
    public $layout;
    public $label;
    public $description;
    public $error;
    public $entity;
    public $element;
    public $formEntity;
    public $formId;
    public $hasError;
    public $options;

    public function __construct($name, $form)
    {
        global $di;
        $element = $form->getElement($name);
        $this->options = $element->getUserOptions();
        $this->{'#module'} = 'form';
        $this->form = $form;
        $this->name = $name;
        $this->formEntity = $form->formEntity;
        $this->formId = $form->formId;
        $this->widget = $this->options['widget'];
        $this->layout = $form->layout;
        $this->{'#templates'} = array(
            'base' => 'formElement',
            'formIdBase' => 'formElement-' . $this->formId,
            'widget' => 'formElement-' . $this->options['widget'],
            'widgetLayout' => 'formElement-' . $this->options['widget'] . '-' . $this->layout,
            'formIdWidget' => 'formElement-' . $this->formId . '-' . $this->options['widget'],
            'formIdWidgetLayout' => 'formElement-' . $this->formId . '-' . $this->options['widget'] . '-' . $this->layout,
            'formIdElement' => 'formElement-' . $this->formId . '-' . $name,
        );
        $this->hasError = $form->hasError($name);
        $this->label = $element->getLabel();
        $this->description = $this->options['description'];
        $this->error = $this->options['error'];
        $this->element = $element;
        if ($di->getShared('eventsManager')->fire('formRenderElement:' . $this->widget, $this) === false) {
            return false;
        }
    }

    public function render()
    {
        if (isset($this->options['isCanAccess']) && $this->options['isCanAccess'] == false) {
            return '';
        }
        return (array)$this;
    }
}
