<?php
/* 
需求，建立一个test数据库，在里边建一个test表，里面就 
只要id字段，输入一下数据就可以啦。。 
由于水平有限，难免出错。。 
*/
$conn = mysql_connect("localhost","root","");

$maxnum = 2;  //每页显示记录条数 

mysql_select_db("test", $conn);
$query1 = "SELECT COUNT(*) AS totalrows FROM test ";
$result1 = mysql_query($query1, $conn) or die(mysql_error());
$row1 = mysql_fetch_assoc($result1);
$totalRows1 = $row1['totalrows'];  //数据集数据总条数 
$totalpages = ceil($totalRows1/$maxnum);//计算可分页总数，ceil()为上舍函数 

if(!isset($_GET['page']) || !intval($_GET['page']) || $_GET['page'] > $totalpages) $page = 1;  //对3种出错进行默认处理 
//在url参数page不存在时，page不为10进制数时，page大于可分页数时，默认为1 
else $page = $_GET['page'];

$startnum = ($page - 1)*$maxnum; //从数据集第$startnum条开始取，注意数据集是从0开始的 

$query = "SELECT * FROM test LIMIT $startnum,$maxnum";//选择出符合要求的数据 从$startnum条数据开始，选出$maxnum行 
$result = mysql_query($query, $conn) or die(mysql_error());
$row = mysql_fetch_assoc($result);

?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=gb2312">
        <title>分页示例</title>
        <script language="JavaScript" type="text/JavaScript">
            <!--
            function MM_jumpMenu(targ,selObj,restore){ //v3.0
                eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
                if (restore) selObj.selectedIndex=0;
            }
            //-->
        </script>
        <style type="text/css">
            a{text-decoration:none;}
            a:hover{text-decoration:underline}
            table{font-size:12px;}
            .tb{background-color:#73BB95}
            .tr{background-color:#FFFFFF}
        </style>
    </head>

    <body>
    <table width="30%"  border="0" align="center" cellpadding="0" cellspacing="1" class="tb">
        <tr>
            <td height="24"><div align="left">分页示例</div></td>
        </tr>
        <?php if($totalRows1) {//记录集不为空显示
        do {
            ?>
            <tr class="tr">
                <td height="24"><div align="center"><?php echo $row['id'];?></div></td>
            </tr>
        <?php }while($row = mysql_fetch_assoc($result));?>
    </table>

    <table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr><form name="form1">
                <td height="27"><div align="center">
                        <?php
                        echo "共计<font color=\"#ff0000\">$totalRows1</font>条记录";
                        echo "<font color=\"#ff0000\">".$page."</font>"."/".$totalpages."页 ";

                        //实现 << < 1 2 3 4 5> >> 分页链接
                        $pre = $page - 1;//上一页
                        $next = $page + 1;//下一页
                        $maxpages = 4;//处理分页时 << < 1 2 3 4 > >>显示4页
                        $pagepre = 1;//如果当前页面是4，还要显示前$pagepre页，如<< < 3 /4/ 5 6 > >> 把第3页显示出来

                        if($page != 1) { echo "<a href='".$_SERVER['PHP_SELF']."'><<</a> ";
                            echo "<a href='".$_SERVER['PHP_SELF'].'?page='.$pre."'><</a> ";}

                        if($maxpages>=$totalpages) //如果总记录不足以显示4页
                        {$pgstart = 1;$pgend = $totalpages;}//就不所以的页面打印处理
                        elseif(($page-$pagepre-1+$maxpages)>$totalpages)//就好像总页数是6，当前是5，则要把之前的3 4 显示出来，而不仅仅是4
                        {$pgstart = $totalpages - $maxpages + 1;$pgend = $totalpages;}
                        else{
                            $pgstart=(($page<=$pagepre)?1:($page-$pagepre));//当前页面是1时，只会是1 2 3 4 > >>而不会是 0 1 2 3 > >>
                            $pgend=(($pgstart==1)?$maxpages:($pgstart+$maxpages-1));
                        }

                        for($pg=$pgstart;$pg<=$pgend;$pg++){ //跳转菜单
                            if($pg == $page) echo "<a href=\"".$_SERVER['PHP_SELF']."?page=$pg\"><font color=\"#ff0000\">$pg</font></a> ";
                            else echo "<a href=\"".$_SERVER['PHP_SELF']."?page=$pg\">$pg</a> ";
                        }
                        if($page != $totalpages)
                        {echo "<a href='".$_SERVER['PHP_SELF'].'?page='.$next."'>></a> ";
                            echo "<a href='".$_SERVER['PHP_SELF'].'?page='.$totalpages."'>>></a> ";}
                        ?>
                        <select name="menu1" onChange="MM_jumpMenu('parent',this,0)">
                            <option value="">选择</option>
                            <?php for($pg1=1;$pg1<=$totalpages;$pg1++) {
                                echo "<option value=\"".$_SERVER['PHP_SELF']."?page=$pg1\">".$pg1."</option>";
                            }?>
                        </select>
                </td></form>
        </tr>
    </table>
    <?php } else {//记录集为空时显示?>
        <tr class="tr">
            <td height="24"><div align="center">没有任何记录</div></td>
        </tr>
        </table>
    <?php }?>
    </body>
    </html>
<?php
mysql_free_result($result1);
mysql_free_result($result);
?>