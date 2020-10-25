<?php
use \Cute\Web\Handler;


class CategoryHandler extends Handler
{
    use \Cute\Contrib\Handler\DBHandler;
    protected $dbkey = 'wordpress';
    protected $modns = 'Blog\\Model';

    public function get($name = false)
    {
        $query = $this->categories->join();
        if ($name === false) {
            $query->setNothing();
        } else {
            $name = str_replace('-', ' ', strtoupper($name));
            $query->findBy('name', $name);
        }
        $category = $query->setPage(1, 1)->all();
        $this->logSQL();
        echo "\n<h3>分类树状图</h3>\n";
        echo "\n<pre>\n";
        $category->recur(function ($obj) {
            $blanks = str_repeat(' ', $obj->depth * 3);
            printf("%s|-%s\n", $blanks, $obj->name);
        });
        echo "\n</pre>\n";
    }
}

app()->route('/', CategoryHandler);
app()->route('/<string>/', CategoryHandler);
