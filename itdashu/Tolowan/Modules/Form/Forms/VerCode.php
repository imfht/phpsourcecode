<?php
namespace Modules\Form\Forms;

use Phalcon\Forms\Element;

class VerCode extends Element
{
    public $options;
    public function setOptions($options)
    {
        $this->options = $options;
    }
    public function getOptions()
    {
        return $this->options;
    }
    public function doimg()
    {
        return '/validate_code';
    }
    public function render($attributes = null)
    {
        $output = '';
        $name = $this->getName();
        $default = $this->getDefault();
        $output = '<input type="text" class="form-control" name="' . $name . '" placeholder="验证码">';
        return $output;
    }
}
