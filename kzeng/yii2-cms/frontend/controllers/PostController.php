<?php

namespace frontend\controllers;

use yeesoft\post\models\Post;
use yeesoft\post\models\Tag;
use Yii;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class PostController extends \yeesoft\controllers\BaseController
{
    public $layout = 'hss_whj';
    public $freeAccess = true;

    public function actionIndex($id)
    {
        $post = Post::find()->where([
            'status' => Post::STATUS_PUBLISHED,
            'id' => $id,
        ])->one();

        return $this->render('index', [
            'post' => $post,
        ]);
    }
}
