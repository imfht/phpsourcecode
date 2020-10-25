<?php
// +----------------------------------------------------------------------
// | eventBindPhpFrame [ keep simple try auto ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015~2016 eventBindPhpFrame All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yannanfei <yannanfeiff@126.com>
// +----------------------------------------------------------------------
class indexControl extends Control
{
    public  function  index(){
echo 'thanks for using event bind php frame ^_^ !';
    }
    public  function  show_demo(){
        echo plugin('T')->include_file('index/show_demo',array('time'=>date('Y-m-d H:i:s')));
    }
}