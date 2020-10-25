<?php
/**
 * Class ArticleController
 *
 * @author pizigou <pizigou@yeah.net>
 */
class ArticleController extends FWFrontController
{
    public function filters() {
        $ret = array();
        if ($this->siteConfig && $this->siteConfig->SiteIsUsedCache) {
            $ret[] = array (
                'FWOutputCache + view',
                'duration' => 2592000,
                'varyByParam' => array('id'),
                'varyByExpression' => array('FWOutputCache', 'getExpression'),
                'dependCacheKey'=> 'article' . $_GET['id'],
//                'dependency' => array(
//                    'class'=> 'FWCacheDependency',
//                    'dependCacheKey'=> 'article' . $_GET['id'],
//                )
            );
        }

        return $ret;
    }

    /**
     * 小说章节详情
     */
    public function actionView($id)
    {
        $chapter = Article::model()->findByPk($id);
        if (!$chapter) {
            return new CHttpException(404);
        }

        $this->pageTitle = $chapter->title;

        $prevChapter = Article::model()->find(
            'bookid=:bookid and chapter<:chapter order by chapter desc',
            array(
                ':bookid' => $chapter->bookid,
                ':chapter' => $chapter->chapter,
            )
        );

        $nextChapter = Article::model()->find(
            'bookid=:bookid and chapter>:chapter order by chapter asc',
            array(
                ':bookid' => $chapter->bookid,
                ':chapter' => $chapter->chapter,
            )
        );

        // 更新小说统计信息
        $chapter->book->updateStats();

        $this->render('detail', array(
            'chapter' => $chapter,
            'prevChapter' => $prevChapter,
            'nextChapter' => $nextChapter,
        ));
    }
}