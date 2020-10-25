@extends('BackTheme::layout.master')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">用户信息</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="col-lg-12">
            <blockquote>
                <div class='pull-left'>
                    <img src="/{{$user['picture']}}" alt="{{$user['username']}}的头像" class="img-rounded author-head margin-right-20"  width="80" height="80">
                </div>
                <div>
                    <p>用户名：{{$user['username']}} <span>(<a href="/admin/user/{{$user['id']}}/edit" role="button">编辑个人信息</a>)</span></p>
                    <h5>邮箱：{{$user['email']}}</h5>
                    <h5>最近登录：{{$user['updated_at']}}</h5>
                </div>
            </blockquote>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>标题</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nodes as $node)
                <tr>
                    <th scope="row"><a href="/node/{{$node['id']}}" target="_blank">{{$node['title']}}</a></th>
                    <td>{{$node['created_at']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- /.row -->
@stop