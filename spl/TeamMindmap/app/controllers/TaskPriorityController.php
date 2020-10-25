<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-23
 * Time: 下午5:12
 */

/**
 * Class TaskPriorityController
 *
 * 此控制器用于获取项目-任务的优先级的相关信息
 */
class TaskPriorityController extends \BaseController
{
    /**
     * 返回所有可用的任务优先级列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Response::json( ProjectTaskPriority::all() );
    }

    /**
     * 通过id查询某一任务优先级的具体信息
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return Response::json( ProjectTaskPriority::findOrFail($id) );
    }


}