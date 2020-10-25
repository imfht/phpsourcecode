<?php
/**
 * Class ArticleController
 *
 * Web Post 发布接口
 * @author pizigou <pizigou@yeah.net>
 */
class PublishController extends FWFrontController
{
    protected function beforeAction($action)
    {
        $this->requestIsValid();
        return true;
    }
    /**
     * 小说发布
     */
    public function actionNovel()
    {

        $name = iconv("GBK", "UTF-8//IGNORE",  $_POST['name']);
        $author = iconv("GBK", "UTF-8//IGNORE", $_POST['author']);
        $catName = iconv("GBK", "UTF-8//IGNORE", $_POST['category']);
        $intro = iconv("GBK", "UTF-8//IGNORE", $_POST['intro']);
        $imgUrl = iconv("GBK", "UTF-8//IGNORE", $_POST['imgurl']);
        $sourceUrl = iconv("GBK", "UTF-8//IGNORE", $_POST['sourceurl']);

        Yii::log("novel1 " . $name);

        $category = Category::model()->find('title=:title', array(
            ':title' => $catName,
        ));

        if (!$category) {
            $category = new Category();

            $category->title = $catName;
            $category->shorttitle = H::getPinYin($catName);
            $category->parentid = 0;
            $category->isnav = 1;
            $category->save();
        }

        $book = Book::model()->find('title=:title and cid=:cid', array(
            ':title' => $name,
            ':cid' => $category->id,
        ));

        if (!$book) {
            $book = new Book();
            $book->title = $name;
            $book->cid = $category->id;
            $book->author = $author;
            $book->summary = $intro;
            $book->imgurl = $imgUrl;
            $book->linkurl = $sourceUrl;
            $book->save();
        }
//        Yii::log("novel2 " . $name);

        // 获取该小说的所有章节标题，可对章节进行纠正
        $chapterList = isset($_POST['chapterlist']) && is_array($_POST['chapterlist']) ? $_POST['chapterlist'] : array();
        $baseUrl = $sourceUrl;
        foreach ($chapterList as $k => $v)
        {
//            $v = iconv("GBK", "UTF-8//IGNORE", $v);

            $sourceUrl = $this->getAbsoluteUrl($baseUrl, $v);
            $sourceUrl = trim($sourceUrl);

            $chapter = Article::model()->find('bookid=:bookid and chapter=:chapter', array(
                ':bookid' => $book->id,
                ':chapter' => $k + 1,
            ));
            if (!$chapter)
            {
                $chapter = new Article();
                $chapter->title = '敬请期待';
                $chapter->bookid = $book->id;
                $chapter->chapter = $k + 1;
                $chapter->linkurl = $sourceUrl;
                $chapter->status = Yii::app()->params['status']['isstop'];
                $chapter->save();
            } else {
                // 章节号已经存在，但是还没有实际内容并且上次采集的地址与本次采集地址不一致，则上次采集地址修正为本次采集地址
                if ($chapter->linkurl != $sourceUrl && Yii::app()->params['status']['isstop'] == $chapter->status) {
                    $chapter->linkurl = $sourceUrl;
                    $chapter->save();
                }
            }
        }
        $this->outputAndEnd(0);
    }

    /**
     * 章节发布
     */
    public function actionChapter()
    {
        $title = iconv("GBK", "UTF-8//IGNORE", $_POST['title']);
        $content = iconv("GBK", "UTF-8//IGNORE", $_POST['content']);
        $name = iconv("GBK", "UTF-8//IGNORE", $_POST['name']);
        $catName = iconv("GBK", "UTF-8//IGNORE", $_POST['category']);
        $sourceUrl = iconv("GBK", "UTF-8//IGNORE", $_POST['sourceurl']);

        $category = Category::model()->find('title=:title', array(
            ':title' => $catName,
        ));

        if (!$category) {
            $this->outputAndEnd(-1);
        }

        $book = Book::model()->find('title=:title and cid=:cid', array(
            ':title' => $name,
            ':cid' => $category->id,
        ));

        if (!$book) {
            $this->outputAndEnd(-1);
        }

        $sourceUrl = trim($sourceUrl);

        $chapter = Article::model()->find('bookid=:bookid and linkurl=:linkurl and chapter>0', array(
            ':bookid' => $book->id,
            ':linkurl' => $sourceUrl,
        ));

        // 如果发现对硬采集地址没有章节信息，则表示可能没有采集到小说章节目录，则返回失败
        if (!$chapter) {
//            $chapter = new Article();
//            $chapter->title = $title;
//            $chapter->bookid = $book->id;
//            $chapter->content = $content;
//            $chapter->linkurl = $sourceUrl;
//            $chapter->chapter = $book->chaptercount + 1;
//            $chapter->save();
            $this->outputAndEnd(-1);
        } else {
            if (Yii::app()->params['status']['isstop'] == $chapter->status) {
                $chapter->title = $title;
                $chapter->content = $content;
                $chapter->status = Yii::app()->params['status']['ischecked'];
                $chapter->save();
            }
        }

        $this->outputAndEnd(0);
    }

    /**
     * 检查采集请求是否合法
     */
    private function requestIsValid()
    {
        $authKey = $_POST['auth_key'];
        if ($authKey != Yii::app()->params['gather_auth_key'])
        {
            $this->outputAndEnd(-1);
        }
    }

    /**
     * 输出运行结果并停止，如果遇到错误则向采集端返回404
     * @param $r
     */
    private function outputAndEnd($r = 0)
    {
        if ($r < 0) {
            header("HTTP/1.0 501 Not Implemented");
        } else {
            // equal header 200
            echo $r;
        }
        Yii::app()->end();
    }

    /**
     * 根据base url 获取 url 地址
     * @param $baseUrl
     * @param $url
     * @return string
     */
    private function getAbsoluteUrl($baseUrl, $url)
    {
        if (preg_match('/^http:\/\//', $url) > 0) return $url;

        $list = parse_url($baseUrl);
        $port = isset($list['port']) ? ':' . $list['port'] : '';
        if (preg_match('/^\//', $url) > 0) {
            return "http://" . $list['host']  . $port . $url;
        }

        $p = dirname($list['path']);

        return "http://" . $list['host']  . $port . $p . '/' .  $url;
    }
}