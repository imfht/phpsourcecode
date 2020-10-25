<?php
/**
 * Class UserController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class UserController extends FWFrontController
{
    /**
     * 用户收藏小说
     * @param $id 小说编号
     */
    public function actionAddFavourite($id)
    {
        if (Yii::app()->user->isGuest) {
            echo "请先登录";
            Yii::app()->end();
        }

        $f = UserBookFavorites::model()->find(
            'bookid=? and type=?',
            array(
                $id,
                0,
            )
        );
        if ($f) {
            echo "本书已经在您的收藏夹中";
            Yii::app()->end();
        }

        $book = Book::model()->findByPk($id);
        if (!$book) {
            echo "您收藏的书籍不存在";
            Yii::app()->end();
        }
        $f = new UserBookFavorites();
        $f->bookid = $id;
        $f->title = $book->title;
        $f->type = 0;

        $f->save();

        $book->updateFavoriteNum(1);

        echo "收藏成功，可以去我的收藏夹中看看哦！";
        Yii::app()->end();
    }

    /**
     * 用户推荐列表
     */
    public function actionLike()
    {
//        $dataProvider = $this->getUserBookFavoritesDataProvider(1);
//
//        $this->render('favorites',array(
//            'dataProvider'=> $dataProvider,
//            'type' => 1,
////			'categorys'=> Category::model()->showAllSelectCategory(Yii::app()->params['module']['article'],Category::SHOW_ALLCATGORY),
//        ));
        $this->renderFavView(1);
    }

    /**
     * 用户收藏列表
     */
    public function actionFavorites()
    {
//        $dataProvider = $this->getUserBookFavoritesDataProvider(0);
//        $this->render('favorites',array(
//            'dataProvider'=> $dataProvider,
//            'type' => 0,
////			'categorys'=> Category::model()->showAllSelectCategory(Yii::app()->params['module']['article'],Category::SHOW_ALLCATGORY),
//        ));
        $this->renderFavView(0);
    }

    /**
     * 显示用户推荐、搜藏
     * @param $type integer
     * @return void
     */
    protected function renderFavView($type)
    {
        $this->pageTitle = $type == 1 ? "我的推荐" : "我的书架" . " - " . Yii::app()->name;

        $criteria=new CDbCriteria(array(
            'order'=>'id desc',
        ));

        $criteria->compare('type', $type);

        $count = UserBookFavorites::model()->count($criteria);
        $pages = new CPagination($count);

        $pageSize = 60;
        $cookies = Yii::app()->request->getCookies();
        if (isset($_GET['pagesize'])) {
            $pageSize = intval($_GET['pagesize']);

            if (isset($cookies['favpagesize'])) {
                unset($cookies['favpagesize']);
            }
            $cookie = new CHttpCookie('favpagesize', $pageSize);
            $cookie->expire = time()+60*60*24*30;  //有限期30天
            Yii::app()->request->cookies['favpagesize'] = $cookie;
        } elseif (isset($cookies['favpagesize'])) {
            $pageSize = intval($cookies['favpagesize']);
        }
        // results per page
        $pages->pageSize =$pageSize;
        $pages->applyLimit($criteria);

        $list = UserBookFavorites::model()->findAll($criteria);

        $page = $this->widget('CLinkPager', array(
            'pages' => $pages,
        ), true);

        $this->render('favorites', array(
            'list' => $list,
            'page' => $page,
            'type' => $type,
//            'keywords' => CHtml::encode($keywords),
//            'category' => $category,
        ));
    }
}