@extends('InstallTheme::layout.page')

@section('content')
<div class="listing listing-success">
    <div class="shape">
        <div class="shape-text">{{$version}}</div>
    </div>
    <div class="listing-content">
        <h3 class="lead">Simpla安装向导<small>第二步</small></h3>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>环境检查</th>
                    <th>推荐配置</th>
                    <th class="text-primary">当前配置</th>
                    <th>最低要求</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>操作系统</td>
                    <td>{{$recommendEnvironment['os']}}</td>
                    <td>
                        {{$currentEnvironment['os_ischeck']?'<span class="glyphicon glyphicon-ok text-success"></span>':'<span class="glyphicon glyphicon-remove text-danger"></span>'}}
                        {{$currentEnvironment['os']}}
                    </td>
                    <td>{{$lowestEnvironment['os']}}</td>
                </tr>
                <tr>
                    <td>PHP版本</td>
                    <td>{{$recommendEnvironment['version']}}</td>
                    <td>
                        {{$currentEnvironment['version_ischeck']?'<span class="glyphicon glyphicon-ok text-success"></span>':'<span class="glyphicon glyphicon-remove text-danger"></span>'}}
                        {{$currentEnvironment['version']}}
                    </td>
                    <td>{{$lowestEnvironment['version']}}</td>
                </tr>
                <tr>
                    <td>MCrypt PHP 扩展</td>
                    <td>{{$recommendEnvironment['mcrypt']}}</td>
                    <td>
                        {{$currentEnvironment['mcrypt_ischeck']?'<span class="glyphicon glyphicon-ok text-success"></span>':'<span class="glyphicon glyphicon-remove text-danger"></span>'}}
                        {{$currentEnvironment['mcrypt']}}
                    </td>
                    <td>{{$lowestEnvironment['mcrypt']}}</td>
                </tr>

                <tr>
                    <td>附件上传</td>
                    <td>{{$recommendEnvironment['upload']}}</td>
                    <td>
                        {{$currentEnvironment['upload_ischeck']?'<span class="glyphicon glyphicon-ok text-success"></span>':'<span class="glyphicon glyphicon-remove text-danger"></span>'}}
                        {{$currentEnvironment['upload']}}
                    </td>
                    <td>{{$lowestEnvironment['upload']}}</td>
                </tr>
                <tr>
                    <td>磁盘空间</td>
                    <td>{{$recommendEnvironment['space']}}</td>
                    <td>
                        {{$currentEnvironment['space_ischeck']?'<span class="glyphicon glyphicon-ok text-success"></span>':'<span class="glyphicon glyphicon-remove text-danger"></span>'}}
                        {{$currentEnvironment['space']}}
                    </td>
                    <td>{{$lowestEnvironment['space']}}</td>
                </tr>
            </tbody>
        </table>
        <hr>
        <p class="text-align-center">
            <a href="/install/step2" type="button" class="btn btn-primary">重新检测</a>
            @if($currentEnvironment['os_ischeck'] && $currentEnvironment['version_ischeck'])
            <a href="/install/step3" type="button" class="btn btn-primary">下一步</a>
            @else
            <span class="text-danger">你的当前的配置环境无法安装Simpla</span>
            @endif
        </p>
    </div>
</div>

@stop