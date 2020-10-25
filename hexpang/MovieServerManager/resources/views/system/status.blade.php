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
</style>
@endsection
@section('body')
    <div class="ali center">
      <div class="by">
          <h4 class="ty">
              磁盘状态
          </h4>
          @if(isset($data['disks']))
            <div class="ud">
              <div class="eg">
                <table class="cl" data-sort="table">
                  <thead>
                    <tr>
                      <th class="header">位置</th>
                      <th class="header">挂载点</th>
                      <th class="header">总空间</th>
                      <th class="header">已使用</th>
                      <th class="header">剩余空间</th>
                      <th class="header">使用率</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($data['disks'] as $disk)
                      <tr>
                        <td>{{ $disk[0] }}</td>
                        <td>{{ $disk[5] }}</td>
                        <td>{{ $disk[1] }}</td>
                        <td>{{ $disk[2] }}</td>
                        <td>{{ $disk[3] }}</td>
                        <td>{{ $disk[4] }}</td>
                      </tr>
                    @endforeach
                </tbody>
                </table>
              </div>
            </div>
          @else
            <div class="ph">
              <span class="dy dh red">
                无法连接至服务器.请检查配置文件.
              </span>
              错误
            </div>
          @endif
      </div>

        <div class="by">
            <h4 class="ty">
                服务状态
            </h4>
            @if(isset($data['service']))
              @foreach($data['service'] as $service_name=>$service)
                <div class="ph">
                  <span class="dy dh {{ $service ? '' : 'red' }}">
                    @if($service)
                      <a href="/system/status/{{ $service_name }}/stop">停止</a>
                    @else
                      <a href="/system/status/{{ $service_name }}/start">启动</a>
                    @endif
                    {{ $service ? '运行中' : '未启动' }}
                  </span>
                  {{ $service_name }}
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
