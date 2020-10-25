<?php
/**
 * Class NotifyController
 * 通知控制器,处理业务包含
 *     获取用户通知列表清单
 *     查看某一通知详细信息
 *     更改某一通知状态
 *     删除某一通知
 * 该控制器处理通知接收方相关业务,
 * 创建通知业务已委托事件处理.
 */

class NotifyController extends \BaseController
{

    /**
     * Display a listing of the resource.
     * 返回用户接收的通知消息清单.
     * 返回的JSON格式:
     * [
     *   {
     *     "id": "主键id",
     *     "type_id": "类型id",
     *     "title": "通知标题",
     *     "content": "通知的内容",
     *     "created_at": "创建时间",
     *     "source_id": "通知源id",     //如项目 A 被更改，则通知源为项目 A
     *     "trigger_id": "通知触发者id"  //如用户 1 更改了项目 A ,则通知触发者为用户 1,
     *     "trigger": {
     *          //trigger的具体信息
     *          "id": "用户id",
     *          "username": "用户名",
     *          "head_image": "用户头像",
     *          "email": "用户电子邮件地址",
     *          "description": "用户描述",
     *      },
     *   },
     *   ......
     * ]
     * @return Response
     */
    public function index()
    {

        $project_id = Input::get('project_id', null);
        $read = Input::get('read', 0);
        $user_id = Auth::user()['id'];

        if ( $project_id ){
            $notifyData = Notification::getNotifyBelongsToProject($user_id, $project_id, $read);
        } else {
            $notifyData = Notification::getUserNotify($user_id, $read);
        }

        return Response::json($notifyData, 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Store a newly created resource in storage.
     * 创建通知消息,该操作已委托通知事件处理,这里不做处理,
     * 关于通知消息创建,查看通知事件处理模块.
     * @return Response
     */
    public function store()
    {
        //
    }


    /**
     * Display the specified resource.
     * 获取某一通知详细信息,返回JSON格式:
     * {
     *   "id": "主键id",
     *   "type_id": "类型id",
     *   "title": "通知标题",
     *   "content": "通知的内容"
     *   "created_at": "创建时间"
     *   "read": "是否已阅读",
     *   "remark": // 扩展型字段，具体内容跟通知类型有关,如下
     * }
     * --------------------------------------------
     * 项目改动通知的remark字段
     * {
     *   "project": {　　　　　// 通知源
     *     "id": "项目id",
     *     "name": "项目名称"
     *   },
     *   "operator": {　　　　 //通知触发者
     *     "id":"用户id",
     *     "username": "用户名",
     *     "email": "用户电子邮箱"
     *   }
     * }
     * ---------------------------------------------
     * 任务改动通知的remark字段
     * {
     *   "task":{　　　　　　　　// 通知原
     *     "id": "任务id",
     *     "name": "任务名称"
     *   },
     *   "operator": {           // 通知触发者
     *     "id":"用户id",
     *     "username": "用户名",
     *     "email": "用户电子邮箱"
     *   }
     * }
     * ---------------------------------------------
     *系统通知的remark字段
     *暂无
     * ---------------------------------------------
     * 通知的remark字段
     * {
     *   "project_sharing":{　　　　　　　　// 通知源
     *     "id": "分享id",
     *     "name": "分享名称"
     *   },
     *   "operator": {           // 通知触发者
     *     "id":"用户id",
     *     "username": "用户名",
     *     "email": "用户电子邮箱"
     *   }
     * }
     *
	   * @param  int  $id
	   * @return Response
	   */
    public function show($id)
    {
        $currNotify = Notification::with('trigger')->findOrFail($id);
        NotifyInbox::readNotify(Auth::user()->id, $id);
        $resp = $currNotify->toArray();
        $resp['read'] = true;
        $resp['remark'] = $currNotify->getSourceData();
        return Response::json($resp, 200, [], JSON_NUMERIC_CHECK);
    }


    /**
     * Update the specified resource in storage.
     * 更改用户通知接收清单中某一通知的状态,这里的更改表现为通知是否已读
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {

	    //只能修改read字段
        $status = 200;
        $check = NotifyInbox::readNotify(Auth::user()->id, $id);

        if ( !$check ) {
            $status = 500;
        }

        return Response::make('', $status);
    }


    /**
     * Remove the specified resource from storage.
     * 删除用户通知接收清单中某一通知
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $status = 200;
        $rows = NotifyInbox::deleteNotify(Auth::user()->id, $id);

        if ( !$rows ) {
            $status = 500;
        }

        return Response::make('', $status);
    }

}

