<?php
/*
*   Package:        PHPCrazy
*   Link:           http://53109774.qzone.qq.com/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*
*	这是一个分页模版, 不能直接 include T('pagination');
*	如果您想进一步了解这是怎么工作的
*	请打开文件 indcludes/lib/class.pagination.php
*/ exit; ?>

<!-- 首页 -->
<!-- BEGIN First --><!-- END First -->

<!-- 末页 -->
<!-- BEGIN Last --><!-- END Last -->

<!-- 下页 -->
<!-- BEGIN Next --><li class="am-pagination-next"><a href="{URL}">{TAG}</a></li><!-- END Next -->

<!-- 上页 -->
<!-- BEGIN Prev --><li class="am-pagination-prev"><a href="{URL}">{TAG}</a></li><!-- END Prev -->

<!-- 分页信息 -->
<!-- BEGIN Info --><!-- END Info -->

<!-- 分页数字中的省略号 -->
<!-- BEGIN Ellipsis --><!-- END Ellipsis -->

<!-- 分页数字 -->
<!-- BEGIN Number --><!-- END Number -->

<!-- 当前分页的数字 -->
<!-- BEGIN SelectedNumber --><!-- END Number -->

<!-- 分页下拉框 -->
<!-- BEGIN Select -->
<li class="am-pagination-select">
    <select id="pagination-select" onchange="window.location = this.value;">
        {OPTION}
    </select>
</li>
<!-- END Select -->

<!-- 完整的分页 -->
<!-- BEGIN Box --><ul data-am-widget="pagination" class="am-pagination am-pagination-select am-no-layout">{PREV}{SELECT}{NEXT}</ul><!-- END Box -->