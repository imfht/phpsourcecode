<?php

namespace app\api\controller\v1;

/**
 * Class AuthEsac AuthEsac授权类
 * @package app\api\controller\v1
 */
class AuthEsac extends Base
{

    /**
     * @doc 获取服务器授权1
     * @route /api/v1/authEsac get
     * @param string $appSecret 授权字符 require|alphaNum
     * @param string $appSec2t 授权字符1 require|alphaNum
     * @param string $appId 开发者ID
     * @success {"code":400,"msg":"appSecret不能为空","data":[]}
     * @error {"code":400,"msg":"appSecret不能为空","data":[]}
     */
    public function read()
    {
        return $this->success('成功~');
    }


}
