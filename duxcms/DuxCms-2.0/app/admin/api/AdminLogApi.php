<?php
namespace app\admin\api;
/**
 * 后台记录接口
 */
class AdminLogApi{
	/**
	 * 增加操作记录
	 * @param string $log 记录内容
	 */
	public function addLog($log){
        return target('admin/AdminLog')->addData($log);
	}

}
