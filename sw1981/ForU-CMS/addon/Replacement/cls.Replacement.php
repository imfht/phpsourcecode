<?php
// 调用代码
// hook('replace_action', array('str'=>$channel['c_content']));
include_once LIB_PATH . 'cls.addon.php';

class Replacement extends Addon{
  public function replace_action($params) {
    foreach($this->getConfig('replace_action') as $key=>$value) {
      $pattern = '/'.$value['key'].'/i';
      $params['str'] = preg_replace($pattern, $value['val'], $params['str']);
    }
    echo $params['str'];
  }
}