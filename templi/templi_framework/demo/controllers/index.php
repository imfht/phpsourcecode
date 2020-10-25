<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14-5-9
 * Time: 下午3:19
 */
namespace demo\controllers;
use Templi;
class index extends \framework\web\Controller
{
    public function beforeIndex()
    {
        echo __FUNCTION__, '<br/>';
        print_r(\Templi::getApp()->router);
    }
    public function actionIndex()
    {
        echo __FUNCTION__, '<br/>';
//        var_dump(Templi::getApp()->request);
//        var_dump(Templi::getApp());
        $a = Templi::getApp()->request->post();
        print_r($a);
//        $this->display('index_index', '');
        $this->display();
//        $this->render('index_index.html');
    }
    public function afterIndex()
    {
        echo '<br/>',__FUNCTION__, '<br/>';

    }
    public function actionDetail($id)
    {
        echo $id;
        $this->display('index_index');
    }
}