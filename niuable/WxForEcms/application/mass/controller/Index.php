<?php
namespace app\mass\controller;

use app\file\model\WxFile;
use app\mass\model\WxMass;
use think\Controller;
use WxSDK\resource\Config;
use WxSDK\core\model\Model;
use WxSDK\Request;
use WxSDK\Url;
use WxSDK\core\common\Ret;
use app\common\model\WxApp;

/**
 *
 * @author WangWei
 * @time 2016年11月22日 下午11:14:54
 *        
 */
class Index extends Controller {
	// 传入视图的参数
	protected $data;
	// 公用数据获取类的实例
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
				'title' => '群发管理',
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
			$this->data ['panel'] = isset ( $this->in ['panel'] ) ? $this->in ['panel'] : 0;
		} else {
			$this->data ['def'] = 1;
			$this->data ['panel'] = 0;
		}
	}
	/**
	 * index
	 * @method 主函数
	 * @todo 查询相应的数据，并返回视图
	 *
	 * @param number $panel 面板参数 0|NULL 待群发，1 已群发，2 新建群发，3修改群发
	 * @return string 视图
	 */
	public function index() {
		// Log::record('测试日志信息');
		$data = $this->data;
		$in = $this->in;
		if (isset ( $in ['order_by_time'] ) && ! empty ( $in ['order_by_time'] )) {
			$order = [ 
					'send_time' => $in ['order_by_time'] 
			];
			$data ['order_by_time'] = $in ['order_by_time'];
		} else {
			$order = [ 
					'send_time' => 'desc' 
			];
			// $data['order_by_time']='desc';
		}
		if (! isset ( $data ['panel'] ) || $data ['panel'] == 0 || $data ['panel'] == 1) {
			$data ['action'] = 'editor';
			if (! isset ( $data ['panel'] ) || $data ['panel'] == 0) {
				$where ['is_do'] = [ 
						'exp',
						'in (0) or isNull(is_do)' 
				];
			} else {
				$where ['is_do'] = 1;
			}
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
				//
			}
			
			// 获取数据
			$WxMass = new WxMass ();
			$where = isset ( $where ) ? $where : array ();
			if (isset ( $data ['panel'] ) && $data ['panel'] == 1) {
				$where ['is_do'] = 1;
			}
			$list = $WxMass->where ( $where )->order ( $order )->paginate ( '', false, [ 
					'query' => $my_query,
					'path' => url ( '/mass/index?', '', false ) 
			] );
			if (! empty ( $list )) {
				$r = $list->toArray ();
				$r = $this->getMassContent ( $r ['data'] );
				if (isset ( $data ['panel'] ) && $data ['panel'] == 1) { // 已群发，获取群发结果
					foreach ( $r as $k => $v ) {
						$res = $this->getMassStatus ( $v ['id'] );
						if ($res ['errCode']) {
							continue;
						} else {
							$v = array_merge ( $v, $res ['data'] );
						}
						$r [$k] = $v;
					}
				}
			} else {
				$r = [ ];
			}
			$data ['list'] = $r;
			$data ['page'] = $list->render ();
			$data ['reply_content'] = '';
		} else {
			$data ['is_do'] = 0;
			$data ['editor_type'] = 'add';
			$data ['msg_type'] = isset ( $in ['msg_type'] ) ? $in ['msg_type'] : 'text';
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
			// $data ['send_time'] = time () + 86400;
		}
		return $this->view ( './vIndex', $data );
	}
	/**
	 * add
	 * @method 新增群发
	 *
	 * @return string 视图
	 */
	public function add() {
		$data = $this->data;
		$in = $this->in;
		$data ['editor_type'] = 'add';
		$result = $this->validate ( $in, 'Index' );
		if (true !== $result) { // 当验证不通过时
			$data = array_merge ( $data, $in );
			$data ['def'] = 1;
			$data ['error_msg'] = "<div class=row><div class=\"col-xs-12\" style=color:red>有错误，请修改后重试</div></div>";
			$data ['form_error'] = $result;
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
			return $this->view ( './vIndex', $data );
		}
		$in ['aid'] = $data ['aid'];
		$WxMass = new WxMass ();
		$res = $WxMass->allowField ( true )->save ( $in );
		if ($res) {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( '新增成功', url ( '/mass/index/index/?panel=2&def=1', '', false ) . $data ['ecms_hashur'] ['href'] );
		} else {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$this->error ( '新增失败' );
		}
	}
	/**
	 * editor
	 * @method 编辑群发
	 *
	 * @return string 视图
	 */
	public function editor() {
		$in = $this->in;
		$data = $this->data;
		$word = '';
		if (! isset ( $in ['editor_type'] )) {
			$res = [ 
					'errCode' => 705,
					'errMsg' => '缺少必要参数editor_type' 
			];
		} else if ('save' == $in ['editor_type']) { // editor方法内的save,更新
			$data ['editor_type'] = $in ['editor_type'];
			$data ['action'] = 'editor';
			$result = $this->validate ( $in, 'Index' );
			if (true !== $result) { // 当验证不通过时
				$data = array_merge ( $data, $in );
				$data ['error_msg'] = "<div class=row><div class=\"col-xs-12\" style=color:red>有错误，请修改后重试</div></div>";
				$data ['form_error'] = $result;
				$data ['def'] = 1;
				$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
				return $this->view ( './vIndex', $data );
			}
			$in ['aid'] = $data ['aid'];
			$word = '更新';
			$WxMass = new WxMass ();
			$res = $WxMass->allowField ( true )->isUpdate ( true )->save ( $in, [ 
					'id' => $in ['id'] 
			] );
			if ($res) {
				$res = [ 
						'errCode' => 0,
						'errMsg' => '数据保存成功' 
				];
			} else {
				$res = [ 
						'errCode' => 702,
						'errMsg' => '数据库读写失败' 
				];
			}
		} elseif ('up_to_wx' == $in ['editor_type']) {
			
			$word = '上传群发';
			$res = $this->up2Wx ();
		} elseif ('update_to_wx' == $in ['editor_type']) {
			
			$word = '更新群发素材';
			$res = $this->updateMass2Wx ();
		} elseif ('doPreview' == $in ['editor_type']) {
			$word = "预览群发";
			$result = $this->update2Wx ();
			if ($result ['errCode']) {
				$res = $result;
			} else {
				$this->in ['media'] = $result ['data'];
				$res = $this->doPreview ();
			}
		} elseif ('nowDoMass' == $in ['editor_type']) {
			$word = "群发";
			/*
			 * 应该先有判断是否需要上传或更新的步骤，
			 */
			$result = $this->update2Wx ();
			if ($result ['errCode']) {
				$res = $result;
			} else {
				$this->in ['media'] = $result ['data'];
				$res = $this->doMass ();
				if ($res->ok()) {
					$id = $in ['id'] [$in ['site'] - 1];
					$r = $res->data;
					$r ['is_do'] = 1;
					$r ['is_auto'] = isset ( $in ['is_auto'] ) ? $in ['is_auto'] : 0;
					$r ['do_send_time'] = time ();
					$WxMass = new WxMass ();
					$result = $WxMass->isUpdate ( true )->allowField ( true )->save ( $r, [ 
							'id' => $id 
					] );
					if (1 === $result) {
						$res = [ 
								'errCode' => 0,
								'errMsg' => '数据更新成功' 
						];
					} else {
						$res = [ 
								'errCode' => 707,
								'errMsg' => '群发成功，但数据库更新失败' 
						];
					}
				}else{
				    $res = [
				        'errCode'=>$res->errCode,
				        'errMsg'=> $res->errMsg
				    ];
				}
			}
		} elseif ('oneDelete' == $in ['editor_type']) {
			$word = '删除';
			$res = $this->oneDelete ( $in, $in ['site'] - 1 );
		} elseif ('sDelete' == $in ['editor_type']) {
			$word = '批量删除';
			$res = $this->sDelete ( $in, $data );
			if ($res ['errCode'] === 0) {
				// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
				$this->success ( $word . '操作成功', url ( '/mass/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
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
				$this->success ( $word . '操作部分成功，出错的id有' . $strings, url ( '/mass/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
			}
		} else {
			return $this->index ();
		}
		if ($res ['errCode']) {
			// 错误页面的默认跳转页面是返回前一页，通常不需要设置
			$res ['errMsg'] = isset ( $res ['errMsg'] ) ? "：" . $res ['errMsg'] : '';
			$this->error ( $word . '操作失败' . $res ['errMsg'], null, '', 5 );
		} else {
			// 设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
			$this->success ( $word . '操作成功', url ( '/mass/index?', '', false ) . $data ['ecms_hashur'] ['href'] );
		}
	}
	/**
	 * getMassStatus
	 * @method 获取群发状态
	 * @param number $id 群发记录id
	 * @return mixed[] 操作结果
	 */
	private function getMassStatus($id = 0) {
		if (empty ( $id )) {
			return [ 
					'errCode' => 701,
					'errMsg' => '群发id错误' 
			];
		}
		$WxMass = new WxMass ();
		$res = $WxMass->get ( $id );
		if ($res) {
			$r = $res->toArray ();
			if (isset ( $r ['mass_status'] ) && ! empty ( $r ['mass_status'] )) {
				$r ['mass_status'] = strtolower ( $r ['mass_status'] );
				if ($r ['mass_status'] == 'send_sucess') {
					return [ 
							'errCode' => 0,
							'errMsg' => '已成功，并早已记录到数据库中',
							'data' => $r 
					];
				}
			}
			$comm = $this->comm;
			$config = $comm->getConfig ();
			$res = $comm->getAccessToken ();
			if ($res ['errCode']) {
				return $res;
			} else {
				$accessToken = $res ['data'];
			}
			$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['get_mass_status'] );
			$r = json_encode ( [ 
					'msg_id' => $r ['msg_id'] 
			] );
			$res = $comm->doCurl ( $url, $r );
			$res = $comm->wxErrCode ( $res );
			if ($res ['errCode']) {
				return $res;
			} else {
				$r2 = [ ];
				$r2 ['mass_status'] = strtolower ( $res ['data'] ['msg_status'] );
				$res = $WxMass->isUpdate ( true )->allowField ( true )->save ( $r, [ 
						'id' => $id 
				] );
				if (1 === $res) {
					$r ['mass_status'] = $r2 ['mass_status'];
					return [ 
							'errCode' => 0,
							'errMsg' => '已成功更新群发状态，并已记录到数据库中',
							'data' => $r 
					];
				} else {
					return [ 
							'errCode' => 702,
							'errMsg' => '数据库读写失败',
							'data' => $r 
					];
				}
			}
		} else {
			return [ 
					'errCode' => 703,
					'errMsg' => '未查询到数据' 
			];
		}
	}
	/**
	 * update2Wx
	 * @method 更新到微信端
	 *
	 * @return mixed[] 操作结果
	 */
	private function update2Wx() {
		/*
		 * =》获取群发数据
		 * =》获取群发素材信息
		 * =》上传素材（图片、视频等，获取media_id)
		 * =》上传群发内容
		 */
		$in = $this->in;
		$WxMass = new WxMass ();
		$id = $in ['id'] [$in ['site'] - 1];
		$res = $WxMass->get ( $id );
		if ($res) {
			$r = $res->toArray ();
			if ($r ['media_id']) {
				// 格式化数据；
				$this->data ['mass'] = $r;
				return [ 
						'errCode' => 0,
						'errMsg' => '无需更新',
						'data' => $r ['media_id'] 
				];
			} else {
				return $this->up2Wx ();
			}
		} else {
			$res = [ 
					'errCode' => 703,
					'errMsg' => '未查询到数据' 
			];
		}
		return $res;
	}
	
	/**
	 * up2Wx
	 * @method 上传群发数据到微信
	 * @return mixed[] 操作结果
	 */
	private function up2Wx() {
		/*
		 * =》获取群发数据
		 * =》获取群发素材信息
		 * =》上传素材（图片、视频等，获取media_id)
		 * =》上传群发内容
		 */
		$in = $this->in;
		$WxMass = new WxMass ();
		$id = $in ['id'] [$in ['site'] - 1];
		$res = $WxMass->get ( $id );
		if ($res) {
			$data ['editor_type'] = $in ['editor_type'];
			$r = [ ];
			$this->data ['mass'] = $r = $res->toArray ();
			switch ($r ['msg_type']) {
				case '文本' :
					$res = [ 
							'errCode' => 0,
							'errMsg' => '文本类型，无须上传',
							'data' => $res 
					];
					break;
				case '视频' :
					$WxFile = new WxFile ();
					$res = $WxFile->get ( $r ['video'] );
					if ($res) {
						$comm = $this->comm;
						$config = $comm->getConfig ();
						$res2 = $comm->getAccessToken ();
						if ($res2 ['errCode']) {
							return $res2;
						} else {
							$accessToken = $res2 ['data'];
						}
						$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['up_video_for_mass'] );
						if (empty ( $res ['short_media_id'] )) {
							$file = new \app\file\controller\Index ();
							$res3 = $file->getUpdateVideo2Wx ( $r ['video'] );
							$videoData ['media_id'] = $res3 ['data'] ['media_id'];
						} else {
							$videoData ['media_id'] = $res ['short_media_id'];
						}
						$videoData ['title'] = urlencode ( $res ['title'] );
						$videoData ['description'] = urlencode ( $res ['description'] );
						
						$res = $comm->doCurl ( $url, urldecode ( json_encode ( $videoData ) ) );
						$res = $comm->wxErrCode ( $res );
					} else {
						return [ 
								'errCode' => 508,
								'errMsg' => '未找到相应附件' 
						];
					}
					// $WxFile = new \app\file\controller\Index ();
					// $res = $WxFile->getUp2WxForeverFile ( $r ['video'] );
					break;
				case '图片' :
					$WxFile = new \app\file\controller\Index ();
					$res = $WxFile->getUp2WxForeverFile ( $r ['img'] );
					break;
				case '图文' :
					$WxNews = new \app\news\controller\Index ();
					$r2 ['id'] = $r ['news'];
					$res = $WxNews->up2Wx2 ( $r2, $this->data );
				// $res = [
				// 'errCode' => 0,
				// 'errMsg' => 'cesh',
				// 'data' => [
				// 'media_id' => 'ceshizhong'
				// ]
				// ];//模拟
			}
			
			if ($r ['msg_type'] != '文本' && ! $res ['errCode']) {
				$r = [ ];
				$this->data ['mass'] ['media_id'] = $r ['media_id'] = $res ['data'] ['media_id'];
				$result = $WxMass->isUpdate ( true )->allowField ( true )->save ( $r, [ 
						'id' => $id 
				] );
				if ($result !== 1) {
					$res = [ 
							'errCode' => 702,
							'errMsg' => '读写数据库错误' 
					];
				} else {
					$res = [ 
							'errCode' => 0,
							'errMsg' => '读写数据成功',
							'data' => $res ['data'] 
					];
				}
			}
		} else {
			$res = [ 
					'errCode' => 703,
					'errMsg' => '未查询到数据' 
			];
		}
		return $res;
	}
	/**
	 * updateMass2Wx
	 * @method 更新群发的素材
	 * @todo 并更新群发素材在群发表中的media_id等记录
	 *
	 * @return mixed[] 操作结果
	 */
	private function updateMass2Wx() {
		/*
		 * =》获取群发数据
		 * =》获取群发素材信息
		 * =》上传素材（图片、视频等，获取media_id)
		 * =》上传群发内容
		 */
		$in = $this->in;
		$WxMass = new WxMass ();
		$id = $in ['id'] [$in ['site'] - 1];
		$res = $WxMass->get ( $id );
		if ($res) {
			$data ['editor_type'] = $in ['editor_type'];
			$r = [ ]; // 群发数据 数组
			$this->data ['mass'] = $r = $res->toArray ();
			switch ($r ['msg_type']) {
				case '文本' :
					$res = [ 
							'errCode' => 0,
							'errMsg' => '文本类型，无须上传',
							'data' => $res 
					];
					break;
				case '视频' :
					$WxFile = new WxFile ();
					$res = $WxFile->get ( $r ['video'] );
					if ($res) {
						$comm = $this->comm;
						$config = $comm->getConfig ();
						$res2 = $comm->getAccessToken ();
						if ($res2 ['errCode']) {
							return $res2;
						} else {
							$accessToken = $res2 ['data'];
						}
						$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['up_video_for_mass'] );
						$file = new \app\file\controller\Index ();
						if (empty ( $res ['short_media_id'] )) {
							$res3 = $file->getUpdateVideo2Wx ( $r ['video'] );
							$videoData ['media_id'] = $res3 ['data'] ['media_id'];
						} else {
							$videoData ['media_id'] = $res ['short_media_id'];
						}
						if ($res ['thumb']) {
							$video ['thumb'] = str_replace ( '//', '/', str_replace ( '\\', '/', $res ['thumb'] ) );
							$str = explode ( '/', $video ['thumb'] );
							$img ['name'] = $str [count ( $str ) - 1];
							$img ['path'] = rtrim ( str_replace ( $img ['name'], '', $video ['thumb'] ), '/' );
							$res4 = $WxFile->where ( [ 
									'name' => $img ['name'],
									'path' => $img ['path'] 
							] )->find ();
							if ($res4) { // 查询封面图片，成功
								$res4 = $file->getThumbMediaId ( $res4 ['id'] );
								// $res=$file->getUp2WxForeverFile($res['id']);
								if ($res4 ['errCode']) { // 上传失败
									return $res4;
								} else {
									$res4 ['data'] = isset ( $res4 ['data'] ['media_id'] ) ? $res4 ['data'] ['media_id'] : $res4 ['data'];
									$thumb_media_id = $res4 ['data'];
								}
							} else { // 查询图片失败
								return $res = [ 
										'errCode' => 508,
										'errMsg' => '未找到封面文件' 
								];
							}
							$videoData ['thumb_media_id'] = $thumb_media_id;
						}
						$videoData ['title'] = urlencode ( $res ['title'] );
						$videoData ['description'] = urlencode ( $res ['description'] );
						$res = $comm->doCurl ( $url, urldecode ( json_encode ( $videoData ) ) );
						$res = $comm->wxErrCode ( $res );
					} else {
						return [ 
								'errCode' => 508,
								'errMsg' => '未找到相应附件' 
						];
					}
					
					break;
				case '图片' :
					$file = new \app\file\controller\Index ();
					$res = $file->getUpdate2WxFile ( $r ['img'] );
					break;
				case '图文' :
					$news = new \app\news\controller\Index ();
					$r2 ['id'] = $r ['news'];
					$res = $news->up2Wx2 ( $r2, $this->data );
				// $res = [
				// 'errCode' => 0,
				// 'errMsg' => 'cesh',
				// 'data' => [
				// 'media_id' => 'ceshizhong'
				// ]
				// ];//模拟
			}
			if ($r ['msg_type'] != '文本' && ! $res ['errCode']) {
				$r = [ ];
				$this->data ['mass'] ['media_id'] = $r ['media_id'] = $res ['data'] ['media_id'];
				$result = $WxMass->isUpdate ( true )->allowField ( true )->save ( $r, [ 
						'id' => $id 
				] );
				if ($result !== 1) {
					$res = [ 
							'errCode' => 702,
							'errMsg' => '读写数据库错误' 
					];
				} else {
					$res = [ 
							'errCode' => 0,
							'errMsg' => '读写数据成功',
							'data' => $res ['data'] 
					];
				}
			}
		} else {
			$res = [ 
					'errCode' => 703,
					'errMsg' => '未查询到数据' 
			];
		}
		return $res;
	}
	/**
	 * doMass
	 * @method 执行群发
	 * @return Ret 操作结果
	 */
	private function doMass() {
		$data = $this->data;
		$r ['filter'] ['is_to_all'] = true;
		$r ['filter'] ['group_id'] = 0;

		switch ($data ['mass'] ['msg_type']) {
			case '文本' :
				$r ['msgtype'] = 'text';
				$r ['text'] ['content'] = urlencode ( $data ['mass'] ['text'] );
				break;
			case '图片' :
				$r ['msgtype'] = 'image';
				$r ['image'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '音频' :
				$r ['msgtype'] = 'voice';
				$r ['voice'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '视频' :
				$r ['msgtype'] = 'mpvideo';
				$r ['mpvideo'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '图文' :
				$r ['msgtype'] = 'mpnews';
				$r ['mpnews'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '卡券' :
				$r ['msgtype'] = 'wxcard';
				$r ['wxcard'] ['card_id'] = $data ['mass'] ['media_id'];
				break;
		}
		$accessToken = new WxApp($this->data['aid']);
		$model = new Model($r);
		$request = new Request($accessToken, $model, new Url(Config::$do_mass_by_tag));
		$res = $request->run();
		return $res;
	}
	
	/**
	 * doPreview
	 * @method 群发预览
	 *
	 * @return mixed[] 操作结果
	 */
	private function doPreview() {
		$in = $this->in;
		$data = $this->data;
		$comm = $this->comm;
		$config = $comm->getConfig ();
		$res = $comm->getAccessToken ();
		if ($res ['errCode']) {
			return $res;
		}
		$accessToken = $res ['data'];
		$url = str_replace ( 'ACCESS_TOKEN', $accessToken, $config ['wx_url'] ['mass_preview'] );
		$r ['towxname'] = $in ['user_id'];
		$r ['touser'] = $r ['towxname']; // 防止用户没有设置过自己的微信号
		switch ($data ['mass'] ['msg_type']) {
			case '文本' :
				$r ['msgtype'] = 'text';
				$r ['text'] ['content'] = $data ['mass'] ['text'];
				break;
			case '图片' :
				$r ['msgtype'] = 'image';
				$r ['image'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '音频' :
				$r ['msgtype'] = 'voice';
				$r ['voice'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '视频' :
				$r ['msgtype'] = 'mpvideo';
				$r ['mpvideo'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '图文' :
				$r ['msgtype'] = 'mpnews';
				$r ['mpnews'] ['media_id'] = $data ['mass'] ['media_id'];
				break;
			case '卡券' :
				$r ['msgtype'] = 'wxcard';
				$r ['wxcard'] ['card_id'] = $data ['mass'] ['media_id'];
				break;
		}
		// echo json_encode ( $r );
		// exit ();
		$res = $comm->doCurl ( $url, json_encode ( $r ) );
		$res = $comm->wxErrCode ( $res );
		return $res;
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
		$data ['mass'] = array ();
		return $this->view ( './editor', $data );
	}
	
	/**
	 * toEditor 跳转到编辑视图：查询数据，
	 * 并返回渲染后的视图
	 *
	 * @param number $id 菜单id
	 * @return string 视图
	 */
	public function toEditor($id = 0) {
		$res = $this->find ( $id );
		if (empty ( $res ['errCode'] )) {
			$data = $this->data;
			$data ['editor_type'] = 'update';
			$data ['panel'] = 3;
			$data ['is_do'] = 0;
			$data ['action'] = 'editor';
			$msg_type = isset ( $res ['data'] ['msg_type'] ) ? $res ['data']->getData ( 'msg_type' ) : 'text';
			$mass = $res ['data']->toArray ();
			$mass ['msg_type'] = $msg_type;
			$mass = $this->getMassContent ( [ 
					$mass 
			] ) [0];
			$data = array_merge ( $data, $mass );
			$data ['reply_content'] = $this->fetch ( 'common@./replyContent', $data );
			
			return $this->view ( './vIndex', $data );
		} else {
			$this->error ( '获取图文失败：' . $res ['errMsg'] );
		}
	}
	/**
	 * myIterator
	 * @method 迭代器
	 * @todo 将验证器返回的错误信息 格式化：带上特定html标记。以便显示
	 *
	 * @param array $r 验证器返回的错误信息
	 * @return array 结果数据
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
	 * @method 数据库查询
	 *
	 * @param number $id 数据id
	 * @return mixed[] 查询结果数据
	 */
	private function find($id = 0) {
		if (empty ( $id )) {
			return [ 
					'errCode' => 401,
					'errMsg' => '图文id错误' 
			];
		} else {
			$res = WxMass::get ( $id );
			// $msg_type=$res->getData('msg_type');
			// $res = $res->toArray ();
			// $res['msg_type']=$msg_type;
			// $res['mass']=json_decode($res['mass'],1);
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
	 * @method 获取多组数组中同角标的数据
	 * @todo 并返回一个新数组
	 *
	 * @param number $s 角标
	 * @return array 数据
	 */
	protected function getTheData($s = 0) {
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
	 * oneDelete
	 * @method 单个删除
	 * @todo 也用于批量删除中的循环
	 *
	 * @param array $in 表单传入的数据
	 * @param number $s 位置/角标
	 * @return mixed[] 操作结果
	 */
	private function oneDelete($in = [], $s = 0) {
		$r = $this->getTheData ( $s );
		$WxMass = new WxMass ();
		$result = $WxMass->get ( $r ['id'] )->delete ();
		if ($result === 1) {
			return [ 
					'errCode' => 0,
					'errMsg' => '数据库读写成功' 
			];
		} else {
			return [ 
					'errCode' => 404,
					'errMsg' => '数据库读写失败' 
			];
		}
	}
	/**
	 * sDelete
	 * @method 批量删除
	 *
	 * @param array $in
	 * @param array $data
	 * @return mixed[] 操作结果
	 */
	private function sDelete($in = [], $data = []) {
		$error = $success = 0;
		if (! isset ( $in ['ids'] )) {
			return [ 
					'errCode' => 403,
					'errMsg' => '未选中任何群发' 
			];
		}
		foreach ( $in ['ids'] as $key => $val ) {
			$res = $this->oneDelete ( $in, $val - 1 );
			if ($res ['errCode']) {
				$error ++;
				$err_id [] = $in ['id'] [$key];
			} else {
				$success ++;
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
					'errMsg' => '未选中任何群发' 
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
	 * @method 渲染视图模板
	 *
	 * @param String $temp 参数地址
	 * @param array $data 数据
	 * @return string HTML代码
	 */
	private function view($temp, $data) {
		$head = $this->fetch ( 'common@./head', $data );
		$foot = $this->fetch ( 'common@./foot', $data );
		return $head . $this->fetch ( $temp, $data ) . $foot;
	}
	/**
	 * getMassContent
	 * @method 获取群发的具体内容
	 * @param array $list 二维数组
	 * @return array 二维数组；群发内容数据
	 */
	private function getMassContent($list = []) {
		if (is_array ( $list )) {
			$comm = $this->comm;
			foreach ( $list as $k => $v ) {
				if ($v ['msg_type'] == '文本' || $v ['msg_type'] == 'text') {
				} elseif ($v ['msg_type'] == '图片' || $v ['msg_type'] == 'img') {
					$WxFile = new WxFile ();
					$r = $WxFile->get ( $v ['img'] );
					if (empty ( $r )) {
						$v ['img'] = [ 
								'path' => '',
								'name' => '' 
						];
					} else {
						$v ['img'] = $r->toArray ();
						$v ['img'] ['img_title'] = $comm->my_substr ( $v ['img'] ['title'], 0, 10 );
						$v ['img'] ['img_url'] = $v ['img'] ['path'] . '/' . $v ['img'] ['name'];
					}
				} elseif ($v ['msg_type'] == '音频' || $v ['msg_type'] == 'voice') {
					$WxFile = new WxFile ();
					$r = $WxFile->get ( $v ['voice'] );
					if (empty ( $r )) {
						$v ['voice'] = [ 
								'path' => '',
								'name' => '' 
						];
					} else {
						$v ['voice'] = $r->toArray ();
						$v ['voice'] ['voice_title'] = $comm->my_substr ( $v ['voice'] ['title'], 0, 10 );
						$v ['voice'] ['voice_url'] = $v ['voice'] ['path'] . '/' . $v ['voice'] ['name'];
					}
				} elseif ($v ['msg_type'] == '视频' || $v ['msg_type'] == 'video') {
					$WxFile = new WxFile ();
					$r = $WxFile->get ( $v ['video'] );
					if (empty ( $r )) {
						$v ['video'] = [ 
								'path' => '',
								'name' => '' 
						];
					} else {
						$v ['video'] = $r->toArray ();
						$v ['video'] ['video_title'] = $comm->my_substr ( $v ['video'] ['title'], 0, 10 );
						$v ['video'] ['video_url'] = $v ['video'] ['path'] . '/' . $v ['video'] ['name'];
					}
				} elseif ($v ['msg_type'] == '图文' || $v ['msg_type'] == 'news') {
					$news = $v ['news'];
					$News = new \app\news\controller\Index ();
					foreach ( $v ['news'] as $i => $j ) {
						$res = $News->getTheNews ( $j );
						if (! $res ['errCode'])
							$news [$i] = $res ['data'];
					}
					$v ['news'] = $news;
				}
				$list [$k] = $v;
			}
			return $list;
		} else {
			return [ ];
		}
	}
}