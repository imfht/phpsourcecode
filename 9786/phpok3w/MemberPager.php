<?php
Class MemberPager extends Pager
{
    function showMemberList()
    {
        global $db;

        $data = $this->getPageData();
// 显示结果的代码
        // ......
    }
}
/// 调用
if ( isset($_GET['page']) )
{
    $page = (int)$_GET['page'];
}
else
{
    $page = 1;
}
$sql = "select * from members order by id";
$pager_option = array(
    "sql" => $sql,
    "PageSize" => 10,
    "CurrentPageID" => $page
);
if ( isset($_GET['numItems']) )
{
    $pager_option['numItems'] = (int)$_GET['numItems'];
}
$pager = @new MemberPager($pager_option);
$pager->showMemberList();
?>