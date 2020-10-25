<?php

/*
 *  @author myf
 *  @date 2014-11-13 13:53:11
 *  @Description demo
 */


class TestController extends Controller{
    
    public function hello(){
       $now = date("Y-m-d H:i:s");
       echo sprintf("<h2>你好：%s,现在是%s</h2>","MyfMVC",$now);
    }
}