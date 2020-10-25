<?php 
namespace Weixin\Controller;
use Common\Controller\BaseController;
use Weixin\Api\WebpageApi;

/**
 * 响应来自微信客户端中点击链接产生的问卷页面请求
 */
class InterviewController extends BaseController
{
	protected $online = true; //false用于本地调试, true用于线上运行

	protected function _initialize()
	{
		if( !$this->online ){ //case: 本地调试
            session('openid', 'oPsprs0_mKYMscNyrnxzy2M3RxYI');
            session('user', array(
                'openid'    =>  'oPsprs0_mKYMscNyrnxzy2M3RxYI',
                'nickname'  =>  '麦沙',
            ));
            return; //本地调试直接跳出
        }

		$code = $this->_checkAuthorize();

		$this->loadSettings(); //加载系统配置信息到C('settings')中

		/* 请求网页授权令牌 */
		$api = new WebpageApi();
		$access_token = $api->getWebpageAccessToken($code);
		C('interview.access_token', $access_token);

		/* 拉取当前答题用户信息 */
		$userInfo = $api->pullUserinfo(session('openid'), $access_token);
		session('user', $userInfo);
	}

	/**
	 * 检查是否获得客户的网页授权(该问卷系统默认采用snsapi_userinfo的scope授权)
	 * @return string  成功则返回临时授权码，失败则进入TP异常
	 */
	private function _checkAuthorize()
	{
		$code = I('get.code');

		if( empty($code) ){
			E('该问卷应用需要你的授权才能运行！'); //抛出异常，终止程序
		}else{
			C('interview.code', $code); //记录临时授权码
			C('interview.state', I('get.state'));
			return $code;
		}
	}

	/**
	 * 问卷单页应用程序的输出
	 */
	public function index()
	{
		$this->assign('questionnaireID', I('get.questionnaireID'));

		$this->display();
	}

}
?>