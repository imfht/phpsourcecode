<?php
namespace app\api\controller;

use app\common\model\ApiApp;
use expand\ApiReturn;
use app\common\model\ApiApptoken;
use expand\Str;

// 获取ApiToken令牌

class GetAppToken extends Base {

    public function initialize() {
        parent::initialize();

	}

    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段数据
		$apptoken = request()->header('apptoken');
		$ApiApp = new ApiApp();
		$appInfo = $ApiApp->where(['app_id' => $data['app_id'], 'app_status' => 1])->find();
        if (empty($appInfo)) {
        	return ApiReturn::r('-2');	//应用AppID非法
        }
        if ( empty($appInfo['app_secret']) || $data['app_secret'] !== $appInfo['app_secret']) {
        	return ApiReturn::r('-900','','非法的app_secret');	//参数错误
        }
		$newAppToken = $this->buildAccessToken( $appInfo['app_id'], $appInfo['app_secret'] );
		$newTime = time() + $appInfo['app_limitTime'];
		$add_data = ['app_tokenTime'=>$newTime,'apptoken'=>$newAppToken,'app_id'=>$appInfo['app_id']];
		$apptoken = new ApiApptoken;
		$edit = $apptoken->allowField(true)->save($add_data);
		if( $edit ){
			$times =  time() - 43200;	//删除12小时前的 appToken
			$apptoken->where([ ['app_tokenTime','<',$times] ])->delete();
			$datas['expire'] = $appInfo['app_limitTime'];
			$datas['appToken'] = $newAppToken;
			return ApiReturn::r(1, $datas);
		}else{
			return ApiReturn::r(0);
		}
    }

    /**
     * 计算出唯一的身份令牌
     * @param $appId
     * @param $appSecret
     * @return string
     */
    private function buildAccessToken( $appId, $appSecret ){
        $preStr = $appSecret.$appId.time().Str::keyGen();
        return md5($preStr);
    }

}
