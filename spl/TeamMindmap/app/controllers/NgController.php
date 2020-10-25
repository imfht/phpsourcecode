<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-17
 * Time: 上午9:27
 */

/**
 * Class NgController
 *
 * 此控制器用于输出ngApp的初始化页面，并将有关的初始化数据以Cookie的形式实现传递.
 *
 * 也会负责一些全局性的查询.
 *
 */
class NgController extends \BaseController
{
    /**
     * 返回ngApp初始化页面
     *
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        if( ! App::environment('testing') ) {
            setcookie('loginData', json_encode( $this->buildLoginData() ), time() + 60, '/');
        }

        return View::make('ng');
    }

    /**
     * 返回未读的通知或私信的统计数据, 注意仅可通过Ａjax访问.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * 返回的JSON格式：
     * {
     *   notification: "未读的通知的数目",
     *   message: "未读的私信的数据"
     * }
     */
    public function getUnread()
    {
        if( Request::wantsJson() ){
            return Response::json( $this->getUnreadStatistics() );
        }else{
            return Redirect::to('/');
        }

    }

    /**
     * 生成ngApp所需偶要的初始化数据
     *
     * @return array
     */
    protected function buildLoginData()
    {
        $loginData = [
            'personalInfo'=> Auth::user()->toArray(),
            'uri'=>['login'=>URL::to('/authority/login'), 'logout'=>URL::to('/authority/logout')],
            'unread'=> $this->getUnreadStatistics()
        ];

        return $loginData;
    }

    /**
     * 返回未读的通知或私信的统计数据
     *
     * @return array
     */
    protected function getUnreadStatistics()
    {
        $currUserId = Auth::user()['id'];

        return [
            'notification'=> NotifyInbox::getUnreadStatistics($currUserId),
            'message'=>MessageInbox::getUnreadStatistics($currUserId)
        ];
    }

}