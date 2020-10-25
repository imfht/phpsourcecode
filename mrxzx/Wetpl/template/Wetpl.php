<?php

/**
 * @author mr小卓X<mrxzx@wwsg18.top>
 * @copyright ©2018 wwsg18.top
 * @link http://Wetpl.wwsg18.top
 * @version 1.0.0
 */

include_once('Compile.php');

class Wetpl{
    private $parser,$page,$file;
    public function __construct($file,$var,$left='{%',$right='%}'){
        $this->file = $file;
        $tpl = file_get_contents($file);
        $this->parser = new Parser($tpl,$var,$left,$right);
        $this->page = $this->parser->Parse();
        compile($file,$this->page);
    }
    public function display(){
        $fname = explode('.',$this->file);
        array_pop($fname);
        $fname = implode('.',$fname);
        require_once('compile/' . $fname . '.php');

        $code = ob_get_contents();
        ob_clean();

        return $code;
    }

    public static function render($file,$vars,$lsym='{%',$rsym='%}'){
        $tpl = new self($file,$vars,$lsym,$rsym);
        return $tpl->display();
    }
}