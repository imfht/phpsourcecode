<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/3/26 10:01
// +----------------------------------------------------------------------
// | TITLE: 简单的Oauth客户端模式
// +----------------------------------------------------------------------

namespace app\first\auth;

use app\first\model\Member;
use DawnApi\auth\OAuth;
use DawnApi\exception\UnauthorizedException;
use think\facade\Cache;
use think\Request;
use think\exception;

class OauthAuth extends OAuth
{
    /**
     * 客户端获取access_token
     * @param Request $request
     * @return \think\facade\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     */
    public function accessToken(Request $request)
    {
        //获客户端信息
        try {
            $this->getClient($request);
        } catch (UnauthorizedException $e) {
            //错误则返回给客户端
            return $this->sendError(401, $e->getMessage(), 401, [], $e->getHeaders());
        } catch (\Exception $e) {
            return $this->sendError(500, $e->getMessage(), 500);
        }
        //校验信息
        if ($this->getClientInfo($this->client_id)->checkSecret()) {
            //通过下放令牌
            $access_token = $this->setAccessToken($this->clientInfo);
        } else {
            return $this->sendError(401, lang('authentication'), 401, [], ['WWW-Authenticate' => 'Basic']);
        }
        return $this->sendSuccess([], 'success', 200, [], [
            'access_token' => $access_token, //访问令牌
            'expires' => self::$expires,      //过期时间秒数
        ]);
    }

    /**
     * 校验密码
     * @return bool
     */
    public function checkSecret()
    {
        if ($this->secret == $this->clientInfo['secret']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息后 验证权限
     * @return mixed
     */
    public function certification()
    {
        if ($this->getAccessTokenInfo($this->access_token) == false) {
            return false;
        } else {
            return true;
        }
    }

    protected function getAccessTokenInfo($accessToken)
    {
        $keys = self::$accessTokenPrefix . $accessToken;
        $info = Cache::store('access_token')->get($keys);
        if ($info == false || $info['expires_time'] < time()) return false;
        //验证索引是否正确
        $client_id = $info['client']['client_id'];
        if ($this->getAccessTokenAndClient($client_id) != $accessToken) return false;
        $this->clientInfo = $info['client'];
        return $info;
    }

    public function getClient(Request $request)
    {
        //先行验证是否有传参
        $this->client_id = $request->get('client_id');
        $this->secret = trim_all($request->get('secret'));
        if ($this->client_id && $this->secret)  return $this;
        //没有再获取
        try {
            $authorization = $request->header('authorization');

            if($authorization){
                $authorization = str_replace("Basic ", "", $authorization);
                $authorization = explode(':', base64_decode($authorization));
                $username = $authorization[0];//$_SERVER['PHP_AUTH_USER']
                $secret = $authorization[1];//$_SERVER['PHP_AUTH_PW']
                $this->client_id = $username;
                $this->secret = $secret;
            }else{
                $access_token = $request->get('access_token/s');
                $info = self::getClientInfoByAccessToken($access_token);
                if(!$info){
                    self::sendError(0,lang('access_token_expires'));
                }
                $data =[
                    'status'=>1,
                    'message'=>lang('success',[lang('query')]),
                    'user'=>Member::getMember($info['client']['id'])
                ];
                return self::sendSuccess($data);
            }
        } catch (Exception $e) {
            throw new UnauthorizedException();
        }
        return $this;
    }
    /**
     *获取AccessToken
     * @param $client_id
     * @return mixed
     */
    protected function getAccessTokenAndClient($client_id)
    {
        return Cache::store('access_token')->get(self::$accessTokenAndClientPrefix . $client_id);
    }
    /**
     * 返回用户信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserInfo()
    {
        if(!$this->client_id){
            $this->sendError(0,lang('error',lang('client_id')));
        }
        if(!$this->secret){
            $this->sendError(0,lang('error',lang('secret')));
        }
        $member = new Member;
        $info = $member::where(['client_id'=>$this->client_id,'secret'=>$this->secret])
            ->field('id,client_id,secret')
            ->find();
        if(!$info){
            $this->sendError(0,lang('unalready',[lang('client')]));
        }
        return $info->toArray();
    }

    /**
     * 获取客户端所有信息
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getClientInfo()
    {
        $this->clientInfo = $this->getUserInfo();
        return $this;
    }

    /**
     * 设置AccessToken
     * @param $clientInfo
     * @return int
     */
    protected function setAccessToken($clientInfo)
    {
        //生成令牌
        $accessToken = self::buildAccessToken();
        $accessTokenInfo = [
            'access_token' => $accessToken,//访问令牌
            'expires_time' => time() + self::$expires,      //过期时间时间戳
            'client' => $clientInfo,//用户信息
        ];
        self::saveAccessToken($accessToken, $accessTokenInfo);
        return $accessToken;
    }

    /**
     * 生成AccessToken
     * @return string
     */
    protected static function buildAccessToken()
    {
        $random = new \Rych\Random\Random();
        $text = $random->getRandomString(32,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        //$secret =  CryptoJSAES::encrypt($text, config('api.passphrase'));
        return $text;
    }


    /**
     * 存储
     * @param $accessToken
     * @param $accessTokenInfo
     */
    protected static function saveAccessToken($accessToken, $accessTokenInfo)
    {
        //存储accessToken
        Cache::store('access_token')
            ->set(self::$accessTokenPrefix.$accessToken,$accessTokenInfo, self::$expires);
        //存储用户与信息索引 用于比较
        Cache::store('access_token')
            ->set(self::$accessTokenAndClientPrefix . $accessTokenInfo['client']['client_id'],$accessToken, self::$expires);
    }

    /**
     * 根据accessToken获取Client信息
     * @param $accessToken
     * @return mixed
     */
    protected static function getClientInfoByAccessToken($accessToken){
        $info =  Cache::store('access_token')->get(self::$accessTokenPrefix.$accessToken);
        return $info;
    }
    /**
     * 获取用户信息
     * @return bool
     */
    public function getUser()
    {
        $info = $this->getAccessTokenInfo($this->access_token);
        if ($info) {
            $this->client_id = $info['client']['client_id'];
            $this->user = $info['client'];
            return $this->user;
        } else {
            return false;
        }
    }
}