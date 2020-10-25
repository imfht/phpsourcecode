@extends('layouts.admin')
@section('content')

    <fieldset class="layui-elem-field layui-field-title">
        <legend>登录日志列表</legend>
        <div class="layui-field-box">
            <table class="layui-table">
                <colgroup>
                    {{--<col width="100">--}}
                    {{--<col width="250">--}}
                    {{--<col width="300">--}}
                    {{--<col width="200">--}}
                    {{--<col width="150">--}}
                </colgroup>
                <thead>
                <tr>
                    <th>时间</th>
                    <th>IP</th>
                    <th>操作系统信息</th>
                    <th>浏览器信息</th>
                </tr>
                </thead>
                <tbody>
                @foreach($histories as $history)
                    <tr>
                        <td>{{ $history->login_time }}</td>
                        <td>{{ $history->ip_address }}</td>
                        <td>{{ $history->platform_info }}</td>
                        <td>{{ $history->browser_info }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="page">
                {{ $histories->links() }}
            </div>
        </div>
    </fieldset>

@endsection
