<?php
namespace Admin\Model;
use Think\Model;


class ArticleModel extends Model {

    /**
     * 查找后置操作
     */
    protected function _after_find(&$result,$options) {

    }

    protected function _after_select(&$result,$options){

        foreach($result as &$record){
            $this->_after_find($record,$options);
        }
    }
    protected $_validate = array(
        array('title','require','标题必须！'), //默认情况下用正则进行验证
        array('name','','标题已存在',0,'unique',3),
        array('description','require','内容必须！'), //默认情况下用正则进行验证
    );

    /**
     * 文件模型自动完成
     * @var array
     */
    protected $_auto = array(
        array('create_time', NOW_TIME, 1),
    		array('uid', 1, 1),
    		array('status', 1, 1),
        array('update_time', NOW_TIME, 3),
        );

   
}
