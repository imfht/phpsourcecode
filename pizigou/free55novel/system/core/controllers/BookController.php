<?php
/**
 * Class BookController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class BookController extends FWFrontController
{

    public function filters() {

        $ret = array();
        if ($this->siteConfig && $this->siteConfig->SiteIsUsedCache) {
            $ret[] = array (
                'FWOutputCache + view',
                'duration' => 2592000,
                'varyByParam' => array('id'),
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey'=> 'book' . $_GET['id'],
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'book' . $_GET['id'],
//                )
            );
        }

        return $ret;
    }

    /**
     * 小说详情
     */
    public function actionView($id)
    {
        $book = Book::model()->findByPk($id);
        if (!$book) {
            return new CHttpException(404);
        }

        $this->pageTitle = $book->title;
        $this->pageKeywords = $book->keywords;
        $this->pageDescription = $book->summary;

        // 更新小说统计信息
        $book->updateStats();

        $this->render('detail', array(
            'book' => $book,
        ));
    }

    /**
     * 小说搜索
     * 根据关键字进行检索
     */
    public function actionSearch()
    {
//        $m = Book::model();

        $criteria=new CDbCriteria(array(
            'order' => 'createtime desc',
        ));

        $keywords = "";
        if (!empty($_GET['keywords'])) {
            $keywords = $_GET['keywords'];
            $criteria->addSearchCondition('title', $keywords);
            $criteria->addSearchCondition('author',$keywords, true, 'OR');
            $this->pageTitle = CHtml::encode($keywords) . " 搜索结果";
        }

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
//        $criteria->compare('recommendlevel', 1);


        $count = Book::model()->count($criteria);
        $pages = new CPagination($count);

        $pageSize = Yii::app()->params['pagesize']['book'];
        $cookies = Yii::app()->request->getCookies();
        if (isset($_GET['pagesize'])) {
            $pageSize = intval($_GET['pagesize']);

            if (isset($cookies['searchpagesize'])) {
                unset($cookies['searchpagesize']);
            }
            $cookie = new CHttpCookie('searchpagesize', $pageSize);
            $cookie->expire = time()+60*60*24*30;  //有限期30天
            Yii::app()->request->cookies['searchpagesize'] = $cookie;
        } elseif (isset($cookies['searchpagesize'])) {
            $pageSize = intval($cookies['searchpagesize']);
        }
        // results per page
        $pages->pageSize =$pageSize;
        $pages->applyLimit($criteria);

        $list = Book::model()->findAll($criteria);

        $page = $this->widget('CLinkPager', array(
            'pages' => $pages,
        ), true);

        $this->render('search', array(
            'list' => $list,
            'page' => $page,
            'keywords' => CHtml::encode($keywords),
//            'category' => $category,
        ));

//        $dataProvider = new CActiveDataProvider('Book',array(
//            'criteria'=> $criteria,
//            'pagination'=>array(
//                'pageSize'=>Yii::app()->params['girdpagesize'],
//            ),
//        ));
//
//        $s = BookViewStatsByDay::model();
//        $w = BookViewStatsByWeek::model();
//        $mn = BookViewStatsByMonth::model();
//
//        $this->render('search', array(
//            'title' => $_GET['title'],
//            'dataProvider' => $dataProvider,
//            'dayDataProvider' => $s->getTopHitsDataProvider(date('Y-m-d'),0, 4),
//            'weekDataProvider' => $w->getTopHitsDataProvider(date('W'), 0, 4),
//            'monthDataProvider' => $mn->getTopHitsDataProvider(date('Y-m'), 0, 4),
//        ));
    }


    /**
     * 用户推荐
     * @param $id
     */
    public function actionLike($id)
    {
        if (Yii::app()->user->isGuest) {
            echo "请先登录";
            Yii::app()->end();
        }

        $f = UserBookFavorites::model()->find(
            'bookid=? and type=?',
            array(
                $id,
                1,
            )
        );
        if ($f) {
            echo "本书您已经推荐过";
            Yii::app()->end();
        }

        $book = Book::model()->findByPk($id);
        if (!$book) {
            echo "您推荐的书籍不存在";
            Yii::app()->end();
        }

        $f = new UserBookFavorites();
        $f->bookid = $id;
        $f->title = $book->title;
        $f->type = 1;
        $f->save();

        $book->updateLikeNum(1);

        echo "推荐成功，感谢您的支持！";
        Yii::app()->end();

    }
}