<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 字典-模型
 * 
 * @author 牧羊人
 * @date 2018-07-20
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class DictionaryModel extends CBaseModel {
    function __construct() {
        parent::__construct('dictionary');
    }
    
    //自动验证
    protected $_validate = array(
        array('title','','字典名称已经存在！',self::EXISTS_VALIDATE,'unique',3),
        array('title', '1,50', '字典名称长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('tag', 'require', '字典标签不能为空！', self::MUST_VALIDATE, '', 3),
        array('tag', '1,50', '字典标签长度不合法', self::MUST_VALIDATE, 'length',3),
        array('type', 'require', '请选择类型！', self::EXISTS_VALIDATE, '', 3),
        array('content', 'require', '字典内容【值】不能为空！', self::MUST_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-20
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            //TODO...
        }
        return $info;
    }
    
}