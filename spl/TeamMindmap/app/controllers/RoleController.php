<?php

/**
 * Class RoleController
 *
 * 此类用于查询角色有关的信息
 *
 * 此处使用RESTFUL资源路由机制.
 *
 */
class RoleController extends \BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Response::json( ProjectRole::all() );
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Response::json( ProjectRole::findOrFail($id) );
    }
}
