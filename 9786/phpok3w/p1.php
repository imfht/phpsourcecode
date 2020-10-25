<?php
/*
分页类 用于实现对多条数据分页显示　
version：1.0
Date：2013-10-20
*/

/*
    调用非常方便，先连接好数据库，直接传入查询的sql字符串即可，也可以指定每页显示的数据条数
    例如$pages = new Page('SELECT * FROM `zy_common_member`');
    或  $pages = new Page('SELECT * FROM `zy_common_member`', 10);
*/

class Page
{
    private $curPage;
    private $totalPages; //数据总共分多少页显示
    private $dispNum; //每页显示的数据条数
    private $queryStr; //查询的SQL语句
    private $limitStr; //查询语句后面的limit控制语句

    /*
    构造函数
    $queryStr 查询数据的SQL语句
    $dispNum  每页显示的数据条数
    */
    public function __construct($queryStr = '', $dispNum = 10)
    {
        $result = mysql_query($queryStr);
        $totalNum = mysql_num_rows($result);
        $this->dispNum = $dispNum;
        $this->totalPages = ceil($totalNum / $dispNum);
        $this->queryStr = $queryStr;

        $temp = (isset($_GET["curPage"]) ? $_GET["curPage"] : 1);
        $this->setCurPage($temp);

        $this->showCurPage();
        $this->showFoot();
    }

    /*显示当前页的数据内容*/
    private function showCurPage()
    {
        $this->limitStr = ' LIMIT ' . (($this->curPage - 1) * $this->dispNum) . ',' . $this->dispNum;
        //echo $this->queryStr.$this->limitStr;
        $result = mysql_query($this->queryStr . $this->limitStr);

        if (!$result)
        {
            if ($this->totalPages > 0)
            {
                echo '查询出错' . '<br>';
            } else
            {
                echo '无数据' . '<br>';
            }
            return;
        }
        $cols = mysql_num_fields($result);

        echo '<table border="1">';
        echo '<tr>';
        for ($i = 0; $i < $cols; $i++)
        {
            echo '<th>';
            echo mysql_field_name($result, $i);
            echo '</th>';
        }
        echo '</tr>';

        while ($row = mysql_fetch_assoc($result))
        {
            echo '<tr>';
            foreach ($row as $key => $value)
            {
                echo '<td>';
                echo $value;
                echo '</td>';
            }
            echo '</tr>';
        }

        echo '</table>';
    }

    private function setCurPage($curPage)
    {
        if ($curPage < 1)
        {
            $curPage = 1;
        } else if ($curPage > $this->totalPages)
        {
            $curPage = $this->totalPages;
        }
        $this->curPage = $curPage;
    }

    /*
    显示分页页脚的信息
    如首页，上一页，下一页，尾页等信息
    */
    private function showFoot()
    {
        echo '<a href="?curPage=1">首页</a>';
        echo '<a href="?curPage=' . ($this->curPage - 1) . '">上一页</a>';
        echo '<a href="?curPage=' . ($this->curPage + 1) . '">下一页</a>';
        echo '<a href="?curPage=' . $this->totalPages . '">尾页</a>';
    }

}

?>
