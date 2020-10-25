<?php

namespace frontend\controllers;

use yeesoft\post\models\Post;
use Yii;
use yeesoft\post\models\Category;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

/**
 * Site controller
 */
class CategoryController extends \yeesoft\controllers\BaseController
{
    public $layout = 'hss_whj';
    public $freeAccess = true;

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex($slug = 'index')
    {
        if (empty($slug) || $slug == 'index') {
            throw new NotFoundHttpException('Page not found.');
        } else {
            $category = Category::find()->where(['slug' => $slug]);
            $categoryCount = clone $category;
            if (!$categoryCount->count()) {
                throw new NotFoundHttpException('Page not found.');
            }
        }

        $query = Post::find()->joinWith('category')->where([
            'status' => Post::STATUS_PUBLISHED,
            Category::tableName() . '.slug' => $slug,
        ])->orderBy('published_at DESC');
        $countQuery = clone $query;

        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'defaultPageSize' => Yii::$app->settings->get('reading.page_size', 10),
        ]);

        $posts = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $posts_tzgg = (new \yii\db\Query())
            ->select('post.id, post.published_at as published_at, post_lang.title as title')
            ->from('post')
            ->leftJoin('post_lang', 'post.id = post_lang.post_id')
            ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 6])
            ->orderBy('post.published_at DESC')
            ->limit(9)
            ->all();

        return $this->render('index', [
                'posts' => $posts,
                'posts_tzgg' => $posts_tzgg,
                
                'category' => $category->one(),
                'pagination' => $pagination,
        ]);
    }
}