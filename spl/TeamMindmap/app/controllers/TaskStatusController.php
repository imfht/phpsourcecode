<?php

class TaskStatusController extends \BaseController
{
    /**
     * 返回任务的状态类型组成的列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Response::json( ProjectTaskStatus::all() );
    }

    /**
     * 通过id查询某一状态的具体信息.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return Response::json( ProjectTaskStatus::findOrFail($id) );
    }

}
