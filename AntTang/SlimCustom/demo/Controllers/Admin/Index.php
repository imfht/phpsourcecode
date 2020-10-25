<?php
/**
 * @package     index.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月2日
 */

namespace Demo\Controllers\Admin;

use Demo\Models\MessageConfigs;
use SlimCustom\Libs\Paginator\Paginator;
use SlimCustom\Libs\Model\Query\PdoQuery as Query;
use SlimCustom\Libs\Controller\Api;
use Demo\Interfaces\Admin\Index as IndexInterface;
use SlimCustom\Libs\Exception\SlimCustomException;

/**
 * Controller Example
 *
 * @author Jing <tangjing3321@gmail.com>
 */
class Index extends \SlimCustom\Libs\Controller\Api implements IndexInterface
{

    /**
     * Model MessageConfigs
     *
     * @var \Demo\Models\MessageConfigs
     */
    protected $messageConfigs;

    /**
     * construct 依赖注入
     *
     * @param MessageConfigs $messageConfigs            
     */
    public function __construct(MessageConfigs $messageConfigs)
    {
        parent::__construct();
        $this->messageConfigs = $messageConfigs;
    }

    /**
     * Action Example
     *
     * @param Request $request            
     * @param Response $response            
     * @param array $args            
     */
    public function index(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args)
    {
        // Cache
        cache()->put('Tokens.timestamp', time(), 1);
        $timestamp = cache()->get('Tokens.timestamp', null);
        // var_dump($timestamp);die;
        
        // Session
        session()->set('User.user_id', 12345);
        // var_dump(session()->all());die;
        
        // Validator
        $validator = validator(request()->getParams(), [
            'key' => 'required|integer'
        ]);
        // var_dump($validator->messages());die;
        
        // Curl
        // $res = curl()->post('http://mxuapi-team.cloud.hoge.cn/api/tuji/detail/57?access_token=8925a79d6a0377211d0bdbc00a5734e')->response;
        // var_dump($res);die;
        
        // Model
        try {
            // mysql查询
            // 绑定闭包处理rows
            $closure = function (\SlimCustom\Libs\Support\Collection $row) {
                $this->configs = unserialize($this->configs);
                return $this;
            };
            $res = model('configs')->where('id', '<', 12)
                ->bind($closure)
                ->rows();
            // 插入
            $res = model('configs')->rules([
                'name' => 'required|string'
            ])->create($request->getParams());
            // 更新
            $res = model('configs')->rules([
                'id' => 'required|integer'
            ])->renew($request->getParams());
            // 删除
            $res = model('configs')->rules([
                'id' => 'required|integer'
            ], $request->getParams())
                ->where('id', '=', $request->getParam('id'))
                ->remove();
            // 静态方法连贯调用
            $res = MessageConfigs::where('id', '<', 12)->rows();
            // 使用注入对象
            $res = $this->messageConfigs->where('id', '<', 12)
                ->bind($closure)
                ->rows();
            // 使用query对象
            $res = MessageConfigs::query(function (Query $query) {
                // Sql
                $item = $query->select()
                    ->limit(Paginator::COUNT, intval(request()->getParam('page', 1)) * Paginator::COUNT - Paginator::COUNT)
                    ->execute()
                    ->fetchAll();
                // Page
                return new Paginator($item, Paginator::COUNT, request()->getParam('page', 1), [
                    'mode' => 'list',
                    'isAll' => request()->getParam('is_all', false)
                ]);
            });
            
            // Mongodb
            // 查询多个
            $res = model('runoob')->rows([
                    'create_user_id' => 1
            ]);
            // 查询单个
            $res = model('runoob')->row([
                    'create_user_id' => 1
            ]);
            // 创建
            $res = model('runoob')->create($data)->isAcknowledged();
            // 更新
            $res = model('runoob')->renew([
                '$set' => $data
            ], [
                'create_user_id' => 1
            ]);
            // 删除
            $res = model('runoob')->remove([
                'create_user_id' => 1
            ]);
            // 原生方法调用
            $res = model('runoob')->find([
                'site_id' => 1
            ])
                ->toArray()
                ->statementResolve();
            
            // Response
            return response()->success($res->toArray());
        }
        catch (SlimCustomException $e) {
            return response()->error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * Renderer Example
     *
     * @param \Slim\Http\Request $request            
     * @param \SlimCustom\Libs\Http\Response $response            
     * @param array $args            
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function renderer(\Slim\Http\Request $request, \SlimCustom\Libs\Http\Response $response, $args)
    {
        return renderer()->render(response(), 'index.phtml', $args);
    }
}