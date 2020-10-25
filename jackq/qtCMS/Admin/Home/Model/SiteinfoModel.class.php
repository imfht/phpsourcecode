<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class SiteinfoModel extends CommonModel{

    protected $_validate = array(

        array('seo_keywords', 'require', 'SEO关键字不能为空！', 1, 'regex', 3),

        array('seo_description', 'require', 'SEO网站描述不能为空！', 1, 'regex', 3),

       // array('icp', 'require', '备案号不能为空！', 1, 'regex', 3),
    );

} 