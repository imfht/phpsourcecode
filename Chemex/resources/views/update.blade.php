<div>当前版本：{{$data['old']}}</div>
<div class="mt-1">最新版本：{{$data['new']}}</div>
@if($data['res'] == -1)
    <a href="{{$data['url']}}" target="_blank" style="width: 100%;" class="btn btn-primary mt-1">下载新版本</a>
@endif
