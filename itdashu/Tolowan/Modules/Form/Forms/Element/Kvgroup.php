<?php
namespace Modules\Form\Forms\Element;

use Phalcon\Forms\Element;

class Kvgroup extends Element
{
    protected $key;
    protected $value;
    public function setKey($key)
    {
        $this->key = $key;
    }
    public function getValue()
    {
        $value = parent::getValue();
        $output = array();
        if (!is_array($value)) {
            $value = array();
        }
        foreach ($value as $v) {
            if (isset($v['key']) && isset($v['value']) && !empty($v['key']) && !empty($v['value'])) {
                $output[$v['key']] = $v['value'];
            }
        }
        return $output;
    }
    public function setValue($value)
    {
        $this->value = $value;
    }
    public function render($attributes = null)
    {
        $default = $this->getDefault();
        $output = '<table class="table"><tbody><tr><th>变量名</th><th>变量值</th></tr>';
        $oi = 0;
        if (is_string($default)) {
            $default = (array) json_decode($default);
        }
        if (is_array($default) && !empty($default)) {
            foreach ($default as $key => $value) {
                if (is_array($value)) {
                    if (isset($value['key']) && isset($value['value']) && !empty($value['key']) && !empty($value['value'])) {
                        $key = $value['key'];
                        $value = $value['value'];
                    } else {
                        continue;
                    }
                } elseif (!is_string($value)) {
                    continue;
                }
                if (empty($key) || empty($value)) {
                    continue;
                }
                $output .= '<tr><td><input type="text" name="' . $this->getName() . '[' . $oi . '][key]" value="' . $key . '" class="form-control"></td>';
                $output .= '<td><input type="text" name="' . $this->getName() . '[' . $oi . '][value]" value="' . $value . '" class="form-control"></td></tr>';
                $oi++;
            }
        }
        for ($i = $oi; $i < 3 + $oi; $i++) {
            $output .= '<tr><td><input type="text" name="' . $this->getName() . '[' . $i . '][key]" class="form-control"></td>';
            $output .= '<td><input type="text" name="' . $this->getName() . '[' . $i . '][value]" class="form-control"></td></tr>';
        }
        return $output . '</tbody></table>';
    }
}
