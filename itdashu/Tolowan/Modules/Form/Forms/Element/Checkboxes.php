<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element;

class Checkboxes extends Element
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
    public function getValue()
    {
        $value = parent::getValue();
        if (!is_array($value)) {
            $value = array();
        }
        $output = array();
        foreach ($value as $v) {
            $output[$v] = $v;
        }
        return $output;
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
        if (!is_array($default)) {
            $default = array($default => $default);
        }
        foreach ($this->options as $oKey => $oValue) {
            $output .= '<div class="input-group pull-left margin-right-15">';
            if (isset($default[$oKey])) {
                $output .= '<input type="checkbox" name="' . $name . '[]"' . self::attributes($this->getAttributes()) . ' value="' . $oKey . '" checked="checked" />';
            } else {
                $output .= '<input type="checkbox" name="' . $name . '[]"' . self::attributes($this->getAttributes()) . ' value="' . $oKey . '" />';
            }
            $output .= '<label>' . $oValue . '</label></div>';
        }
        $output .= '<div class="clear"></div>';
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
