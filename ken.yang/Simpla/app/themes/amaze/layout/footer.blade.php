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
<footer class="footer">
    <p>{{$menu_bottom['content']}}</p>
    <p>{{$copyright}}</p>
</footer>
<div class="amz-toolbar" id="amz-toolbar" style="right: 119.5px;">
    <a href="#top" title="回到顶部" class="am-icon-btn am-icon-arrow-up" id="amz-go-top"></a> 
</div>
<div class="clearfix"></div>

<!--统计代码-->
{{$tongji}}