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
 * 版本管理-模型
 * 
 * @author 牧羊人
 * @date 2018-07-13
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class VersionModel extends CBaseModel {
    function __construct() {
        parent::__construct("version");
    }
    
    //自动验证
    protected $_validate = array(
        array('version_num', 'require', '版本号不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('version_num', '1,10', '版本号长度不合法', self::EXISTS_VALIDATE, 'length',3),
        array('version_type', 'require', '请选择版本类型！', self::EXISTS_VALIDATE, '', 3),
        array('type', 'require', '请选择终端类型！', self::EXISTS_VALIDATE, '', 3),
        array('download', 'require', '应用下载地址不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('intro', 'require', '版本升级说明不能为空！', self::EXISTS_VALIDATE, '', 3),
        array('time_interval', 'require', '请输入提示时间间隔！', self::EXISTS_VALIDATE, '', 3),
    );
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-07-13(non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {

            //版本类型
            if($info['version_type']) {
                $info['version_type_name'] = C("VERSION_TYPE")[$info['version_type']];
            }
            
        }
        return $info;
    }
    
}