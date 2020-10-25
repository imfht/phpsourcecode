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

class {{control}}Control extends Control
{
    public  function  index(){
echo 'you are visit {{control}}';
    }

   public function  show_{{control}}(){

      $T= plugin('T');$T=$T?$T:new TPlugin();
      $main= $T->include_file('{{control}}');
      echo $main;
   }
   public function  {{control}}(){

   }
}