<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/10/14
 * Time: 上午9:53
 */

namespace LuciferP\Router\Controller;




use LuciferP\Controller\Controller;

class Home extends Controller
{

    /**
     *  只渲染包含layout 的 html
     * @return string
     */
    public function index(){

        return $this->render(['name' => 'zhangsan', 'age' => 20]);
    }

    /**
     * 调用response渲染数据
     *
     * @throws \Exception
     */
    public function index2(){
        $this->response->status(200)->type('text/html')->render(BASE_PATH . "/views/view.php", ['name' => 'zhangsan', 'age' => 20]);

    }

    /**
     * 把包含在layout 的 html一起渲染的数据交给response返回
     */
    public function index3(){

        $ret = $this->render(['name'=>'zhangsan']);
        $this->response->status(403)->type('text/html')->send($ret);
    }







}