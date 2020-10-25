<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>轴承型号</title>
    <link href="pager.css" type="text/css" rel="stylesheet" />
</head>
<body>


<style type="text/css">
    body { font-family:"新宋体", Arial, Verdana; font-size:12px; color:#333; margin:0; }
    th td{font-size: 12px;}
    .Pagination {margin:10px 0 0;padding:5px 0;text-align:rightright; height:20px; line-height:20px; font-family:Arial, Helvetica, sans-serif,"宋体";}
    .Pagination a {margin-left:2px;padding:2px 7px 2px;}
    .Pagination .dot{ border:medium none; padding:4px 8px}
    .Pagination a:link, .Pagination a:visited {border:1px solid #dedede;color:#696969;text-decoration:none;}
    .Pagination a:hover, .Pagination a:active, .Pagination a.current:link, .Pagination a.current:visited {border:1px solid #dedede;color:#fff; background-color:#ff6600; background-image:none; border:#ff6600 solid 1px;}
    .Pagination .selectBar{ border:#dedede solid 1px; font-size:12px; width:95px; height:21px; line-height:21px; margin-left:10px; display:inline}
    .Pagination a.tips{_padding:4px 7px 1px;}
</style>


<?php include "conn.php";?>
<?php include "pager1.class.php";?>


<table  border="1" align="center" cellpadding="0" cellspacing="1">
    <tr>
        <th width="30" height="38">ID</th>
        <th>类型</th>
        <th>型号</th>
        <th>旧型号</th>
        <th>内径<br />(mm)</th>
        <th>外径<br />(mm)</th>
        <th>宽度<br />(mm)</th>
        <th>Cr<br />(kN)</th>
        <th>Cor<br />(kN)</th>
        <th>脂润滑转速<br />(r/min)</th>
        <th>油润滑转速<br />(r/min)</th>
        <th>重量<br />(kg)</th>
        <th style="border-right:0;">供应商</th>
    </tr>
    <?php

    if (isset($_GET["total"]))
    { //只需要进行一次总信息条数的统计即可
        $total = intval($_GET["total"]);
//以后的的总信息数量通过GET传递即可，节省了1/2的数据库负荷，，，，
    } else
    {
        $sqlstr = "select  count(*)  as total  from  xinghao";
        $sql = mysql_query($sqlstr,$link) or die("error");
        $info = mysql_fetch_array($sql); //第一次 数据库调用
        $total = $info["total"];
    }
    //总信息条数


    $CurrentPage=isset($_GET['page'])?$_GET['page']:1;
    $myPage=new pager($total,intval($CurrentPage));
    $pageStr= $myPage->GetPagerContent();


    $sqls= $myPage->getQuerySqlStr("select * from xinghao");

    $result=mysql_query($sqls,$link);
    while ($row=mysql_fetch_array($result)) {
        ?>
        <tr>
            <td height="25px">
                <a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['id']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['leixing']?></a></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['xinxinghao']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['jiuxinghao']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['neijing']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['waijing']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['kuandu']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['cr']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['cor']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['zhisu']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['yiusu']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['zhongliang']?></a></td>
            <td><a href="show.php?id=<?=$row['id']?>" target="_blank"><?php echo $row['gongying']?></a></td>
        </tr>
    <?php
    }

    ?>
 <tr><td colspan="13"><?=$pageStr;?></td></tr>


</table>

</body>
</html>  