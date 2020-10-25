<?php
namespace app\menu\controller;

use think\Controller;
use app\menu\model\WxMenu;
use think\response\Json;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	// 公共类的实例
	protected $comm;
	// 输入的数据 array
	protected $in;
	public function __construct() {
		global $ecms_hashur, $lur, $isadmin;
		parent::__construct ();
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号数据;
		$common = new \app\common\controller\Index ();
		$this->comm = $common;
		$wx = $common->getDefaultWx ();
		if (! empty ( $wx ['errCode'] )) { // 获取失败时
			$this->error ( '中止操作：' . $wx ['errMsg'] );
		}
		$this->data = [ 
				'title' => '自定义菜单管理','version' => config ( 'version' ),'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),'public' => url ( '/', '', false ),'wx' => $wx ['data'],
				'aid' => $wx ['data'] ['id'] 
		];
		$this->in = isset ( $_POST ) && count ( $_POST ) > 0 ? $_POST : $_GET;
		if (is_array ( $this->in ) && (count ( $this->in ) > 2 || isset ( $this->in ['page'] ))) {
			$this->data ['def'] = isset ( $this->in ['def'] ) && $this->in ['def'] == 1 ? 1 : 0;
		} else {
			$this->data ['def'] = 1;
		}
	}
	/**
	 * index
	 * @method 主函数
	 * @todo 查询相应的数据，并返回视图
	 *
	 * @return string 视图
	 */
	public function index() {
		// Log::record('测试日志信息');
		$data = $this->data;
		$in = $this->in;
		$my_query [] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		if ($data ['def'])
			$where ['aid'] = $data ['aid'];
		if (isset ( $in ['search'] ) && (! empty ( $in ['search'] ) || $in ['search'] === 0)) {
			$where ['title'] = [ 
					'like','%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			//
		}
		
		// 获取数据
		$WxMenu = new WxMenu ();
		$where = isset ( $where ) ? $where : array ();
		$list = $WxMenu->where ( $where )->order ( 'id', 'desc' )->paginate ( '', false, [ 
				'query' => $my_query,'path' => url ( '/menu/index?', '', false ) 
		] ); // 保持链接稳定，尤其是修改等操作后跳转至本函数时
		
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		return $this->view ( './vIndex', $data );
	}
	/**
	 * add
	 * @method 新增自定义菜单
	 *
	 * @return string 视图
	 */
	public function add() {
		$data = $this->data;
		$in = $this->in;
		$data ['editor_type'] = 'add';
		// 验证数据,由于TP5没有办法验证数组的情况，更无法针对元素独立返回验证状态，故决定完全自定义
		$result = $this->myValidate ( $in );
		if (true !== $result) { // 当验证不通过时
			$data ['form'] = $in;
			$data ['error_msg'] = "<div class=row><div class=\"col-xs-12\" style=color:red>有错误，请修改后重试</div></div>";
			$result = $this->myIterator ( $result );
			$data ['form_error'] = $result;
			return $this->view ( './editor', $data );
		}
		$in ['menu'] = $this->setMenu ( $in );
		$in ['aid'] = $data ['aid'];
		$WxMenu = new WxMenu ();
		$res = $WxMenu->allowField ( true )->save ( $in );
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( '新增成功', url ( '/menu/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( '新增失败' );
		}
	}
	/**
	 * editor
	 * @method 编辑自定义菜单
	 *
	 * @return string 视图
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		if ('update' === $in ['editor_type']) {
			// 验证数据,由于TP5没有办法验证数组的情况，更无法针对元素独立返回验证状态，故决定完全自定义
			$result = $this->myValidate ( $in );
			if (true !== $result) { // 当验证不通过时
				$data ['form'] = $in;
				$data ['error_msg'] = "<div class=row><div class=\"col-xs-12\" style=color:red>有错误，请修改后重试</div></div>";
				$result = $this->myIterator ( $result );
				$data ['form_error'] = $result;
				return $this->view ( './editor', $data );
			}
			$in ['aid'] = $data ['aid'];
			$word = '更新';
			$res = $this->updateDate ( $in );
		} elseif ('oneDelete' == $in ['editor_type']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res ['errCode'] === 0) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/menu/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			} elseif ($res ['errCode'] == 403 || $res ['errCode'] == 404) {
				// 错误页面的默认跳转页面是返回前一页，通常不需要设置
				$this->error ( $word . '操作失败:' . $res ['errMsg'] );
			} else {
				$errid = $res ['data'];
				$strings = '';
				foreach ( $errid as $k => $v ) {
					$strings .= $v . '，';
				}
				$strings = rtrim ( $strings, '，' );
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/menu/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			}
		}
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/menu/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( $word . '操作失败' );
		}
	}
	/**
	 * addView
	 * @method 显示图文新增页
	 *
	 * @return string 视图
	 */
	public function addView() {
		$data = $this->data;
		$data ['editor_type'] = 'add'; // 定义页面类型：update更新，add新增
		$data ['menu'] = array ();
		return $this->view ( './editor', $data );
	}
	
	/**
	 * toEditor
	 * @method 跳转到编辑视图：查询数据，
	 * @todo 并返回渲染后的视图
	 *
	 * @param number $id 菜单id
	 * @return string 视图
	 */
	public function toEditor($id = 0) {
		$res = $this->find ( $id );
		if (empty ( $res ['errCode'] )) {
			$data = $this->data;
			$data ['editor_type'] = 'update';
			$data ['all_menu'] = $res ['data'];
			$data ['menu'] = isset ( $data ['all_menu'] ['menu'] ['button'] ) ? $data ['all_menu'] ['menu'] ['button'] : array ();
			return $this->view ( './editor', $data );
		} else {
			$this->error ( '获取图文失败：' . $res ['errMsg'] );
		}
	}
	/**
	 * up2Wx
	 * @method 上传菜单至微信
	 * @param number $id 菜单在数据表中的id
	 */
	public function up2Wx($id = 0) {
		if ($id == 0) {
			$this->error ( '上传失败：参数错误' );
		}
		$WxMenu = new WxMenu ();
		$res = $WxMenu->get ( $id );
		if ($res) {
			$menu=$res->getData('menu');
			$comm = $this->comm;
			$res = $comm->getAccessToken ();
			if ($res ['errCode']) {
				$this->error ( '上传失败：' . $res ['errMsg'] );
			}
			$config = $comm->getConfig ();
			$url = str_replace ( 'ACCESS_TOKEN', $res ['data'], $config ['wx_url'] ['create_menu'] );
			$res = $comm->doCurl ( $url, $menu );
			$res = $comm->wxErrCode ( $res );
			if ($res ['errCode']) {
				$this->error ( '上传失败：' . $res ['errMsg'] );
			} else {
				
				$r = [ ];
				$r ['up_to_wx_time'] = time ();
				$r ['active'] = 1;
				$res = $WxMenu->allowField ( true )->save ( $r, [ 
						'id' => $id 
				] );
				if ($res)
					$this->success ( '上传成功' );
				else
					$this->success ( '上传成功，但更新数据库时出错' );
			}
		} else {
			$this->error ( '上传失败：未查询到菜单数据' );
		}
	}
	/**
	 * myIterator
	 * @method 迭代器
	 * @todo 将验证器返回的错误信息 格式化：带上特定html标记。以便显示
	 *
	 * @param array $r 验证器返回的错误信息
	 * @return array 格式化后的数据
	 */
	private function myIterator($r = array()) {
		foreach ( $r as $key => $value ) {
			if (is_array ( $value )) {
				$res [$key] = $this->myIterator ( $value );
			} else {
				$value = "<div class=\"col-xs-4\" style=color:red>" . $value . "</div>";
				$res [$key] = $value;
			}
		}
		return $res;
	}
	
	/**
	 * find
	 * @method 查询菜单数据，菜单数据类型为array
	 *
	 * @param number $id 数据id
	 * @return mixed[] 查询结果数据
	 */
	private function find($id = 0) {
		if (empty ( $id )) {
			return [ 
					'errCode' => 401,'errMsg' => '图文id错误' 
			];
		} else {
			$res = WxMenu::get ( $id );
			$res = $res->toArray ();
			// $res['menu']=json_decode($res['menu'],1);
			if ($res) {
				return [ 
						'errCode' => 0,'data' => $res 
				];
			} else {
				return [ 
						'errCode' => 402,'errMsg' => '查询图文出错' 
				];
			}
		}
	}
	
	/**
	 * myValidate
	 * @method 数据验证器
	 *
	 * @param array $in 表单数据
	 * @return boolean|array TRUE或错误信息数组
	 */
	protected function myValidate($in = array()) {
		$in = empty ( $in ) ? $this->in : $in;
		$name = $in ['name'];
		$result = true;
		$allEmpty = true;
		foreach ( $name as $key => $value ) {
			$firstMenu = true;
			foreach ( $value as $k => $v ) {
				if (empty ( $v ) && $v !== '0') { // 菜单名为空
					if ($k == 0) {
						$res = $this->myValidataMenuType ( $in, $key, $k );
						if ($res) {
							if ($result === true)
								$result = array ();
							$result ['error_title'] [$key] [$k] = true;
							$result ['name'] [$key] [$k] = '菜单有值，则名称不能为空';
							$allEmpty = false;
						} else {
							$firstMenu = false;
							continue;
						}
					} else {
						$res = $this->myValidataMenuType ( $in, $key, $k );
						if ($res) {
							if ($result === true)
								$result = array ();
							$result ['error_title'] [$key] [$k] = true;
							$result ['name'] [$key] [$k] = '菜单有值，则名称不能为空';
							$allEmpty = false;
							if ($firstMenu === false) {
								$result ['error_title'] [$key] [0] = true;
								$result ['name'] [$key] [0] = '二级菜单有值，则一级菜单不能为空';
								$firstMenu = true;
							}
						} else {
							continue;
						}
					}
				} else { // 菜单名不为空
					$allEmpty = false;
					if ($k === 0 && ! empty ( $value [$k + 1] )) { // 有二级菜单，则一级菜单的key或url无须设置
					} else {
						$res = $this->myValidataMenuType ( $in, $key, $k );
						if ($res) {
							continue;
						} else {
							if ($result === true)
								$result = array ();
							$result ['type'] [$key] [$k] = true;
							$type = $in ['type'] [$key] [$k];
							$result ['error_title'] [$key] [$k] = true;
							$result [$type] [$key] [$k] = '菜单名有值，菜单内容不能为空';
						}
					}
				}
			}
		}
		if ($allEmpty) {
			$result = array ();
			$result ['error_title'] [0] [0] = true;
			$result ['name'] [0] [0] = '菜单没有数据，保存什么？';
		}
		return $result;
	}
	/**
	 * getTheData
	 * @method 获取多组数组中同角标的数据
	 *
	 * @param number $s 角标
	 * @return array [] 数据
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
	 * myValidataMenuType
	 * @method 菜单类型的独立验证
	 *
	 * @param array $in 表单数据
	 * @param number $key 一维角标
	 * @param number $k 二维角标
	 */
	private function myValidataMenuType($in = array(), $key = 0, $k = 0) {
		$type = $in ['type'] [$key] [$k];
		return empty ( $in [$type] [$key] [$k] ) ? false : true;
	}
	
	/**
	 * updateDate
	 * @method 更新数据库
	 *
	 * @param array $in 表单数据
	 * @return number|false
	 */
	private function updateDate($in = array()) {
		$WxMenu = new WxMenu ();
		$in ['menu'] = $this->setMenu ( $in );
		$res = $WxMenu->allowField ( true )->isUpdate ( true )->save ( $in );
		return $res;
	}
	
	/**
	 * oneDelete
	 * @method 单个删除，也用于批量删除中的循环
	 *
	 * @param array $in 表单传入的数据
	 * @param number $s 位置/角标
	 * @return number 影响数量
	 */
	private function oneDelete($in = [], $s = 0) {
		$r = $this->getTheData ( $s );
		$WxMenu = new WxMenu ();
		return $result = $WxMenu->get ( $r ['id'] )->delete ();
	}
	/**
	 * sDelete 批量删除
	 *
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return mixed[] 操作结果
	 */
	private function sDelete($in = [], $data = []) {
		$error = $success = 0;
		if (! isset ( $in ['ids'] )) {
			return [ 
					'errCode' => 403,'errMsg' => '未选中任何菜单' 
			];
		}
		foreach ( $in ['ids'] as $key => $val ) {
			$res = $this->oneDelete ( $in, $val );
			if ($res) {
				$success ++;
			} else {
				$error ++;
				$err_id [] = $in ['id'] [$val];
			}
		}
		if ($error < 1) {
			return [ 
					'errCode' => 0,'errMsg' => '成功' 
			];
		} elseif ($success < 1) {
			return [ 
					'errCode' => 404,'errMsg' => '未选中任何菜单' 
			];
		} else {
			return [ 
					'errCode' => 405,'errMsg' => '部分错误','data' => $err_id 
			];
		}
	}
	/**
	 * view
	 * @method 渲染视图模板
	 *
	 * @param String $temp 模板参数
	 * @param array $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
	private function setMenu($in = []) {
		foreach ( $in ['name'] as $k => $name ) {
			$b = NULL; // 循环内，数据格式化后临时存储容器
			foreach ( $name as $j => $v ) {
				if ($j == 0) { // 一级菜单
					if ($v) {
						$b ['name'] = $v;
						if (! empty ( $name [$j + 1] )) { // 存在二级菜单
							$b ['sub_button'] = array ();
						} else { // 不存在二级菜单
							$b ['type'] = $in ['type'] [$k] [$j];
							$res = $this->setKeyOrOtherTypeMenu ( $k, $j, $in );
							$b = array_merge ( $b, $res );
						}
					} else {
						break;
					}
				} else { // 二级菜单
					if ($v) {
						$c ['name'] = $v;
						$c ['type'] = $in ['type'] [$k] [$j];
						$res = $this->setKeyOrOtherTypeMenu ( $k, $j, $in );
						$c = array_merge ( $c, $res );
						$b ['sub_button'] [] = $c;
					} else {
						break;
					}
				}
			}
			if ($b) {
				$r ["button"] [] = $b;
			}
		}
		return $r;
	}
	
	/**
	 * setKeyOrOtherTypeMenu
	 * @method 格式化菜单具体内容
	 *
	 * @param number $k 一维角标
	 * @param number $j 二维角标
	 * @param array $in 表单数据
	 * @return array 格式化后的菜单
	 */
	private function setKeyOrOtherTypeMenu($k = 0, $j = 0, $in = []) {
		$type = $in ['type'] [$k] [$j];
		if ($type == "view") {
			$r ['url'] = $in ['view'] [$k] [$j];
		} else {
			$r ['key'] = $in [$type] [$k] [$j];
		}
		return $r;
	}
}
