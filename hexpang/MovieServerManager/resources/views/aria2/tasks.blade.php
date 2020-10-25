@extends('body')
@section('header')
<style>
.ph span{
  color:#1ca8dd;
}
.red {
  color:red !important;
}
.dy a {
  color:#5add2d;
}
.stat {
  float:right;
  margin-right:10px;
  font-size:13px;
  line-height: 20px;
}
.stat-action {
  float:right;
  margin-right:10px;
  font-size:13px;
  line-height: 20px;
  color:red;
}
</style>
@endsection
@section('body')
    <div class="ali center">
        <div class="by">
          @if($data)
            <h4 class="ty">
                任务状态
                <span class="stat">停止任务:{{ $data['stat']['numStoppedTotal'] }}</span>
                <span class="stat">暂停任务:{{ $data['stat']['numWaiting'] }}</span>
                <span class="stat">活跃任务:{{ $data['stat']['numActive'] }}</span>
                <span class="stat">下载:{{ $data['stat']['downloadSpeed'] }}/s</span>
                <span class="stat">上传:{{ $data['stat']['uploadSpeed'] }}/s</span>
            </h4>
            @foreach($data['downloading'] as $file)
              <div class="ph">
                <a href="/aria2/tasks/{{ $file['gid'] }}/forceRemove" class="stat-action">删除</a>
                <span class="stat">
                  {{ $file['downloadSpeed'] }}/s
                </span>
                <span class="stat">进度:{{ $file['completedLength'] }}/{{ $file['totalLength'] }}</span>
                @if (isset($file['bittorrent']['info']))
                  {{ $file['bittorrent']['info']['name'] }}
                  @else
                  <label>检索文件中...</label>
                @endif
              </div>
            @endforeach
          @else
            <div class="ph">
              <span class="dy dh red">
                无法连接至服务器.请检查配置文件.
              </span>
              错误
            </div>
          @endif
        </div>
    </div>
@endsection
