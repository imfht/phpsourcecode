<?php
/**
 * 变量：
 * --$message：信息内容
 * --$type：信息类型,success、error、info和其他四种
 * --$url：跳转URL
 */
?>

<!--前台展示信息-->
@extends('Theme::layout.page-single')

@section('content')
<br>
@if($message)
    @if($type == 'success')
        <div class="am-alert am-alert-success" data-am-alert>{{ $message }}</div>
    @elseif($type == 'error')
        <div class="am-alert am-alert-danger" data-am-alert>{{ $message }}</div>
    @elseif($type == 'info')
        <div class="am-alert am-alert-secondary" data-am-alert>{{ $message }}</div>
    @else
        <div class="am-alert" data-am-alert>{{ $message }}</div>
    @endif
    <br>
@if(isset($url))
<script type="text/javascript">
    function jumpurl() {
        window.location.href = '{{ $url }}';
    }
    var url = '{{ $url }}';
    if (url) {
        setTimeout(jumpurl, 2000);
    }
</script>
@endif

@endif

@stop