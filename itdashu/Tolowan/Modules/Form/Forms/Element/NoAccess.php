<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Exception;
use Phalcon\Forms\Element;

class Radios extends Element
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
        if (is_array($attributes) === false &&
            is_null($attributes) === false) {
            throw new Exception('Invalid parameter type.');
        }
        $output = '';
        $name = $this->getName();
        $default = $this->getDefault();
        foreach ($this->options as $oKey => $oValue) {
            $output .= '<div class="element-group radios">';

            if (isset($default[$oKey])) {
                $output .= '<input type="radio" name="' . $name . '"' . self::attributes($this->getAttributes()) . ' value="' . $oKey . '" check />';
            } else {
                $output .= '<input type="radio" name="' . $name . '"' . self::attributes($this->getAttributes()) . ' value="' . $oKey . ' " />';
            }
            $output .= '<label>' . $oValue . '</label></div>';
        }
        return $output;
    }
    protected static function attributes($attributes)
    {
        $output = '';
        foreach ($attributes as $aKey => $aValue) {
            if (is_array($aValue)) {
                $output .= ' ' . $aKey . '="' . implode(' ', $aValue) . '" ';
            } else {
                $output .= ' ' . $aKey . '="' . $aValue . '"';
            }
        }
        return $output;
    }
}
