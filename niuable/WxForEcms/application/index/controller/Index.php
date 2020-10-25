<?php
namespace app\index\controller;

use app\index\model\WxWx;
use think\Controller;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	
	// 输入的数据 array
	protected $in;
	public function __construct() {
		global $ecms_hashur;
		parent::__construct ();
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		$this->data = [ 
				'title' => '公众号管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ) 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
	}
	/**
	 * Index
	 *
	 * @return string 封面页或错误页
	 */
	public function index() {
		$data = $this->data;
		$in = $this->in;
		$my_query [] = $data ['ecms_hashur'] ['href'];
		if (isset ( $in ['search'] )) {
			$where ['name'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = '';
		}
		// 获取数据
		$WxWx = new WxWx ();
		$list = $WxWx->where ( $where )->paginate ( '', false, [ 
				'query' => $my_query,
				'path' => url ( "/", '', false ) 
		] );
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		return $this->view ( './vIndex', $data );
	}
	/**
	 * 新增公众号
	 *
	 * @return string
	 */
	public function add() {
		$data = $this->data;
		$r = $this->in;
		// 验证数据合法性
		$result = $this->validate ( $r, 'Index' );
		if (true !== $result) { // 当验证不通过时
			$data ['form'] = $r;
			$data ['form_error'] = $result;
			return $this->view ( './vIndex', $data );
		}
		$WxWx = new WxWx ( $r );
		if ($r ['active'] == 1) {
			$WxWx->update ( [ 
					'id' => true,
					'active' => 0 
			] );
		} else {
			$res = $WxWx->where ( 'active', 1 )->find ();
			if (empty ( $res )) {
				$WxWx->active = 1;
			}
		}
		$res = $WxWx->allowField ( true )->save ();
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( '新增成功', url ( '/Index/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( '新增失败' );
		}
	}
	/**
	 * editor
	 * @method 编辑
	 * @todo 编辑公众号数据
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		$msg = '';
		if ('update' == $in ['editorType']) {
			$word = '更新';
			$res = $this->updateDate ( $in, $data );
		} elseif ('oneDelete' == $in ['editorType']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] - 1 );
			if (false == $res)
				$msg = ',注意是否正在删除默认公众号';
		} elseif ('sDelete' == $in ['editorType']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res [0] == 1) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/Index/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			} elseif ($res [0] == 0) {
				// 错误页面的默认跳转页面是返回前一页，通常不需要设置
				$this->error ( $word . '操作失败' );
			} else {
				$errid = $res [1];
				$strings = '';
				foreach ( $errid as $k => $v ) {
					$strings .= $v . '，';
				}
				$strings = rtrim ( $strings, '，' );
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/Index/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			}
		}
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/Index/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			$this->error ( $word . '操作失败' . $msg );
		}
	}
	/**
	 * getTheData
	 * @method 获取数据
	 * @param number $s 位置
	 * @return mixed[] 数据
	 */
	private function getTheData($s = 0) {
		$r = [ ];
		$in = $this->in;
		unset ( $in ['ids'] );
		foreach ( $in as $k => $v ) {
			if (is_array ( $v )) {
				$r [$k] = $v [$s];
			}
		}
		
		return $r;
	}
	/**
	 * updateDate
	 * @method 更新
	 * @param array $in 传入的数据
	 * @param array $data 参数
	 * @return boolean 上传是否成功
	 */
	private function updateDate($in, $data) {
		$res = $this->getTheData ( $in ['site'] - 1 );
		$result = $this->validate ( $res, 'Index' );
		if (true !== $result) { // 当验证不通过时
			return false;
		} else {
			$WxWx = new WxWx ();
			if ($res ['active'] == 1) {
				$res2 = $WxWx->where ( 'active', 1 )->update ( [ 
						'active' => 0 
				] );
			}
			$res = $WxWx->allowField ( true )->update ( $res );
			return true;
		}
	}
	/**
	 * oneDelete
	 * @method 单个删除
	 * @param array $in 数据
	 * @param number $s 位置
	 * @return boolean|number 是否成功/影响数量
	 */
	private function oneDelete($in, $s) {
		$r = $this->getTheData ( $s );
		$WxWx = new WxWx ();
		$res = $WxWx->get ( $r ['id'] );
		if ($res ['active']) {
			$count = $WxWx->count ( 'id' );
			if ($count != 1)
				return false;
		}
		return $result = $WxWx->get ( $r ['id'] )->delete ();
	}
	/**
	 * sDelete
	 * @method 批量删除
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return mixed[] 结果
	 */
	private function sDelete($in, $data) {
		$error = $success = 0;
		$WxWx = new WxWx ();
		foreach ( $in ['ids'] as $key => $val ) {
			$r = $this->getTheData ( $val - 1 );
			$res = $WxWx->get ( $r ['id'] )->delete ();
			if ($res) {
				$success ++;
			} else {
				$error ++;
				$err_id [] = $in ['id'] [$val - 1];
			}
		}
		// 判断是否存在默认公众号，如没有，设置一个
		$res = $WxWx->where ( 'active', 1 )->find ();
		if (empty ( $res )) {
			$result = $WxWx->limit ( 1 )->order ( 'id', 'desc' )->find ();
			$result = $WxWx->where ( 'id', $result ['id'] )->update ( [ 
					'active' => 1 
			] );
		}
		if ($error < 1) {
			return [ 
					1 
			];
		} elseif ($success < 1) {
			return [ 
					0 
			];
		} else {
			return [ 
					- 1,
					$err_id 
			];
		}
	}
	/**
	 * view
	 * @method 显示
	 * @param string $temp 模板地址
	 * @param array $data 参数
	 * @return string 待显示的HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
