<?php
/**
 * 测试实例
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/4/4
 * Time: 17:04
 */

namespace app\Controllers;

use Bjask\Controller;

class TestController extends Controller
{
    public function indexAction()
    {
        $extras = $this->getExtras();
        //$configs = $this->getConfig();
        $this->log('任务处理:'.var_export($extras,true));
       // $em = $this->getDoctrine()->getManager();
       // $article = $em->find('app\Models\Entities\Article', 2);
        //var_dump($article);
    }
}