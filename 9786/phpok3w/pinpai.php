<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-8
 * Time: 上午10:12
 */
set_time_limit(0);
$link=MySQL_connect('localhost','root','mmeizhen');
mysql_select_db('zhoucheng');
mysql_query('set names utf8');

for ($i = 0; $i < 1000; $i++)
{

    $sqlstr = "select * from  xinghao where pinpai is null limit 0,100";
    $sql = mysql_query($sqlstr, $link) or die("error");

    while ($row = mysql_fetch_array($sql))
    {
        $consql = "SELECT GROUP_CONCAT(pingpai) as pinpai1 from bearing where new_x='" . $row["xinxinghao"] . "'";
        $result = mysql_query($consql, $link);
        $resar = mysql_fetch_array($result);

        $upsql = "update xinghao set pinpai='" . $resar["pinpai1"] . "' where id=" . $row["id"];
        mysql_query($upsql);
        echo $row["id"] . "<br />";

    }
    sleep(1);ob_flush();flush();
}
