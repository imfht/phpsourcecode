<?php
/**
 * Class CategoryController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class CategoryController extends FWFrontController
{
    public function filters() {
        $ret = array();
        if ($this->siteConfig && $this->siteConfig->SiteIsUsedCache) {
            $ret[] = array (
                'FWOutputCache + index',
                'duration' => 2592000,
                'varyByParam' => array('title'),
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey' => 'book-category' . $_GET['title'],
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'book-category' . $_GET['title'],
//                )
            );
        }
        return $ret;
    }

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex($title)
	{

        $category = Category::model()->find(
          'shorttitle=:shorttitle and status=:status',
          array(
              ':shorttitle' => $title,
              ':status' => Yii::app()->params['status']['ischecked'],
          )
        );

        if (!$category) {
            return new CHttpException(404);
        }

        $this->pageTitle = !empty($category->seotitle) ? $category->seotitle : $category->title;
        $this->pageKeywords = $category->keywords;
        $this->pageKeywords = $category->description;

        $criteria = new CDbCriteria(array(
            'order' => 'createtime desc',
        ));

        $criteria->compare('status', Yii::app()->params['status']['ischecked']);
        $criteria->compare('cid', $category->id);

//        $criteria->compare('recommendlevel', 1);

//        $dataProvider = new CActiveDataProvider('Book',array(
//            'criteria'=> $criteria,
//            'pagination'=> array(
//                'pageSize'=> Yii::app()->params['girdpagesize'],
//            ),
//        ));

        $count = Book::model()->count($criteria);
        $pages = new CPagination($count);

        // results per page
        $pages->pageSize = Yii::app()->params['pagesize']['book'];
        $pages->applyLimit($criteria);

        $list = Book::model()->findAll($criteria);

        $page = $this->widget('CLinkPager', array(
            'pages' => $pages,
        ), true);

        $this->render('index', array(
            'list' => $list,
            'page' => $page,
            'category' => $category,
        ));
	}

}