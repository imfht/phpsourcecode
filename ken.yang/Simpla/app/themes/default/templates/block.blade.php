<?php
/**
 * 变量
 * --$title：区块标题
 * --$body：区块内容
 */
?>
<div class="panel panel-default">
    @if($title)
    <div class="panel-heading">
        {{$title}}
    </div>
    @endif
    <div class="panel-body">
        {{$body}}
    </div>
</div>
