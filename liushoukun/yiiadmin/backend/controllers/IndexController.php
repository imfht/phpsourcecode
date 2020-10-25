<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/30 23:02
// +----------------------------------------------------------------------
// | TITLE:后台首页
// +----------------------------------------------------------------------

namespace backend\controllers;


use backend\helps\Tree;
use Yii;
use backend\models\AdminRole;
use backend\models\TestForm;
use Symfony\Component\VarDumper\Tests\Fixture\DumbFoo;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class IndexController
 * @package backend\controllers
 */
class IndexController extends BaseController
{

    public function actionIndex()
    {
        return $this->render('index',['menu'=>$this->menuHtml]);
    }

    public function actionMain()
    {

        $mysql = Yii::$app->db->createCommand("select VERSION() as version")->queryAll();
        $mysql=$mysql[0]['version'];
        $info =
            [
                '操作系统'=>PHP_OS,
                '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
                'PHP运行方式'=>php_sapi_name(),
                'PHP版本'=>PHP_VERSION,
                'MYSQL版本'=>$mysql,
                '上传附件限制'=>ini_get('upload_max_filesize'),
                '执行时间限制'=>ini_get('max_execution_time').' s',
                '剩余空间'=>round((@disk_free_space(".") / (1024 * 1024)), 2).' M',

            ];
        return $this->render('main',['info'=>$info]);
    }




}