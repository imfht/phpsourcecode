<?php
namespace Core;

use Core\Models\Flash as Mflash;

class Flash
{
    protected $config;
    protected $flashSession;
    public function __construct($config = array())
    {
        $this->config = $config;
        global $di;
        $this->flashSession = $di->getShared('flashSession');
    }
    public function error($string, $file = 'none', $line = 0)
    {
        if(is_array($string)){
            $string = $this->toString($string);
        }
        $this->save($string, 'error', $file, $line);
        $this->flashSession->error($string);
    }
    public function success($string, $file = 'none', $line = 0)
    {
        if(is_array($string)){
            $string = $this->toString($string);
        }
        $this->save($string, 'success', $file, $line);
        $this->flashSession->success($string);
    }
    public function notice($string, $file = 'none', $line = 0)
    {
        if(is_array($string)){
            $string = $this->toString($string);
        }
        $this->save($string, 'notice', $file, $line);
        $this->flashSession->notice($string);
    }
    protected function toString($arr){
        $output = '<ol>';
        foreach($arr as $a){
            $output .= $a;
        }
        $output .= '</ol>';
        return $output;
    }
    public function save($string, $type = 'notice', $file = 'none', $line = 0)
    {
        if (in_array($type, $this->config)) {
            $mflash = new Mflash();
            $mflash->data = $string;
            $mflash->type = $type;
            $mflash->file = $file;
            $mflash->line = $line;
            $mflash->save();
        }
    }
    public function code($code)
    {
        //$output = '<pre><h3>查看源码</h3>' . print_r($code, true) . '</pre>';
        $this->flashSession->notice($output);
    }
}
