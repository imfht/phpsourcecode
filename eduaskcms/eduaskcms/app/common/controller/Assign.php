<?php
namespace app\common\controller;

class Assign
{
    public $VarsReturned = false;
    public $js = array();
    public $css = array();
    public $defer_js = array();

    public function addJs($path, $defer = false)
    {
        if ($defer) {
            if (!is_array($path)) {
                array_push($this->defer_js, $path);
            } else {
                $this->defer_js = array_merge($this->defer_js, $path);
            }
            $this->defer_js = array_unique($this->defer_js);            
        } else {
            if (!is_array($path)) {
                array_push($this->js, $path);                
            } else {
                $this->js = array_merge($this->js, $path);
            }
            $this->js = array_unique($this->js);
        }
        
    }

    public function removeJs($path, $defer = false)
    {
        if ($defer) {
            if (!is_array($path)) {
                $this->defer_js = array_diff($this->defer_js, [$path]);
            } else {
                $this->defer_js = array_diff($this->defer_js, $path);
            }
            
        } else {
            $this->js = array_diff($this->js, [$path]);
        }
    }

    public function addCss($path)
    {
        if (!is_array($path)) {
            array_push($this->css, $path);
        } else {
            $this->css = array_merge($this->css, $path);
        }
        $this->css = array_unique($this->css);    
    }

    public function removeCss($path)
    {
        if (!is_array($path)) {
            $this->css = array_diff($this->css, [$path]);
        } else {
            $this->css = array_diff($this->css, $path);
        }
    }

}
