<?php

/**
 * 如果要在NetBeans里面实现，$this->代码提示，就把该文件放到项目目录任意文件夹即可。
 */
class MpLoaderPlus extends MpLoader {
    
}

class CI_DB_active_record {

    /**
     * @return CI_DB_result 
     */
    public function get() {
        
    }

    /**
     * @return CI_DB_result 
     */
    public function get_where() {
        
    }

}

/**
 * @property CI_DB_active_record $db
 * @property phpFastCache        $cache
 * @property MpInput          $input
 * @property MpModelTip       $model
 * @property MpRule           $rule
 */
class MpLoader {

    /**
     * @return MpModelTip
     */
    public function model() {
        return null;
    }

}

/**
 * 当新增加了模型，在这里按着下面格式添加上新加的模型<br/>
 * 然后就可以通过$this->model-> 就能自动提示新加的模型
 * @property DemoModel             DemoModel
 */
class MpModelTip {
    
}
