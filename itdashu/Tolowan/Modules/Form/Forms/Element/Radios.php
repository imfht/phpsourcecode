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
        $value = $this->getValue();
        $i = 0;
        $class = '';
        foreach ($this->options as $oKey => $oValue) {
            if ($i === 0) {
                $class = ' first';
            } else {
                $class = '';
            }
            $i++;
            $output .= '<div class="inline radios' . $class . '">';

            if ($oKey == $value) {
                $output .= '<input type="radio" name="' . $name . '"' . self::attributes($this->getAttributes()) . ' value="' . $oKey . '" checked />';
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
