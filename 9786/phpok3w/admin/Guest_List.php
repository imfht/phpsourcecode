<?php
require_once("chk.php");
require_once "../AppCode/Class/Guest.php";

$psize = isset($psize) ? intval($psize) : 0;
if ($psize > 0 && $psize != $pagesize)
{
    $pagesize = $psize;
    $offset = ($page - 1) * $pagesize;
}

$page = isset($page) ? max(intval($page), 1) : 1;
$catid = isset($catid) ? intval($catid) : 0;
$areaid = isset($areaid) ? intval($areaid) : 0;
$itemid = isset($itemid) ? (is_array($itemid) ? array_map('intval', $itemid) : intval($itemid)) : 0;
$pagesize = 30;
$offset = ($page-1)*$pagesize;


$do = new guest();
$menus = array(
    array('添加管理员', '?moduleid=' . $moduleid . '&file=' . $file . '&action=add'),
    array('管理员管理', '?moduleid=' . $moduleid . '&file=' . $file),
);
$this_forward = '?file=' . $file;

switch ($action)
{
    case 'add':
        break;
    default:
        $sfields = array('按条件', '用户名', '姓名', '角色');
        $dfields = array('username', 'username', 'truename', 'role');
        isset($fields) && isset($dfields[$fields]) or $fields = 0;
        $type = isset($type) ? intval($type) : 0;
        $areaid = isset($areaid) ? intval($areaid) : 0;
        $fields_select = dselect($sfields, 'fields', '', $fields);
        $condition = ' 1=1 ';
        if ($keyword) $condition .= " AND $dfields[$fields] LIKE '%$keyword%'";
        if ($type) $condition .= " AND admin=$type";
        if ($areaid) $condition .= ($AREA[$areaid]['child']) ? " AND aid IN (" . $AREA[$areaid]['arrchildid'] . ")" : " AND aid=$areaid";
        $lists = $do->get_list($condition);

        include tpl('guest');
        break;

}


$table="dt_fahuo";

$idlist = $_REQUEST["IdList"];
$ispass = $_REQUEST["ispass"];
$CurrentPage=isset($_REQUEST['page'])?$_REQUEST['page']:1;


$cmd=$_REQUEST["cmd"];
if($idlist )
{
    $idlists=implode(",",$idlist);
    $Guest = new Ok3w_Guest();


    switch($cmd)
    {
        case "删除":
            $Guest->Del($idlists);
            break;
        case "开通":
            $Guest->Pass(1,$idlists);
            break;
        case "关闭":
            $Guest->Pass(0,$idlists);
            break;
        case "置顶":
            $Guest->top(1,$idlists);
            break;
        case "取消置顶":
            $Guest->top(0,$idlists);
            break;
        case "已读":
            $Guest->read(1,$idlists);
            break;
        case "未读":
            $Guest->read(0,$idlists);
            break;

    }

}

$Sql1="select * from $table  where 1=1 ";

$conditon="";
if($ispass != "") $conditon.=  " and ispass=" . $ispass;
if($keyword != "") $conditon.=   " and " . $stype & " like '%" . $keyword . "%'";
$conditon .= " order by istop desc, Id desc";

$Sql1.=$conditon;
if (isset($_GET["total"]))
{ //只需要进行一次总信息条数的统计即可
    $total = intval($_GET["total"]);
//以后的的总信息数量通过GET传递即可，节省了1/2的数据库负荷，，，，
} else
{
    $sqlstr = "select  count(*)  as total  from  $table  where 1=1 " .$conditon;
    $sql = mysql_query($sqlstr,$link) or die("error");
    $info = mysql_fetch_array($sql); //第一次 数据库调用
    $total = $info["total"];
}
//总信息条数

$myPage=new pager($total,intval($CurrentPage));
$pageStr= $myPage->GetPagerContent();
$sqls = $myPage->getQuerySqlStr($Sql1);
$result=mysql_query($sqls,$link);

?>

