<?php

namespace app\api\controller\v1;

/**
 * Class Auth Auth授权类
 * @package app\api\controller\v1
 */
class Auth extends Base
{

    /**
     * @doc 获取服务器授权1
     * @route /api/v1/auth get
     * @param string $appSecret 授权字符 require|alphaNum 1
     * @param string $appSec2t 授权字符1 require|alphaNum 1
     * @param string $appId 开发者ID
     * @success {"code":400,"msg":"appSecret不能为空","data":[]}
     * @error {"code":400,"msg":"appSecret不能为空","data":[]}
     */
    public function read()
    {
        return $this->success('成功~',$this->param);
    }

    /**
     * @doc 获取服务器授权2
     * @route /api/v1/auth/read1 get
     * @param string $appSecret 授权字符 require|alphaNum 1
     * @param string $appSec2t 授权字符1 require|alphaNum 1
     * @param string $appId 开发者ID
     * @success {"code":400,"msg":"appSecret不能为空","data":[]}
     * @error {"code":400,"msg":"appSecret不能为空","data":[]}
     */
    public function read1()
    {
        return $this->success('token', []);
    }


    /**
     * @doc 获取服务器授权3
     * @route /api/v1/auth/read2 get
     * @param string $appSecret 授权字符 require|alphaNum 1
     * @param string $appSec2t 授权字符1 require|alphaNum 1
     * @param string $appId 开发者ID
     * @success {"code":400,"msg":"appSecret不能为空","data":[]}
     * @error {"code":400,"msg":"appSecret不能为空","data":[]}
     */
    public function read2()
    {
        return $this->error('sss');
    }

    /**
     * @doc 获取服务器授权1
     * @route /api/v1/auth/read3 get
     * @param string $appSecret 授权字符 require|alphaNum 1
     * @param string $appSec2t 授权字符1 require|alphaNum 1
     * @param string $appId 开发者ID
     * @success {"code":400,"msg":"appSecret不能为空","data":[]}
     * @error {"code":400,"msg":"appSecret不能为空","data":[]}
     */
    public function read3()
    {
        return $this->error('sss');
    }

}
