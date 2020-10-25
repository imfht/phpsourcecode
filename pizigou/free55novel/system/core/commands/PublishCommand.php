<?php
/**
 * 采集发布接口
 *
 * @author pizigou
 */
class PublishCommand extends CConsoleCommand {

    /**
     * 小说发布
     */
    public function actionNovel()
    {
        $argv = $this->trimArgv();

        if (count($argv) < 5) {
            return 1;
        }
        $name = iconv("GBK", "UTF-8//IGNORE",  $argv[0]);
        $author = iconv("GBK", "UTF-8//IGNORE", $argv[1]);
        $catName = iconv("GBK", "UTF-8//IGNORE", $argv[2]);
        $intro = iconv("GBK", "UTF-8//IGNORE", $argv[3]);
        $imgUrl = iconv("GBK", "UTF-8//IGNORE", $argv[4]);

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
            $book->save();
        }
        Yii::log("novel2 " . $name);

        return 0;
    }

    /**
     * 章节发布
     */
    public function actionChapter()
    {
        $argv = $this->trimArgv();

        if (count($argv) < 3) {
            $this->outputAndEnd();
        }

        $title = iconv("GBK", "UTF-8//IGNORE", $argv[0]);
        $content = iconv("GBK", "UTF-8//IGNORE", $argv[1]);
        $name = iconv("GBK", "UTF-8//IGNORE", $argv[2]);

        $book = Book::model()->find('title=:title', array(
            ':title' => $name,
        ));

        if (!$book) {
            return 1;
        }

        $chapter = Article::model()->find('title=:title', array(
            ':title' => $title,
        ));

        if (!$chapter) {
            $chapter = new Article();
            $chapter->title = $title;
            $chapter->bookid = $book->id;
            $chapter->content = $content;
            $chapter->save();
            // 更新章节信息
//            $book->updateLastChapter($chapter);
        }

        return 0;
    }

    /**
     * 整理命令行参数，取出系统所需要的参数
     * @return mixed
     */
    private function trimArgv()
    {
        $argv = $GLOBALS['argv'];
        array_shift($argv);
        array_shift($argv);
        array_shift($argv);

        return $argv;
    }

    /**
     * 输出并停止
     * @param $r
     */
    private function outputAndEnd($r = 0)
    {
        echo $r;
        Yii::app()->end();
    }
}

?>
