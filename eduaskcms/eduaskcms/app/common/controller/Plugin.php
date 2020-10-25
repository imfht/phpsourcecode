<?php
namespace app\common\controller;

abstract class Plugin extends App
{
    /**
     * $info = array(
     *  'name'=>'Editor',
     *  'title'=>'编辑器',
     *  'description'=>'用于增强整站长文本的输入和显示',
     *  'status'=>1,
     *  'author'=>'thinkphp',
     *  'version'=>'0.1'
     *  )
     */
    public $info = [];   
    
    
    protected function initialize()
    {
        call_user_func(array('parent', __FUNCTION__));
        if (isset($GLOBALS['plugin_params'])) {
            $this->params = $GLOBALS['plugin_params'];
        } else {
            $this->params = []; 
        }
        $this->params['param'] = $this->passedArgs;
    }
    
    protected function loadPluginModel($model)
    {
        $model = parse_name($model, 1);
        if (isset($this->$model) && is_object($this->$model)) {
            return $this->$model;
        }
        
        if (isset($GLOBALS['plugin_params']['module'])) {
            $class_name = 'plugin\\' . $GLOBALS['plugin_params']['plugin'] .  '\\' . $GLOBALS['plugin_params']['module'] . '\\model\\' . $model; 
            if (!class_exists($class_name)) {
                $class_name = 'plugin\\' . $GLOBALS['plugin_params']['plugin'] .  '\\common\\model\\' . $model; 
            }            
        } else {
            $class_name = 'plugin\\' . $GLOBALS['plugin_params']['plugin'] . '\\model\\' . $model; 
        }
        
        if (class_exists($class_name)) {
            return $this->$model = new $class_name();
        } else {
            return $this->$model = db($model);
        }
    }
    
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        echo call_user_func_array(['parent', __FUNCTION__], func_get_args());
    }
}
