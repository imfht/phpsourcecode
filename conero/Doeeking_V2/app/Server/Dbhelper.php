<?php
/* 2017年2月21日 星期二
 *  新增数数据库连接扩展/增强
 */
namespace app\Server;
use hyang\Logic;
use think\Db;
class Dbhelper extends Logic
{
    /**
     *  @name sql查询分页
     *  @param $sql  
     *  @param $bind 数据绑定
     *  @param $pageIpt = [$page,$num/30] = $page - 30
     */
    public function sqlPage($sql,$bind=[],$pageIpt=[])
    {
        // 预处理
        $num = isset($pageIpt['num'])? intval($pageIpt['num']):30;
        if(is_array($pageIpt) && isset($pageIpt['page'])) $page = intval($pageIpt['page']);
        elseif($pageIpt && is_numeric($pageIpt)) $page = $pageIpt;
        else $page = 1;
        $start = ($page-1)*$num;    // 其实页面
        $sql .= ' limit '.$start.','.$num;
        return Db::query($sql,$bind);
    }
}