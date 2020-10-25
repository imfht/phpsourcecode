<?php

namespace Admin\Controller;

use Think\Controller;

class SiteoptionController extends CommonController {

    public function index() {
        $this->title='站点信息';
       if(IS_POST){
           $option = $_POST['option'];
           foreach ($option as $key => $value) {
               $data =array();
               $data['id']=$key+1;
               $data['value']=$value;
               M('option')->save($data);
            }
            S('option',null);
            echo "<span class='text-success'>保存成功</span>";
       }
       $option=M('option')->getField('value',true);
       $this->option = $option;
       $this->display();
    }
    

  

}
