<!--后台展示信息-->
@extends('BackTheme::layout.master')

@section('content')

@if($message)
<div class="col-lg-12">
    <h3 class="page-header">信息提示</h3>
</div>
<div class="row">
    <div class="col-lg-12">
        @if($type == 'success')
        <div class="alert alert-success" role="alert">{{ $message }}</div>
        @elseif($type == 'error')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
        @elseif($type == 'info')
        <div class="alert alert-info" role="alert">{{ $message }}</div>
        @else
        <div class="alert alert-warning" role="alert">{{ $message }}</div>
        @endif
        <div class="alert alert-info" role="alert">2秒后将为你跳转到之前页面！</div>
    </div>
    <!-- /.col-lg-12 -->
</div>
@if(isset($url))
<script type="text/javascript">
    function jumurl() {
        window.location.href = '{{ $url }}';
    }
    setTimeout(jumurl, 2000);
</script>
@endif

@endif

@stop