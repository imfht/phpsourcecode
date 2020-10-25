<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');

class tag_categoryAdmincp extends categoryAdmincp {
    public function __construct() {
        parent::__construct(iCMS_APP_TAG,'category');
        $this->category_name     = "分类";
        $this->_app              = 'tag';
        $this->_app_name         = '标签';
        $this->_app_table        = 'tag';
        $this->_app_cid          = 'tcid';
       /**
         *  模板
         */
        $this->category_template+=array(
            'tag'     => array('标签','{iTPL}/tag.htm'),
        );

        /**
         *  URL规则
         */
        $this->category_rule+= array(
            'tag'     => array('标签','/tag/{TKEY}{EXT}','{ID},{0xID},{TKEY},{NAME},{ZH_CN},{Hash@ID},{Hash@0xID}')
        );
        /**
         *  URL规则选项
         */
        $this->category_rule_list+= array();
    }
    public function do_add($default=null){
        parent::do_add(array('status'=> '2'));
    }
}
