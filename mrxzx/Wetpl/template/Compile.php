<?php

/**
 * @author mr小卓X<mrxzx@wwsg18.top>
 * @copyright ©2018 wwsg18.top
 * @link http://Wetpl.wwsg18.top
 * @version 1.0.0
 */

require_once("Parser.php");

function compile($path,$tpl){

    $fname = explode('.',$path);
    array_pop($fname);
    $fname = implode('.',$fname);
    @$dir = dirname('compile/' . $fname . '.php');
    @mkdir($dir,0777,true);
    file_put_contents('compile/' . $fname . '.php',$tpl);
}