<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/1/29
 * Time: 16:46
 */
class NotifyTypeController extends \BaseController
{
  /**
   * 获取通知类型表的所有数据
   *
   * 返回的JSON格式如下
   *
   * [
   *    {
   *      'id': 通知类型的id
   *      'name': 通知类型的名称
   *      'label': 通知类型的标签
   *      'map': 通知类型所关联的模型
   *    }
   * ]
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    return Response::json(NotifyType::all());
  }
}