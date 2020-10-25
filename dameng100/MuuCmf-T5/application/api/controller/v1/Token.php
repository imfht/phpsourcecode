<?php
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use Firebase\JWT\JWT;
use app\api\controller\Api;
use app\api\controller\UnauthorizedException;
use app\api\controller\v1\Base;

/**************************************************/
/* token 机制
/* 接口访问权限token
/* 用户授权token
/**************************************************/
class Token extends Controller
{
    //请求验证规则
    public static $rule_request = [
        'app_key'     =>  'require',
        'nonce'       =>  'require',
        'timestamp'   =>  'require',
    ];
    
    /**
     * 构造函数
     * 初始化检测请求时间，签名等
     */
    public function __construct()
    {
        $this->request = Request::instance();
        //为了调试注释掉时间验证与签名验证，请开发者自行测试
        //$this->checkTime();
        //$this->checkSign();
    }

    public function index()
    {
    	
    }

	/**
	 * 检测时间+_300秒内请求会异常
	 */
	public function checkTime()
	{
		$time = $this->request->param('timestamp');
		if($time > time()+300  || $time < time()-300){
			return $this->returnmsg(401,'The requested time is incorrect');
		}
	}

	/**
	 * 检测appkey的有效性
	 * @param 验证规则数组
	 */
	public function checkAppkey($rule)
	{
		$result = $this->validate($this->request->param(),$rule);
		if(true !== $result){
			return $this->returnmsg(405,$result);
		}
        //====调用模型验证app_key是否正确，这里注释，请开发者自行建表======
		// $result = Oauth::get(function($query){
		// 	$query->where('app_key', $this->request->param('app_key'));
		// 	$query->where('expires_in','>' ,time());
		// });
		if(empty($result)){
			return $this->returnmsg(401,'App_key does not exist or has expired. Please contact management');
		}
	}

	/**
	 * 检查签名
	 */
	public function checkSign()
	{	
		
	}

}