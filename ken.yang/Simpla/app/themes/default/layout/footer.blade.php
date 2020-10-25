<?php
/**
 * 变量：
 * --$menu_bottom：底部菜单
 *      --list菜单列表
 *      --conent菜单内容
 * --$copyright：版权信息等
 * --$tongji:统计代码
 */
?>
<div class="clearfix"></div>
<hr>
<div class="footer">
    <div>
        {{$menu_bottom['content']}}
    </div>
    <div class="clearfix"></div>
    <p>{{$copyright}}</p>
</div>

<!--统计代码-->
{{$tongji}}