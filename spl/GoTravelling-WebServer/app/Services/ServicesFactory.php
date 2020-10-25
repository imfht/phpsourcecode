<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 15-4-20
 * Time: 下午4:51
 */
namespace App\Services;

use App;
use Illuminate\Http\Request;

class ServicesFactory
{
    /**
     * 服务命名空间，采用Laravel的默认命名空间
     *
     * @var string
     */
    protected $servicesPath = 'App\Services';

    /**
     * 根据参数，获取个人中心模块的响应数据
     *
     * 备注：
     * 1、服务的存放目录：服务统一放在 'App/Services' 的对应模块的目录下：如 'App/Services/personal'
     * 2、服务的命名：类型+名称+'Services'，示例：WebResponseServices
     * 3、服务的类型：主要用于针对不同请求端采用不同的响应处理
     * 4、服务的名字：考虑用于扩展
     *
     * @param Request $request 
     * @param string name 服务的名字
     * @param string $type 服务的类型
     * @return mixed 返回一个服务
     */
    public function getPersonalService(Request $request, $name, $type = 'Web')
    {
        $controllerName = '\personal';

        // 判断是不是移动端的请求
        if ( $request->wantsJson() ) {
            $type = 'App';
        }

        // 生成对应的服务
        return App::make($this->servicesPath. $controllerName. '\\'. $type. $name. 'Services');
    }
}

