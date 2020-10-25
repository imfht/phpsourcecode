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
class CategoriesController extends \yeesoft\controllers\BaseController
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
        $category_slugs = [
            'whhs' => [
                'shwh',
                'whsc',
                'wwbw',
                'xwcb',
            ],
            'ddjs' => [
                'zdjs',
                'djdt',
            ],
        ];

        foreach ( $category_slugs[$slug] as $category_slug ) {
            $category[$category_slug] = Category::find()
                ->where([
                    'slug' => $category_slug,
                ])
                ->one();

            $category_post[$category_slug] = Post::find()
                ->joinWith('category')
                ->where([
                    'status' => Post::STATUS_PUBLISHED,
                    Category::tableName() . '.slug' => $category_slug,
                ])
                ->orderBy('published_at DESC')
                ->limit(10)
                ->all();
        }

        $posts_tzgg = (new \yii\db\Query())
            ->select('post.id, post.published_at as published_at, post_lang.title as title')
            ->from('post')
            ->leftJoin('post_lang', 'post.id = post_lang.post_id')
            ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 6])
            ->orderBy('post.published_at DESC')
            ->limit(9)
            ->all();

        return $this->render('index', [
            'category' => $category,
            'category_post' => $category_post,
            'posts_tzgg' => $posts_tzgg,
        ]);
    }
}