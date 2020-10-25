<?php
/**
 * 变量
 * --$title：区块标题
 * --$body：区块内容
 */
?>
<section class="am-panel am-panel-default">
    @if($title)
    <div class="am-panel-hd">{{$title}}</div>
    @endif
    <div class="am-panel-bd">
        {{$body}}
    </div>
</section>
