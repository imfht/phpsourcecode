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

<!-- 未激活的分页按钮 -->
<!-- BEGIN Tag --> <li class="am-disabled"><a href="#">{TAG}</a></li><!-- END Tag -->

<!-- 首页和末页 -->
<!-- BEGIN FirstOrLast --><li><a href="{URL}">{TAG}</a></li><!-- END FirstOrLast -->

<!-- 上页和下页 -->
<!-- BEGIN NextOrPrev --><li><a href="{URL}">{TAG}</a></li><!-- END NextOrPrev -->

<!-- 分页信息 -->
<!-- BEGIN Info --><li class="am-active"><a href="#">{ONPAGE} / {TOTAL}</a></li><!-- END Info -->

<!-- 完整的分页 -->
<!-- BEGIN Box --><ul class="am-pagination">{FIRST}{NEXT}{INFO}{PREV}{LAST}</ul><!-- END Box -->