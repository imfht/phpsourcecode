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

class article_categoryAdmincp extends categoryAdmincp {
    public function __construct() {
        parent::__construct(iCMS_APP_ARTICLE,'category');
        $this->category_name            = "栏目";
        $this->_app                     = 'article';
        $this->_app_name                = '文章';
        $this->_app_table               = 'article';
        $this->_app_cid                 = 'cid';
        /**
         *  模板
         */
        $this->category_template+=array(
            'article' => array('文章','{iTPL}/article.htm'),
            'tag'     => array('标签','{iTPL}/tag.htm'),
        );

        /**
         *  URL规则
         */
        $this->category_rule+= array(
            'article' => array('文章','/{CDIR}/{YYYY}/{MM}{DD}/{ID}{EXT}','{ID},{0xID},{LINK},{Hash@ID},{Hash@0xID}'),
            'tag'     => array('标签','/{CDIR}/t-{TKEY}{EXT}','{ID},{0xID},{TKEY},{NAME},{ZH_CN},{Hash@ID},{Hash@0xID}')
        );
        /**
         *  URL规则选项
         */
        $this->category_rule_list+= array();
    }
}
