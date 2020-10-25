<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element\Text;

class ValidateCode extends Text
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
    public function render($attributes = null)
    {
        global $di;
        $output = '';
        $url = $di->getShared('url')->get(array(
            'for' => 'validateCode'
        ));
        $name = $this->getName();
        $default = $this->getDefault();
        $output .= '<div><img src="'.$url.'" /><a href="#">开不清，换一个</a></div>';
        $output .= parent::render();
        return $output;
    }
}
