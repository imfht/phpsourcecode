<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月15日晚 2:49 
* @基础应用model
*/
namespace Common\Model;
use Think\Model;
use Org\Util\Dir;
class AppModel extends Model {
	public static $defaultApp = array (); // 默认应用字段
	public $_host_type = array (); // 应用类型字段
	/**
	 * 初始化 - 用于双语处理
	 *
	 * @return void
	 */
	public function _initialize() {
		$this->_host_type = array (
				0 => "本地应用",
				1 => "远程应用"
		); // 本地应用，远程应用
	}
	/**
	 * 获取未安装应用列表
	 * @return array 未安装应用列表
	 */
	public function getUninstallList() {
		$uninstalled = $arrapp = array ();

		// 已经安装的
		$installed = $this->field ( 'app_name' )->order ( 'app_id DESC' )->select();
		// 默认应用，不能安装卸载
		if(!empty($installed)){
			foreach($installed as $key=>$item){
				$arrapp[$key] = $item['app_name'];
			}
			$installed = array_merge ( $arrapp, C ( 'DEFAULT_APPS' ) ); 
		}else{
			//在AdminControll里已经初始化了
			$installed = C ( 'DEFAULT_APPS' ); 
		}
		
		$dirs = new Dir ( APP_PATH );
		$dirs = $dirs->toArray (); //转换成数组
	

		foreach ( $dirs as $v ) { 
			if ($v ['isDir'] && ! in_array ( $v['filename'], $installed )) { 
				if ($info = $this->__getAppInfo ( $v['filename'] )) { 
					$uninstalled [] = $info;
				}
			}
			
		}
		return $uninstalled;
	}
	/**
	 * 获取应用信息
	 *
	 * @param string $path_name   	应用路径名称
	 * @param boolean $using_lowercase    返回键值为大写还是小写，默认为小写
	 * @return array 指定应用的相关信息
	 */
	public function __getAppInfo($path_name, $using_lowercase = true) { 
		$filename = APP_PATH . $path_name . '/Appinfo/about.php'; 

		if (is_file ( $filename )) {	
			$info = include_once $filename; 
			$info ['HOST_TYPE_ALIAS'] = $this->_host_type [$info ['HOST_TYPE']];
			$info ['APP_ALIAS'] = $info ['NAME']; //应用别名
			$info ['PATH_NAME'] = $path_name;
			$info ['APP_NAME'] = $path_name; 
			return $using_lowercase ? array_change_key_case ( $info ) : array_change_key_case ( $info, CASE_UPPER );
		} else {
			return false;
		}
	}
	/**
	 * 保存应用信息数据
	 * 
	 * @param array $data
	 *        	应用相关数据
	 * @return boolean 是否保存成功
	 */
	public function saveApp($data) {
		foreach ( $data as $k => &$v ) {
			$v = ($k == 'description') ? htmlspecialchars ( $v ) : clearText ( $v );
		}

		if ($data ['host_type'] == 0 && ! is_dir ( APP_PATH . $data ['app_name'] )) {
			return $data ['app_name'].'目录不存在！';
		}
		
		if (! empty ( $data ['app_id'] )) {
			// 更新应用数据操作
			$map = array ();
			$map ['app_id'] = $data ['app_id'];
			unset ( $data ['app_id'] );
			if ($this->where ( $map )->save ( $data )) {
				return true;
			} else {
				return '数据更新失败，可能未做任何修改'; // 数据更新失败，可能未做任何修改
			}
		} else {
					
			// 新增加应用操作
			if ($this->isAppNameExist ( $data ['app_name'] )) {
				return $data ['app_name'].'应用已经存在'; // 应用已经存在
			}
			//获取应用信息
			$oldInfo = $this->__getAppInfo ( $data ['app_name'] );
			// 入库数据内容处理
			empty ( $oldInfo ['child_menu'] ) && $oldInfo ['child_menu'] = array ();
			$data ['child_menu'] = serialize ( $oldInfo ['child_menu'] );
			
			$install_script = APP_PATH  . $data ['app_name'] . '/Appinfo/install.php';
			if (file_exists ( $install_script )) {
				include_once $install_script;
			}
			//安装时间
			$data ['setuptime'] = time ();
			// 为便于排序，将order设置为ID
			unset ( $data ['app_id'] );
			
			if ($insertID = $this->add ( $data )) {
				// 成功入库之后执行的操作 还有其他继续执行
				$this->where (array('app_ic'=>$insertID))->setField ( 'display_order', $insertID );
				return true;
			} else {
				return '保存数据失败！'; // 数据插入失败
			}			
		}
		
	}	
	/**
	 * 判断指定应用是否已经安装
	 * 
	 * @param string $app_name
	 *        	应用名称
	 * @param integer $app_id
	 *        	应用ID
	 * @return boolean 指定应用是否安装
	 */
	public function isAppNameExist($app_name = '', $app_id = '') {
		// 参数判断
		if (empty ( $app_name ) && empty ( $app_id )) {
			$this->error = '错误的参数'; // 错误的参数
			return false;
		}
		// 默认应用
		if (in_array ( $app_name, C ( 'DEFAULT_APPS' ) )) {
			return true;
		}
		// 用户自定义安装应用
		$list = $this->getAppList ();
		foreach ( $list as $v ) {
			if (! empty ( $app_name ) && ($v ['app_name'] == $app_name)) {
				return true;
			}
			if (! empty ( $app_id ) && ($v ['app_id'] == $app_id)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 获取所有应用列表 - 不分页型
	 * 
	 * @param array $map
	 *        	查询条件
	 * @param string $limit
	 *        	显示结果集数目，默认不设置
	 * @return array 应用列表数据
	 */
	public function getAppList($map = array(), $limit = '') {
		$list = $this->where ( $map )->order ( 'app_id DESC' )->limit($limit)->select();
		return $list;
	}
	/**
	 * 获取单个应用
	 * 
	 * @param array $map
	 * @return array 应用列表数据
	 */
	public function getOneApp($map = array()){
		$res = $this->where($map)->find();
		return $res;
	}
	/**
	 * 后台卸载指定应用
	 *
	 * @param integer $app_id
	 *        	应用ID
	 * @return boolean 是否卸载成功
	 */
	public function uninstallApp($app_id) {
		$map = array ();
		$map ['app_id'] = $app_id;
		$appinfo = $this->where ( $map )->find();
		if (empty ( $appinfo )) {
			return '应用不存在或未安装'; // 应用不存在或未安装
		}
		if ($this->where ( $map )->delete ()) {
			$uninstall_script = APP_PATH . '/' . $appinfo ['app_name'] . '/Appinfo/uninstall.php';
			if (is_file ( $uninstall_script )) {
				include_once $uninstall_script;
			}
			//删除用户应用表中的数据 这版不开发 下版本开发
			//$umap ['app_id'] = $app_id;
			//D( 'UserApp' )->where ( $umap )->delete ();				
			return true;
		} else {
			return '操作失败'; // 操作失败
		}
	}
}