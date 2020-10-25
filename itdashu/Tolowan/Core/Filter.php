<?php
namespace Core;

use Phalcon\Filter as PhalconFilter;
use Core\Config;

class Filter extends PhalconFilter{

    public function __construct()
    {
        $filters = Config::cache('filters');
        foreach ($filters as $filterName => $filter){
            $this->add($filterName,$filter);
        }
    }

    protected function _sanitize($value, $filter)
    {
        if(is_string($value)){
            if(strstr($filter,'(')){
                $funcInfo = explode('(',trim($filter,') '));
                $funcParams = explode(',',$funcInfo[1]);
                $funcName = $funcInfo[0];
            }else{
                $funcParams = [];
                $funcName = $filter;
            }
        }
        if(function_exists($funcName)){
            array_unshift($funcParams,$value);
            return call_user_func_array($funcName,$funcParams);
        }
        return parent::_sanitize($value, $filter);
    }
}