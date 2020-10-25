<?php
/**
 * @package     Index.php
 * @author      Jing Tang <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net/
 * @version     2.0
 * @copyright   Copyright (c) http://www.slimphp.net
 * @date        2017年7月10日
 */

namespace Demo\Interfaces\Admin;

/**
 * Index
 * 
 * @author Jing Tang <tangjing3321@gmail.com>
 */
interface Index
{
    /**
     * @api {get} /admin/index/{id} index
     * @apiName index
     * @apiGroup Index
     * @apiVersion 1.0.0
     * @apiDescription 缓存，模型，session等调用示例
     *
     * @apiParam {int} [page=1] 分页
     *
     * @apiSuccess {String} code 结果码
     * @apiSuccess {String} msg 消息说明
     * @apiSuccess {Object} result 数据封装
     * @apiSuccess {Object} result.page 分页信息
     * @apiSuccess {Array[]} result.data 数据集合
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     "code": 0,
     *     "msg": "success",
     *     "result": [
     *         {
     *             "id": 7,
     *             "configs": {
     *                 "basic": {
     *                     "message_audited": 1,
     *                     "online_person_count": "999"
     *                 },
     *                 "send": [
     *                     {
     *                         "id": "e563c138b872a47cc4d9b03e14cc23dc",
     *                         "title": "123",
     *                         "url": "123"
     *                     }
     *                 ]
     *             },
     *             "create_user_id": "61",
     *             "update_user_id": "61",
     *             "update_time": "1491965945",
     *             "create_time": "1491963536",
     *             "ip": "218.2.102.114",
     *             "site_id": 10040,
     *             "disable": 0,
     *             "original_content_id": "gh_fd215fe3ed12"
     *         },
     *         {
     *             "id": 8,
     *             "configs": {
     *                 "basic": {
     *                     "message_audited": 0
     *                 },
     *                 "send": []
     *             },
     *             "create_user_id": "48",
     *             "update_user_id": "48",
     *             "update_time": "1494499924",
     *             "create_time": "1494494617",
     *             "ip": "218.2.102.114",
     *             "site_id": 18,
     *             "disable": 0,
     *             "original_content_id": "4"
     *         },
     *         {
     *             "id": 9,
     *             "configs": {
     *                 "basic": {
     *                     "message_audited": 0
     *                 },
     *                 "send": []
     *             },
     *             "create_user_id": "48",
     *             "update_user_id": "48",
     *             "update_time": "1494499594",
     *             "create_time": "1494499573",
     *             "ip": "218.2.102.114",
     *             "site_id": 18,
     *             "disable": 0,
     *             "original_content_id": "454822"
     *         },
     *         {
     *             "id": 10,
     *             "configs": {
     *                 "basic": {
     *                     "message_audited": 0
     *                 },
     *                 "send": []
     *             },
     *             "create_user_id": "48",
     *             "update_user_id": "48",
     *             "update_time": "1494572271",
     *             "create_time": "1494572271",
     *             "ip": "218.2.102.114",
     *             "site_id": 18,
     *             "disable": 0,
     *             "original_content_id": "456739"
     *         },
     *         {
     *             "id": 11,
     *             "configs": {
     *                 "basic": {
     *                     "message_audited": 0
     *                 },
     *                 "send": []
     *             },
     *             "create_user_id": "48",
     *             "update_user_id": "48",
     *             "update_time": "1494576993",
     *             "create_time": "1494576993",
     *             "ip": "218.2.102.114",
     *             "site_id": 18,
     *             "disable": 0,
     *             "original_content_id": "5"
     *         }
     *     ]
     * }
     * 
     */
    public function index(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args);
    public function renderer(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args);
}