<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\demo\controller\api\v1;
use app\demo\controller\api\Base;
use app\demo\model\DemoHello;

class Index extends Base{
    
    /**
     * 读取微信API
     * @param integer 读取ID
     * @return json
     */
    public function adwords(){
        $param['id']        = $this->request->param('id'); //
        $param['publickey'] = $this->request->param('publickey'); //已在签名中使用,不用重复传参
        $param['sign']      = $this->request->param('sign'); //已在签名中使用,不用重复传参
        //签名验证
        $this->apiSign($param); //$param 没有参数可留空
        /**
         * 第一个参数：返回的状态吗
         * 第二个参数：返回的提示文字,可不填写,第三方参数,返回的数组或对象直接用在第二个上
         * 第三个参数：,返回的数组或对象
         * enjson(200,'成功',$data) / enjson(200,$data)
         */
        return enjson(200,'成功',$data); //enjson(200,$data)
    }
}