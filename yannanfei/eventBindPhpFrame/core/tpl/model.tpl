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
class {{model}}Model extends Model{
//列举出所有字段和基础字段
 protected   $field = '{{field}}';
 protected   $privateKey='{{key}}';
 protected   $table='{{model}}';

//example
public function  search_{{model}}(){
  return  Model3('{{model}}')->select();
}



}