<?php
namespace app\user\controller;

use think\Controller;
use app\user\model\WxUser;
use think\Exception;
use think\Config;
use think\Log;
class Index extends Controller {
	// 传入视图的参数
	protected $data;
	
	// 输入的数据 array
	protected $in;
	public function __construct() {
		global $ecms_hashur;
		parent::__construct ();
		$ecms_hashur = isset ( $ecms_hashur ) ? $ecms_hashur : '';
		// 获取默认公众号数据;
		$common = new \app\common\controller\Index ();
		$wx = $common->getDefaultWx ();
		if (! empty ( $wx ['errCode'] )) { // 获取失败时
			$this->error ( '中止操作：' . $wx ['errMsg'] );
		}
		$this->data = [ 
				'title' => '关注者管理',
				'version' => config ( 'version' ),
				'ecms_hashur' => $ecms_hashur,
				'form_error' => array (),
				'public' => url ( '/', '', false ),
				'wx' => $wx ['data'],
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
	 * @method 主方法
	 * @return string HTML代码
	 */
	public function index() {
		// Log::record('测试日志信息');
		$data = $this->data;
		$in = $this->in;
		$my_query [] = $data ['ecms_hashur'] ['href'];
		$my_query ['def'] = $data ['def'];
		$where['aid']=$data['aid'];
		if (isset ( $in ['search'] ) && (! empty ( $in ['search'] ) || $in ['search'] === 0)) {
			$where ['nick_name'] = [ 
					'like',
					'%' . $in ['search'] . '%' 
			];
			$data ['search'] = $in ['search'];
			$my_query ['search'] = $in ['search'];
		} else {
			$where = isset($where)?$where:'';
		}
		
		// 获取数据
		$WxUser = new WxUser ();
		$list = $WxUser->where ( $where )->paginate ( '', false, [ 
				'query' => $my_query,
				'path' => '' 
		] ); // 保持链接稳定，尤其是修改等操作后跳转至本函数时
		
		$data ['list'] = $list;
		$data ['page'] = $list->render ();
		return $this->view ( './vIndex', $data );
	}
	/**
	 * add
	 * @method 新增用户
	 * @abstract 无实际用途
	 * @return string HTML代码
	 */
	private function add() {
	}
	/**
	 * editor
	 * @method 编辑用户
	 * @abstract 某些功能有待开发，如“分组”
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		if (isset ( $in ['doSearch'] )) {
			$word = '查找';
			return $res = $this->index ();
		}
		if (! isset ( $in ['editor_type'] )) {
			$word = '编辑';
			$this->error ( '操作类型错误' );
		} elseif ('refresh_list' == $in ['editor_type']) {
			$word = '更新';
			/*
			 * 逻辑：
			 * 1、加锁：创建一个临时文件，内容无所谓
			 * 2、开始更新；
			 * 3、更新结束后，删除上述临时文件，以解锁
			 * 4、真结束
			 * 因此，隐含了最开始一步：判断是否有“加锁文件”
			 */
			$filename = __DIR__ . "/lock_update.php";
			if (file_exists ( $filename )) {
				$this->error ( '后台更新中，请勿重复操作' );
			}
			ignore_user_abort ( true ); // 设置：当用户关闭页面或关闭浏览器，后台继续执行，直至结束
			/*
			 * 设置脚本超时时间，防止意外：不能终止
			 * 该设置不宜太短，以免在用户的粉丝数较大的情况下
			 * 尚未完成更新即被中断
			 * 因此，暂定为30分钟
			 */
			set_time_limit ( 1800 );
			$file = fopen ( $filename, 'w+' );
			/*
			 * 其实下面两行写入的内容不是必要的。
			 * 为了安全，觉得还是加上保险一点
			 */
			$words = "<?php defined('APP_PATH') OR exit('No direct script access allowed'); ";
			fwrite ( $file, $words );
			fclose ( $file );
			$res = '';
			try {
				$res = $this->updateDate ();
			} catch ( \Exception $e ) {
				$this->error ( '系统错误，请稍后重试' );
			}finally {
    			@unlink ( $filename );
			}
			if (isset ( $res ['errCode'] ) && $res ['errCode']) {
				$this->error ( $word . '失败：' . $res ['errMsg'], NULL, '', -1 );
			} else {
				$this->success ( $word . '成功', url ( '/user/index', '', false ) . '?' . $data ['ecms_hashur'] ['href'] );
			}
		}
	}
	/**
	 * updateDate
	 * @method 更新
	 * @return mixed[] 操作结果
	 */
	private function updateDate() {
		/*
		 * 思路
		 * 1、清空数据表
		 * 2、获取数据
		 * 3、循环写入
		 * 4、结束
		 */
		$data = $this->data;
		$res = $this->getAccessToken ();
		if ($res ['errCode']) {
			return $res;
		}
		$this->data ['wx'] ['access_token'] = $res ['data'];
		$res = $this->update_user ();
		
		return $res;
	}
	/**
	 * update_user
	 * @method 更新
	 * @param number $aid 公众号id,默认公众号无需传入该值
	 * @return mixed[] 操作结果
	 */
	public function update_user($aid = NULL) {
		$data = $this->data;
		$aid = $aid ? $aid : ((isset ( $data ['aid'] ) && $data ['aid']) ? $data ['aid'] : NULL);
		if (NULL === $aid)
			return [ 
					'errCode' => 105,
					'errMsg' => '公众号id错误' 
			];
		$WxUser = new WxUser ();
		$res = $WxUser->where ( 'aid', $aid )->delete ();
		if (gettype ( $res ) !== 'integer') {
			return [ 
					'errCode' => 702,
					'errMsg' => '删除旧表时出错' 
			];
		}
		
		$continue = 1;
		$next_openid = NULL;
		Config::load ( APP_PATH . 'common/config.php' );
		$url = str_replace ( 'ACCESS_TOKEN', $data ['wx'] ['access_token'], config ( 'wx_url.get_user_list' ) );
		$url2 = str_replace ( 'ACCESS_TOKEN', $data ['wx'] ['access_token'], config ( 'wx_url.get_user_detail' ) ); // 用于获取用户详细信息
		$comm = new \app\common\controller\Index ();
		while ( $continue ) {
			$URL = str_replace ( 'NEXT_OPENID', $next_openid, $url );
			$res = $comm->doCurl ( $URL );
			$res = $comm->wxErrCode ( $res );
			if (! $res ['errCode']) {
				$r = $res ['data'];
				if($r['count'] == 0){
				    $result = array (
				        'errCode' => 0,
				        'errMsg' => '成功'
				    );
				    break;
				}
				$openid = $r ['data']['openid'];
				$userList = []; //用户数据列表
				foreach ( $openid as $v ) { // 获取详细信息，写到数据库中
					$URL = str_replace ( 'OPENID', $v, $url2 );
					$res = $comm->doCurl ( $URL );
					$res = $comm->wxErrCode ( $res );
					if (! $res ['errCode']) {
						$res = $res ['data'];
						$userList[] = $this->transWxData2Table ( $res );
					} else {
						continue;
					}
				}
				if(count($userList)>0){
					$WxUser->allowField ( true )->saveAll($userList);
				}
				if (isset ( $r ['next_openid'] ) && $r ['next_openid']) {
					$next_openid = $r ['next_openid'];
				} else {
					break;
				}
			} else {
				$continue = 0;
				$result = $res;
			}
		}
		
		if ($result) {
			return $result;
		} else {
			return $result = array (
					'errCode' => 0,
					'errMsg' => '成功'
			);
		}
	}
	/**
	 * getTheUser
	 * @method 获取指定union_id的用户信息
	 * @param string $union_id union_id
	 * @return mixed[] 操作结果
	 * 
	 */
	public function getTheUser($union_id = NULL) {
		if (NULL === $union_id) {
			return [ 
					'errCode' => 703,
					'errMsg' => '用户union_id传入值错误' 
			];
		}
		$WxUser = new WxUser ();
		$res = $WxUser->where ( 'union_id', $union_id )->find ();
		if ($res) {
			$res = $res->toArray ();
		} else {
			/*
			 * 数据库中未查询到，可能是由于“用户列表”正在刷新，或刚刚关注，
			 * 故，尝试从微信端获取数据
			 */
			$res = $this->updateTheUser ( $union_id );
			if($res['errCode'])
				return $res;
			else
				$res=$res['data'];
		}
		return [ 
				'errCode' => 0,
				'errMsg' => '获取用户基本信息',
				'data' => $res 
		];
	}
	/**
	 * updateTheUser
	 * @method 更新指定union_id 的用户信息
	 * @param number $union_id union_id
	 * @return mixed[] 操作结果
	 */
	public function updateTheUser($union_id = NULL) {
		if ($union_id === NULL) {
			return [ 
					'errCode' => 703,
					'errMsg' => '用户union_id传入值错误' 
			];
		}
		$filename = __DIR__ . "/lock_update.php";
		if (file_exists ( $filename )) {
			$this->error ( '后台更新用户信息中，请稍后重试' );
		}
		$comm = new \app\common\controller\Index ();
		$res = $comm->getAccessToken ();
		if ($res ['errCode'])
			return $res;
		$config = $comm->getConfig ();
		$url = str_replace ( [ 
				'ACCESS_TOKEN',
				'OPENID' 
		], [ 
				$res ['data'],
				$union_id 
		], $config ['wx_url'] ['get_user_detail'] );
		$res = $comm->doCurl ( $url );
		$res = $comm->wxErrCode ( $res );
		if ($res ['errCode'])
			return $res;
		$res = $this->transWxData2Table ( $res ['data'] );
		$WxUser = new WxUser ();
		$WxUser->allowField ( true )->save ( $res );
		return [ 
				'errCode' => 0,
				'errMsg' => '更新特定用户数据',
				'data' => $res 
		];
	}
	/**
	 * transWxData2Table
	 * @method 转换数据格式
	 * @todo 以便保存到数据库中
	 * @param array $res 待转换数据
	 * @return mixed[] 操作结果
	 */
	private function transWxData2Table($res = NULL) {
		$r = [ ];
		$r ['aid'] = $this->data ['aid'];
		$r ['subscribe'] = $res ['subscribe'];
		$r ['union_id'] = isset ( $res ['unionid'] ) ? $res ['unionid'] : $res ['openid'];
		$r ['nick_name'] = $res ['nickname'];
		$r ['sex'] = $res ['sex'];
		$r ['city'] = $res ['city'];
		$r ['country'] = $res ['country'];
		$r ['province'] = $res ['province'];
		$r ['language'] = $res ['language'];
		$r ['head_img_url'] = $res ['headimgurl'];
		$r ['subscribe_time'] = $res ['subscribe_time'];
		$r ['remark'] = $res ['remark'];
		$r ['group_id'] = $res ['groupid'];
		return $r;
	}
	/**
	 * getAccessToken
	 * @todo 获取access_token 
	 * @return mixed[] 操作结果
	 */
	private function getAccessToken() {
		$WxWx = new \app\common\controller\Index ();
		$res = $WxWx->getAccessToken ();
		return $res;
	}
	/**
	 * view
	 * @method 显示
	 * @param string $temp 模板路径
	 * @param array $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
}
