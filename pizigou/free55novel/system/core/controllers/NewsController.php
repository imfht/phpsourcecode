<?php
/**
 * Class NewsController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class NewsController extends FWFrontController
{

    public function filters() {
        $ret = array();
        if ($this->siteConfig && $this->siteConfig->SiteIsUsedCache) {
            $ret[] = array (
                'FWOutputCache + index',
                'duration' => 2592000,
                'varyByParam' => array('id', 'page'),
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey'=> 'news-category' . $_GET['id'] . $_GET['page'],
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'news-category' . $_GET['id'] . $_GET['page'],
//                )
            );
            $ret[] = array (
                'FWOutputCache + view',
                'duration' => 2592000,
                'varyByParam' => array('id'),
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey'=> 'news' . $_GET['id'],
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'news' . $_GET['id'],
//                )
            );
        }

        return $ret;
    }

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
        $criteria=new CDbCriteria(array(
            'order' => 'createtime desc',
        ));

        $category = null;
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($id > 0) {
                $criteria->compare("cid", $id);

                $category = NewsCategory::model()->findByPk($id);
            }
        }

        if ($category) {
            $this->pageTitle = !empty($category->seotitle) ? $category->seotitle : $category->title;
            $this->pageKeywords = $category->keywords;
            $this->pageKeywords = $category->description;
        }

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);

        $count = News::model()->count($criteria);
        $pages = new CPagination($count);

        // results per page
        $pages->pageSize = Yii::app()->params['pagesize']['news'];
        $pages->applyLimit($criteria);

        $list = News::model()->findAll($criteria);

        $page = $this->widget('CLinkPager', array(
            'pages' => $pages,
        ), true);

		$this->render('index', array(
            'list' => $list,
            'page' => $page,
            'category' => $category,
        ));
	}

    /**
     * 新闻详情
     */
    public function actionView($id)
    {
        $news = News::model()->findByPk($id);
        if (!$news) {
            return new CHttpException(404);
        }

        $this->pageTitle = $news->title;
        $this->pageKeywords = $news->keywords;
        $this->pageDescription = $news->summary;

        $this->render('detail', array(
            'news' => $news,
        ));
    }

}