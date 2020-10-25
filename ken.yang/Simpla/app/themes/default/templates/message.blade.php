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

@if($message)
    @if($type == 'success')
        <div class="alert alert-success" role="alert">{{ $message }}</div>
    @elseif($type == 'error')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @elseif($type == 'info')
        <div class="alert alert-info" role="alert">{{ $message }}</div>
    @else
        <div class="alert alert-warning" role="alert">{{ $message }}</div>
    @endif
@if(isset($url))
<script type="text/javascript">
    function jumpurl() {
        window.location.href = '{{ $url }}';
    }
    var url = '{{ $url }}';
    if (url){
        setTimeout(jumpurl, 2000);
    }
</script>
@endif
@endif

@stop