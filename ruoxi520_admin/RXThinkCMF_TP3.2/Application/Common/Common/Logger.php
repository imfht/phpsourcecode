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
 * 系统日志-常用类
 *
 * @author 牧羊人
 * @date 2018-07-18
 */
use Admin\Model\SystemLogModel;
class Logger {
    
    //类型：1登录 2登出 3新增 4修改 5删除 6查询
    const LOGGER_LOGIN = 1;//登录
    const LOGGER_LOGIN_OUT = 2;//登出
    const LOGGER_ADD = 3;
    const LOGGER_UPDATE = 4;
    const LOGGER_DELETE = 5;
    const LOGGER_QUERY = 6;
    
    
    /**
     * 实例化存储对象
     * 
     * @author 牧羊人
     * @date 2018-07-20
     * @return \Common\Model\SystemLogModel
     */
    static function instance() {
        return new SystemLogModel();
    }
    
    /**
     * 写入日志
     * 
     * @author 牧羊人
     * @date 2018-07-26
     */
    static function write($title,$type,$data,$obj,$isSucc=true) {
        if($isSucc) {
            //写入成功日志
            self::writeSucc($title, $type, $data, $obj);
        }else {
            //写入失败日志
            self::writeFail($title, $type, $data, $obj);
        }
    }
    
    /**
     * 写入成功日志
     * 
     * @author 牧羊人
     * @date 2018-07-18
     */
    static function writeSucc($title,$type,$data,$obj) {
        //操作数据ID
        $id = $data['id'];
       
        //执行SQL语句
        $sql = $obj->_sql();
        
        //查询对象
        $info = $obj->find($id);
        
        //请URL地址
        $url = $_SERVER["REQUEST_URI"];
        if(empty($title)) {
            
            $funcInfo = M("menu")->where([
                'type'  =>4,
                'url'   =>__ACTION__,
                'mark'  =>1,
            ])->find();
            if($funcInfo) {
                $menuInfo = M("menu")->find($funcInfo['parent_id']);
                $title = $menuInfo['name'] . "【{$funcInfo['name']}】";
            }
        }
        $item = [
            'title'=>$title,
            'type'=>$type,
            'method'=>REQUEST_METHOD,
            'url'=>$url,
            'param'=>serialize($data),
            'result'=>serialize($info),
            'description'=>'',
            'ip'=>get_client_ip(),
            'sql'=>$sql,
            'start_time'=>$GLOBALS['_beginTime'],
            'spend_time'=>get_runtime(),
            'add_user'=>$_SESSION['adminId'] ? $_SESSION['adminId'] : 0,
            'add_time'=>time(),
        ];
        return self::instance()->add($item);
    }
    
    /**
     * 写入失败日志
     * 
     * @author 牧羊人
     * @date 2018-07-26
     */
    static function writeFail($title,$type,$data,$obj) {
        
    } 
    
    /**
     * 写入数据库
     * 
     * @author 牧羊人
     * @date 2018-07-26
     * @param unknown $data
     */
    static function writeSave($data) {
        
    }
    
}