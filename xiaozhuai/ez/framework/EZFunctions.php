<?php
/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午12:36
 */

function EZConfig(){
    return EZConfig::getInstance();
}

function EZGlobal(){
    return EZGlobal::getInstance();
}

function EZ(){
    return EZ::getInstance();
}

function EZRouter(){
    return EZRouter::getInstance();
}

function EZView(){
    return EZView::getInstance();
}