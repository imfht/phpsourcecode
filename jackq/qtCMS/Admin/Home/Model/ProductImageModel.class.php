<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2014/11/17
 * Time: 16:43
 */

namespace Home\Model;


class ProductImageModel extends CommonModel{

    /**
     * 字段自动完成
     */
    protected $_auto = array(
        array('type','setType',1,'callback'),
    );

    function setType($type){
        if(empty($type)){
            return 2;
        }
        return $type;
    }
} 