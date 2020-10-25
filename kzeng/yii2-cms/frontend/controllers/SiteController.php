<?php

namespace frontend\controllers;

use frontend\actions\PageAction;
use frontend\actions\PostAction;
use frontend\models\ContactForm;
use yeesoft\page\models\Page;
use yeesoft\post\models\Post;
use Yii;
use yii\data\Pagination;
use yeesoft\carousel\models\Carousel;
use common\helpers\Dump;

/**
 * Site controller
 */
class SiteController extends \yeesoft\controllers\BaseController
{
    // public $layout = 'layout_left';
    public $layout = 'layout_left';
    public $freeAccess = true;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex($slug = 'index')
    {
        // display home page
        if (empty($slug) || $slug == 'index') {

        	// $this->layout = 'home';
            // $this->layout = 'index';
            $this->layout = 'hss_whj';
            // 是否开启幻灯片
            $slider = 1;
            $carousel = Carousel::findAll(['status'=>1]);
            // Dump::dump($carousel[0]->title);



           // $query = Post::find()->where(['status' => Post::STATUS_PUBLISHED]);
           // $countQuery = clone $query;

           // $pagination = new Pagination([
           //     'totalCount' => $countQuery->count(),
           //     'defaultPageSize' => Yii::$app->settings->get('reading.page_size', 10),
           // ]);

           // $posts = $query->orderBy('published_at DESC')->offset($pagination->offset)
           //     ->limit($pagination->limit)
           //     ->all();
            //免费辅导
            // $posts_mffds = Post::find()
            //     ->where(['status' => Post::STATUS_PUBLISHED, 'category_id' => 7])
            //     ->orderBy('published_at DESC')
            //     ->limit(7)
            //     ->all();

            // --- 标签
            // 工作动态
            $posts_gzdt = (new \yii\db\Query())
                ->select('post.id, post.important, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->leftJoin('post_tag_post', 'post.id = post_tag_post.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post_tag_post.tag_id' => 6])
                ->orderBy('post.important desc, post.published_at DESC')
                ->limit(9)
                ->all();
            // 文化动态
            $posts_whdt = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->leftJoin('post_tag_post', 'post.id = post_tag_post.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post_tag_post.tag_id' => 7])
                ->orderBy('post.published_at DESC')
                ->limit(9)
                ->all();
            // 通知公告
            $posts_tzgg = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->leftJoin('post_tag_post', 'post.id = post_tag_post.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post_tag_post.tag_id' => 8])
                ->orderBy('post.published_at DESC')
                ->limit(9)
                ->all();

            // --- 分类
            // 专业艺术
            $posts_zyys = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 5])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();
            // 社会文化
            $posts_shwh = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 4])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();
            // 文化遗产
            $posts_whyc = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 3])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();
            // 文化市场
            $posts_whsc = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 8])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();

            // 视频新闻
            $posts_spxw = (new \yii\db\Query())
                ->select('post.id, post.thumbnail, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 16])
                ->orderBy('post.published_at DESC')
                ->limit(3)
                ->all();

            // 新闻出版
            $posts_xwcb = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 9])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();
            // 文化产业
            $posts_whcy = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 10])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();
            // 政策法规
            $posts_zcfg = (new \yii\db\Query())
                ->select('post.id, post.published_at as published_at, post_lang.title as title')
                ->from('post')
                ->leftJoin('post_lang', 'post.id = post_lang.post_id')
                ->where(['post.status' => Post::STATUS_PUBLISHED, 'post.category_id' => 11])
                ->orderBy('post.published_at DESC')
                ->limit(6)
                ->all();

            return $this->render('index', [
                // 'posts' => $posts,
                // 'posts_mffds' => $posts_mffds,

                'posts_gzdt' => $posts_gzdt,
                'posts_whdt' => $posts_whdt,
                'posts_tzgg' => $posts_tzgg,

                'posts_zyys' => $posts_zyys,
                'posts_shwh' => $posts_shwh,
                'posts_whyc' => $posts_whyc,
                'posts_whsc' => $posts_whsc,

                'posts_spxw' => $posts_spxw,

                'posts_xwcb' => $posts_xwcb,
                'posts_whcy' => $posts_whcy,
                'posts_zcfg' => $posts_zcfg,

                'slider'      => $slider,
                'carousel' => $carousel,
                //'pagination' => $pagination,
            ]);
        }
        $this->layout = 'layoutleft';
        //try to display action from controller
        try {
            return $this->runAction($slug);
        } catch (\yii\base\InvalidRouteException $ex) {

        }

        //try to display static page from datebase
//         $page = Page::getDb()->cache(function ($db) use ($slug) {
//             return Page::findOne(['slug' => $slug, 'status' => Page::STATUS_PUBLISHED]);
//         }, 3600);
        $page = Page::findOne(['slug' => $slug, 'status' => Page::STATUS_PUBLISHED]);

        if ($page) {
            $pageAction = new PageAction($slug, $this, [
                'slug'   => $slug,
                'page'   => $page,
                'view'   => $page->view,
                'layout' => $page->layout,
            ]);

            return $pageAction->run();
        }

        //try to display post from datebase
//         $post = Post::getDb()->cache(function ($db) use ($slug) {
//             return Post::findOne(['slug' => $slug, 'status' => Post::STATUS_PUBLISHED]);
//         }, 3600);
        $post = Post::findOne(['slug' => $slug, 'status' => Post::STATUS_PUBLISHED]);

        if ($post) {
            $postAction = new PostAction($slug, $this, [
                'slug'   => $slug,
                'post'   => $post,
                'view'   => $post->view,
                'layout' => $post->layout,
            ]);

            return $postAction->run();
        }

        //if nothing suitable was found then throw 404 error
        throw new \yii\web\NotFoundHttpException('Page not found.');
    }
    public function post($id) {
        echo $id;
        die;
    }
    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if ( $model->load(Yii::$app->request->post()) ) {

            if ( $model->validate() ) {

                Yii::$app->session->setFlash('success', '成功');
            } else {

                Yii::$app->session->setFlash('error', '失败');
            }

            return $this->refresh();

        } else {
     
            $this->layout = 'hss_whj';
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {

        $this->layout = 'img_up';
        return $this->render('about',[
            'title'   => '关于我们',
        ]);
    }

    public function actionAjaxBroker($args)
    {
        $args = json_decode($args, true);
        if (YII_ENV_DEV) {
            yii::error(print_r($args, true));
        }
        return call_user_func(array($args['classname'], $args['funcname']), $args['params']);
    }

}
