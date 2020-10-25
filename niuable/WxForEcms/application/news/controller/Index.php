<?php
namespace app\news\controller;

use think\Controller;
use app\news\model\WxNews;
use app\file\model\WxFile;
use think\debug\Html;
use app\file\service\UpService;
use app\common\model\WxApp;
use app\news\service\UpNews;

class Index extends Controller {
	// 传入视图的参数
	protected $data;
	// 公用数据获取类的实例
	protected $comm;
	// 输入的数据 array
	protected $in;
	// 本模块模型实例
	protected $WxNews;
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
				'title' => '图文管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ),
				'wx' => $wx ['data'],
				'aid' => $wx ['data'] ['id'],
				'lur' => $lur,
				'isadmin' => $isadmin 
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
	 * @method 主函数，显示主页
	 *
	 * @return string 渲染后的页面
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
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = isset ( $where ) ? $where : array ();
		}
		// 获取数据
		$WxNews = new WxNews ();
		$list = $WxNews->where ( $where )->paginate ( '', false, [ 
				'query' => $my_query,
				'path' => '' 
		] ); // 保持链接稳定，尤其是修改等操作后跳转至本函数时
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		return $this->view ( './vIndex', $data );
	}
	/**
	 * add
	 * @method 新增图文
	 *
	 * @return string|NULL Html代码 
	 */
	public function add() {
		$data = $this->data;
		$r = $this->in;
		$data ['editor_type'] = 1;
		// 验证数据合法性
		$result = $this->validate ( $r, 'Index' );
		if (true !== $result) { // 当验证不通过时
			$data ['news'] = $r;
			foreach ( $result as $k => $v ) {
				$v = "<div class=\"col-xs-4\" style=color:red>" . $v . "</div>";
				$result [$k] = $v;
			}
			$data ['form_error'] = $result;
			$data ['error_msg'] = "<div class='text-danger'><h3>有错误，请检查后重试</h3></div>";
			return $this->view ( './editor', $data );
		}
		$r ['aid'] = $data ['aid'];
		$WxNews = new WxNews ();
		$res = $WxNews->allowField ( true )->save ( $r );
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( '新增成功', url ( '/news/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( '新增失败' );
		}
	}
	/**
	 * editor
	 * @method 编辑图文
	 * @return string Html代码
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		
		if ('refresh' === $in ['editor_type']) {
			// 验证数据
			$result = $this->validate ( $in, 'Index' );
			if (true !== $result) {
				$data ['form'] = $in;
				foreach ( $result as $k => $v ) {
					$v = "<div class=\"col-xs-4\" style=color:red>" . $v . "</div>";
					$result [$k] = $v;
				}
				$data ['form_error'] = $result;
				$data ['error_msg'] = "<div class='text-danger'><h3>有错误，请检查后重试</h3></div>";
				
				return $this->view ( './editor', $data );
			}
			
			$word = '更新';
			$res = $this->updateDate ( $in, $data );
		} elseif ('oneDelete' == $in ['editor_type']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res ['errCode'] === 0) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/news/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
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
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/newx/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			}
		}
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/news/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( $word . '操作失败' );
		}
	}
	/**
	 * addView
	 * @method 显示图文新增页
	 * @return String HTML代码
	 */
	public function addView() {
		$data = $this->data;
		$data ['news'] ['send_time'] = date ( 'Y-m-d H:i', time () );
		$data ['editor_type'] = 1; // 定义页面类型：0更新，1新增
		return $this->view ( './editor', $data );
	}
	
	/**
	 * toEditor
	 * @method 获取图文数据，并返回图文编辑页
	 *
	 * @param number $id 图文id
	 * @return string 图文编辑页视图
	 */
	public function toEditor($id = NULL) {
		$res = $this->find ( $id );
		if (empty ( $res ['errCode'] )) {
			$data = $this->data;
			$data ['editor_type'] = 0;
			$data ['news'] = $res ['data'];
			return $this->view ( './editor', $data );
		} else {
			$this->error ( '获取图文失败：' . $res ['errMsg'] );
		}
	}
	/**
	 * getTheNews
	 * @method 获取指定id的图文
	 *
	 * @param string|int $id 图文id
	 * @return mixed[] 操作结果
	 */
	public function getTheNews($id = NULL) {
		if ($id === NULL || empty ( $id ))
			return [ 
					'errCode' => 401,
					'errMsg' => '图文id错误' 
			];
		$WxNews = new WxNews ();
		$res = $WxNews->get ( $id );
		if ($res)
			$news = $res->toArray ();
		return [ 
				'errCode' => 0,
				'errMsg' => '数据库读写成功',
				'data' => $news 
		];
	}
	
	/**
	 * newsList
	 * @method 获取图文列表，并显示出来
	 * @abstract 用于模态框内的搜索
	 *
	 * @return string HTML代码
	 */
	public function newsList() {
		$data = $this->data;
		$in = $this->in;
		$my_query ['ecms_hashur'] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		$data ['list_num'] = $in ['list_num']; // 传递图文收集容器中小容器的序号
		$my_query ['list_num'] = 1;
		if ($data ['def'])
			$where ['aid'] = $data ['aid'];
		if (isset ( $in ['search'] ) && (! empty ( $in ['search'] ) || $in ['search'] === 0)) {
			$where ['title'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = isset ( $where ) ? $where : array ();
		}
		// 获取数据
		$WxNews = new WxNews ();
		$list = $WxNews->where ( $where )->paginate ( '', false, [ 
				'query' => $my_query,
				'path' => url ( 'news/index/newsList', '', false ) 
		] ); // 保持链接稳定，尤其是修改等操作后跳转至本函数时
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		echo $this->fetch ( './list', $data );
	}
	
	/**
	 * up2Wx
	 * @method 上传 单/多 图文 模块内使用
	 *
	 * @param array $in
	 * @param array $data
	 * @return array 其中元素data仍是数组，而非json
	 */
	public function up2Wx($in = [], $data = []) {
		$in = empty ( $in ) ? $this->in : $in;
		$data = empty ( $data ) ? $this->data : $data;
		$word = '上传图文';
		if (! isset ( $in ['id'] ) || empty ( $in ['id'] )) {
			$this->error ( '缺少必要参数id' );
		}
		
		// 获取待上传的图文数据json
		$app = new WxApp($this->data['aid']);
		$service = new UpNews();
		$res = $service->getNewsForUp ($app, $in ['id'] );
		if (! $res ['errCode']) {
			$comm = $this->comm;
			$res2 = $comm->getAccessToken ();
			if (! $res2 ['errCode']) { // 正确获取access_token
				$accessToken = $res2 ['data'];
				$config = $comm->getConfig ();
				// $url=str_replace('ACCESS_TOKEN', $accessToken, $config['wx_url']['up_news_for_mass']);
				$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['up_news_forever'] );
				$res = $comm->doCurl ( $url, urldecode ( json_encode ( $res ['data'] ) ) );
				$res = $comm->wxErrCode ( $res );
				if (! $res ['errCode']) { // 正确上传
					$WxNews = new WxNews ();
					$r = $res ['data'];
					$r ['create_at'] = (isset ( $res ['data'] ['create_at'] ) && $res ['data'] ['create_id']) ? $res ['data'] ['create_at'] : time ();
					$res = $WxNews->isUpdate ( true )->allowField ( true )->save ( $r, [ 
							'id' => $in ['id'] 
					] );
					if ($res === 1) {
						$res = [ 
								'errCode' => 0,
								'errMsg' => '数据库更新成功' 
						];
					} else {
						$res = [ 
								'errCode' => 404,
								'errMsg' => '数据库读写失败' 
						];
					}
				}
			} else {
				$res = $res2;
			}
		}
		if ($res ['errCode']) {
			$this->error ( $word . '失败：' . $res ['errMsg'], '', '', - 1 );
		} else {
			$this->success ( $word . '成功', url ( 'news/index/index' ) . '?' . $data ['ecms_hashur'] ['href'] );
		}
	}
	/**
	 * up2Wx2
	 * @method 上传 单/多 图文
	 * @abstract 供模块外部(其他模块)调用
	 *
	 * @param array $in
	 * @param array $data
	 * @return array 其中元素data仍是数组，而非json
	 */
	public function up2Wx2($in = [], $data = []) {
		$in = empty ( $in ) ? $this->in : $in;
		$data = empty ( $data ) ? $this->data : $data;
		$word = '上传图文';
		if (! isset ( $in ['id'] ) || empty ( $in ['id'] )) {
			$this->error ( '缺少必要参数id' );
		}
		
		// 获取待上传的图文数据json
		$service = new UpNews();
		$app = new WxApp($this->data['aid']);
		$res = $service->getNewsForUpMass ($app, $in ['id'] );
		if (! $res ['errCode']) {
			$comm = $this->comm;
			$res2 = $comm->getAccessToken ();
			if ($res2 ['errCode']) { // 获取access_token发生错误
				$res = $res2;
			} else {
				$accessToken = $res2 ['data'];
				$config = $comm->getConfig ();
				$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['up_news_for_mass'] );
				// $url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['up_news_forever'] );
				$res = $comm->doCurl ( $url,  urldecode ( json_encode ( $res ['data']) ));
				$res = $comm->wxErrCode ( $res );
			}
		}
		return $res;
	}
	
	/**
	 * find
	 * @method 从数据库中读取图文信息
	 *
	 * @param number $id 图文id
	 * @return mixed[] 操作结果
	 */
	private function find($id = NULL) {
		if (empty ( $id )) {
			return [ 
					'errCode' => 401,
					'errMsg' => '图文id错误' 
			];
		} else {
			$WxNews = new WxNews ();
			$res = $WxNews->get ( $id );
			if ($res) {
				return [ 
						'errCode' => 0,
						'data' => $res 
				];
			} else {
				return [ 
						'errCode' => 402,
						'errMsg' => '查询图文出错' 
				];
			}
		}
	}
	/**
	 * getTheData
	 * @method 获取多维数组中每个元素（该元素为数组）某个角标的值; 将这些值重新组合成一个一维数组
	 * @param number $s 位置
	 * @return [] 操作结果
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
	 * @method 更新数据
	 *
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return boolean 是否成功
	 */
	private function updateDate($in, $data) {
		$WxNews = new WxNews ();
		$res = $WxNews->allowField ( true )->isUpdate ( true )->save ( $in );
		return true;
	}
	/**
	 * oneDelete
	 * @method 删除单个图文
	 *
	 * @param array $in 数据
	 * @param number $s 位置
	 * @return number 影响结果
	 */
	private function oneDelete($in, $s) {
		$r = $this->getTheData ( $s );
		$WxNews = new WxNews ();
		return $result = $WxNews->get ( $r ['id'] )->delete ();
	}
	/**
	 * sDelete
	 * @method 删除多个图文
	 *
	 * @param array $in 数据
	 * @param array $data 参数
	 * @return mixed[] 操作结果
	 */
	private function sDelete($in, $data) {
		$error = $success = 0;
		if (! isset ( $in ['ids'] )) {
			return [ 
					'errCode' => 403,
					'errMsg' => '未选中任何图文' 
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
					'errCode' => 0,
					'errMsg' => '成功' 
			];
		} elseif ($success < 1) {
			return [ 
					'errCode' => 404,
					'errMsg' => '未选中任何图文' 
			];
		} else {
			return [ 
					'errCode' => 405,
					'errMsg' => '部分错误',
					'data' => $err_id 
			];
		}
	}
	/**
	 * view
	 * @method 渲染模板并返回结果
	 *
	 * @param string $temp 模板路径
	 * @param mixed[] $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
