<?php
require_once ('Zend/Controller/Action.php');
/**
 * 
 * @author zhanghuihua
 *
 */
class Dwz_Controller_Action extends Zend_Controller_Action {
	
	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array()) {
		parent::__construct ( $request, $response, $invokeArgs );
	}
	
	public function init() {
		parent::init ();
		$name = $this->_request->getControllerName ();
		$this->view->URL = $name;
		$this->view->MODULE = $name;
	}
	
	protected function _dbMap($dbCols = array()) {
		$dbMap = array ();
		if (is_array ( $dbCols )) {
			foreach ( $_REQUEST as $key => $val ) {
				if (in_array ( $key, $dbCols, true )) {
					$dbMap [$key] = $val;
				}
			}
		}
		return $dbMap;
	}
	
	/**
	 * 数据列表展示页面
	 */
	function indexAction() {
		$model = $this->M ();
		$dbMap = $this->_dbMap ( $model->info ( 'cols' ) );
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $dbMap );
		}

		$termCount = 0;
		foreach ( $dbMap as $key => $val ) {
			
			if (isset ( $val ) && trim ( $val ) != '') {
				
				if (is_array ( $val ) && trim ( $val [1] ) != '') {
					$where .= ($termCount > 0 ? ' and ' : ' ') . $key . ' ' . $val [0] . ' \'' . trim ( $val [1] ) . '\'';
					$termCount ++;
				} else if (trim ( $val ) != '') {
					$where .= ($termCount > 0 ? ' and ' : ' ') . $key . '=\'' . trim ( $val ) . '\'';
					$termCount ++;
				}
			
			}
		}
		
		if (! empty ( $_REQUEST ['orderField'] )) {
			$order = $_REQUEST ['orderField'];
			if (empty ( $_REQUEST ['orderDirection'] )) {
				$order .= ' asc';
			} else {
				$order .= ' ' . $_REQUEST ['orderDirection'];
			}
		}
		
		$numPerPage = 20;
		$offset = 0;
		$pageNum = $_REQUEST ['pageNum'];
		if (! empty ( $pageNum ) && $pageNum > 0) {
			$offset = ($pageNum - 1) * $numPerPage;
		}
		
		$totalCount = $model->getAdapter ()->fetchOne ( 'select count(*) as count from ' . $model->info ( 'name' ) . (empty ( $where ) ? '' : ' where ' . $where) );
		
		$this->view->list = $model->fetchAll ( $where, $order, $numPerPage, $offset );
		$this->view->totalCount = $totalCount;
		$this->view->numPerPage = $numPerPage;
		$this->view->currentPage = $pageNum > 0 ? $pageNum : 1;
	
	}
	
	/**
	 * 数据展示页面
	 */
	function readAction() {
		$this->editAction ();
	}
	
	/**
	 * 数据创建页面
	 */
	function addAction() {
	
	}
	
	/**
	 * 数据编辑页面
	 */
	function editAction() {
		$model = $this->M ();
		$this->view->vo = $model->fetchRow ( 'id=' . $_REQUEST ['id'] );
	}
	
	/**
	 * 创建数据操作
	 */
	function insertAction() {
		try {
			$model = $this->M ();
			$dbMap = $this->_dbMap ( $model->info ( 'cols' ) );
			$id = $model->insert ( $dbMap );
			$this->success ( '操作成功' );
		} catch ( Exception $e ) {
			$this->error ( '操作失败' );
		}
	}
	
	/**
	 * 更新数据操作
	 */
	function updateAction() {
		try {
			$model = $this->M ();
			$dbMap = $this->_dbMap ( $model->info ( 'cols' ) );
			$db = $model->getAdapter ();
			
			$where = $db->quoteInto ( 'id=?', $_REQUEST ['id'] );
			$row_affected = $model->update ( $dbMap, $where );
			
			$this->success ( '操作成功' );
		} catch ( Exception $e ) {
			$this->error ( '操作失败' );
		}
	}
	
	/**
	 * 删除数据操作，设置删除标志
	 */
	function deleteAction() {
		try {
			$model = $this->M ();
			$db = $model->getAdapter ();
			
			$where = $db->quoteInto ( 'id=?', $_REQUEST ['id'] );
			$row_affected = $model->update ( array ('is_delete' => 1 ), $where );
			
			$this->success ( "操作成功" );
		} catch ( Exception $e ) {
			$this->error ( '操作失败' );
		}
	}
	
	/**
	 * 强制删除数据操作
	 */
	function foreverdeleteAction() {
		try {
			$model = $this->M ();
			$db = $model->getAdapter ();
			$where = $db->quoteInto ( 'id=?', $_REQUEST ['id'] );
			$row_affected = $model->delete ( $where );
			$this->success ( "操作成功" );
		} catch ( Exception $e ) {
			$this->error ( '操作失败' );
		}
	}
	
	private function M() {
		$className = ucfirst ( $this->_request->getControllerName () ) . 'Model';
		return new $className ();
	}
	
	/**
     +----------------------------------------------------------
	 * 是否AJAX请求
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @return bool
     +----------------------------------------------------------
	 */
	protected function isAjax() {
		if (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] )) {
			if ('xmlhttprequest' == strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ))
				return true;
		}
		if (! empty ( $_REQUEST ['ajax'] ))
			// 判断Ajax方式提交
			return true;
		return false;
	}
	
	/**
     +----------------------------------------------------------
	 * Ajax方式返回数据到客户端
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param boolean $status 返回状态
	 * @param String $message 提示信息
	 * @param String $status ajax返回类型 JSON
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	protected function ajaxReturn($status = 1, $message = '') {
		$result = array ();
		$result ['statusCode'] = $status;
		$result ['navTabId'] = $_REQUEST ['navTabId'];
		$result ['rel'] = $_REQUEST ['rel'];
		$result ['callbackType'] = $_REQUEST ['callbackType'];
		$result ['forwardUrl'] = $_REQUEST ['forwardUrl'];
		$result ['message'] = $message;
		
		header ( 'Content-Type:text/html; charset=utf-8' );
		exit ( Zend_Json::encode ( $result ) );
	}
	
	/**
     +----------------------------------------------------------
	 * 操作成功跳转的快捷方法
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $message 提示信息
	 * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	protected function success($message, $ajax = false) {
		//		$this->_dispatch_jump (1, $message, $ajax);
		$this->ajaxReturn ( 1, $message );
	}
	
	/**
     +----------------------------------------------------------
	 * 操作错误跳转的快捷方法
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $message 错误信息
	 * @param Boolean $ajax 是否为Ajax方式
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 */
	protected function error($message, $ajax = false) {
		//		$this->_dispatch_jump (0, $message, $ajax);
		$this->ajaxReturn ( 0, $message );
	}
	/*
	private function _dispatch_jump($status = 1, $message='', $ajax = false) {
		// 判断是否为AJAX返回
		if ($ajax || $this->isAjax ())
			$this->ajaxReturn ($status, $message);

		if ($status) { //发送成功信息
			
		} else {
			//发生错误
		}

		exit();
	}
	*/
}
