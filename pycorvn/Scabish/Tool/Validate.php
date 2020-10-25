<?php
namespace Scabish\Tool;

/**
 * Scabish\Tool\Validate
 *
 * 数据验证器
 * 用于对来自非安全来源的数据（比如用户输入）进行验证
 * @example
     $rules = [
        'fdEmail' => ['require', 'email'], // 必须，邮箱验证规则
        'fdGender' => ['in' => [1, 2]], // 限定只能在1,2之间取值
        'fdAge' => ['int', 'min' => 10, 'max' => 120], // 使用整形验证规则，最小值10，最大值120
        'fdAddress' => ['minLength' => 10, 'maxLength' => 20], // 最小长度10，最大长度20
        'fdIdCard' => ['length' => 18],  // 18位
        'fdHomepage' => ['url'], // 使用url验证规则
        'fdIp' => ['ip'], // 使用ip验证规则
    ];
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2016-12-15
 */
class Validate {
    
    private $_rules;
    private $_data = [];
    
    public function __construct(array $rules) {
        $this->_rules = $rules;
    }
    
    public function Check(array $data) {
        $this->_data = $data;
        foreach($this->_rules as $param=>$setting) {
            if(!is_array($setting)) continue;
            foreach($setting as $rule=>$extend) {
                if(is_numeric($rule)) {
                    $rule = $extend;
                    $extend = null;
                }
                $method = 'Check'.Ucfirst($rule);
                method_exists(__CLASS__, $method) && $this->$method($param, $extend);
            }
        }
    }
    
    protected function GetParam($param) {
        return isset($this->_data[$param]) ? $this->_data[$param] : false;
    }
    
    protected function CheckRequire($param) {
        if(!Kit::Valid($param, $this->_data)) throw new \Exception('Param '.$param.' is required');
    }
    
    protected function CheckInt($param) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(false === filter_var($value, FILTER_VALIDATE_INT)) {
                throw new \Exception('Param of '.$param.' should be an integer');
            }
        }
    }
    
    protected function CheckArray($param) {
        $value = $this->GetParam($param);
        if(!is_array($value)) throw new \Exception('Param of '.$param.' should be an array');
    }
    
    protected function CheckIp($param) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(false === filter_var($value, FILTER_VALIDATE_IP)) {
                throw new \Exception('Param of '.$param.' is not a valid ip address');
            }
        }
    }
    
    protected function CheckUrl($param) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(false === filter_var($value, FILTER_VALIDATE_URL)) {
                throw new \Exception('Param of '.$param.' is not a valid url');
            }
        }
    }
    
    protected function CheckEmail($param) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(false === filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Param of '.$param.' is not a valid email address');
            }
        }
    }
    
    protected function CheckString($param) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(!is_string($value)) throw new \Exception('Param of '.$param.' should be a string');
        }
    }
    
    protected function CheckLength($param, $extend) {
        $value = $this->GetParam($param);
        if(is_array($value) && is_numeric($extend)) {
            if(count($value) != $extend) throw new \Exception('Param of '.$param.'\'s length should be equal to '.$extend);
        } elseif(is_string($value) && strlen($value)) {
            if(is_numeric($extend)) {
                if(strlen($value) != $extend) throw new \Exception('Param of '.$param.'\'s length should be equal to '.$extend);
            } elseif(is_array($extend)) {
                list($min, $max) = $extend;
                if(!($value > $min && $value < $extend)) {
                    throw new \Exception('Param of '.$param.'\'s length should be greater than '.$min.' and less than '.$max);
                }
            }
        }
    }
    
    protected function CheckRegexp($param, $extend) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if(false == filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $extend]])) {
                throw new \Exception('Param of '.$param.'\'s format is invalid');
            }
        }
    }
    
    protected function CheckMin($param, $extend) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if($value < $extend) throw new \Exception('Param of '.$param.' should be greater or equal than '.$extend);
        }
    }
    
    protected function CheckMax($param, $extend) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value)) {
            if($value > $extend) throw new \Exception('Param of '.$param.' should be less or equal than '.$extend);
        }
    }
    
    protected function CheckIn($param, $extend) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value) && is_array($extend)) {
            if(!in_array($value, $extend)) throw new \Exception('Param of '.$param.' is invalid');
        }
    }
    
    protected function CheckExcept($param, $extend) {
        $value = $this->GetParam($param);
        if(!is_array($value) && !is_object($value) && strlen($value) && is_array($extend)) {
            if(in_array($value, $extend)) throw new \Exception('Param of '.$param.' is invalid');
        }
    }
    
}