<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-31 07:35:46
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-31 08:07:11
 */


namespace backend\controllers\demo;

use Yii;
use common\models\DdAiApplications;
use common\models\searchs\DdAiApplicationsSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\controllers\BaseController;
use diandi\region\Region;
use common\models\DdMember;
use common\models\DdRegion;

/**
 * DdAiApplicationsController implements the CRUD actions for DdAiApplications model.
 */
class FormController extends BaseController
{

    public function actions()
    {
        $actions = parent::actions();
        $actions['get-region'] = [
            'class' => \diandi\region\RegionAction::className(),
            'model' => DdRegion::className()
        ];
        return $actions;
    }

    public function actionIndex()
    {
        $model = new DdMember();
        $DdRegion = new DdRegion();
        return $this->render('index', [
            'model' => $model,
            'DdRegion' => $DdRegion,
        ]);
    }

    public function actionMaile()
    {
        // 不使用模板
        $mail = Yii::$app->mailer->compose();
        $mail->setTo('2192138785@qq.com');
        $mail->setSubject('邮件的标题');
        $mail->setHtmlBody('邮件内容，这里可以使用 HTML 代码具体发送的内容');
        $mail->send(); //发送
        //你也可以在 compose() 方法中传递一些视图所需参数，这些参数可以在视图文件中使用
        //     Yii::$app->mailer->compose('模板文件名称',['key' => $value])
        //     ->setFrom('from@domain.com')
        // 　　->setTo('to@domain.com') 
        // 　　 ->setSubject('Message subject') 
        // 　　 ->setTextBody('Plain text content')
        // 　　 ->setHtmlBody('<b>HTML content</b>')
        // 　　 ->send();
        //compose 与控制器中的 render 方法参数方式相同.
        return $this->render('index', [
            // 'model' => $model,
            // 'DdRegion' => $DdRegion,
        ]);
    }
}
