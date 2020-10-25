<?php
$link=MySQL_connect('localhost','root','mmeizhen');
mysql_select_db('zhoucheng');
mysql_query('set names utf8');


$sqlstr = "select  count(*)  as total  from  xinghao";
$sql = mysql_query($sqlstr) or die("error");
$info = mysql_fetch_array($sql); //第一次 数据库调用
$total = $info["total"]; //每次翻页都要进行 总信息条数 的数据库查询操作
$pagesize = 10; //每页显示数量
$page = $_GET["page"] ? max(intval($_GET["page"]), 1) : 1; //当前页

var_dump($total);
if ($total)
{
    $sql = "select * from  tablename  limit " . ($page - 1) * $pagesize . ",$pagesize";
    $sql = mysql_query($sql) or die("error"); //第二次数据库查询操作
    $info = mysql_fetch_array($sql);
    do
    {
        ;

    } while ($info = mysql_fetch_array($sql));
    include("page_class.php"); //调用分页类
    $url = "url.php?page="; //假设当前页为 URL.PHP
    echo $get_page = new get_page($url, $total, $pagesize, $page); //URL 为要分页的URL地址
}

//优化后的分页技术(只需在第一次调用时进行信息统计即可)
if (isset($_GET["total"]))
{ //只需要进行一次总信息条数的统计即可
    $total = intval($_GET["total"]);
    //以后的的总信息数量通过GET传递即可，节省了1/2的数据库负荷，，，，
} else
{
    $sqlstr = "select  count(*)  as total  from  tablename";
    $sql = mysql_query($sqlstr) or die("error");
    $info = mysql_fetch_array($sql); //第一次 数据库调用
    $total = $info["total"];
}
//总信息条数
$pagesize = 10; //每页显示数量
$page = $_GET["page"] ? max(intval($_GET["page"]), 1) : 1; //当前页
if ($total)
{
    $sql = "select * from  xinghao  limit " . ($page - 1) * $pagesize . ",$pagesize";
    $sql = mysql_query($sqlstr) or die("error"); //第二次数据库查询操作
    $info = mysql_fetch_array($sql);
    do
    {
        ;
    } while ($info = mysql_fetch_array($sql));
    include("page_class.php"); //调用分页类
    $url = "url.php?total=$total&page="; //假设当前页为 URL.PHP
    echo $get_page = new get_page($url, $total, $pagesize, $page); //URL 为要分页的URL地址
}
?>