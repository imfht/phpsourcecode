@extends('app')
@section('content')
<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/css/main-index.css') }}" />
<script type="text/javascript" src="{{ asset('/script/bootstrap-contextmenu.js') }}"></script>
<div class="container-fluid">
    <div id="context-menu-file">
        <ul class="dropdown-menu" role="menu">
            <li><a tabindex="-1">创建目录</a></li>
            <li><a tabindex="-1">创建目录并进入</a></li>
            <li><a tabindex="-1">创建文件</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="row">
            <div class="col-md-3 left">
                <form class="form-inline">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
                            <input type="text" class="form-control" style="height:30px;" id="local-folder" placeholder="本地站点目录">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-9 right">
                <form class="form-inline">
                    <div class="form-group">
                        <button type="button" class="btn btn-success back" id="back">返回上级</button>
                        <div class="input-group">
                            <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
                            <input type="text" class="form-control" id="remote-folder" style="height:30px;" placeholder="远程站点目录" value="{{ Session::get('pwd')  }}">
                        </div>
                    </div>
                </form>
                <table class="table table-striped table-hover" id="content">
                    <thead style="border:1px solid #ccc;background:#450;color:#fff;font-weight:normal;">
                        <tr id="head">
                            <th class="first sort" id="filename" data="a">名称</th>
                            <th id="size" class="sort" data="a">大小</th>
                            <th id="time" class="sort" data="a">修改时间</th>
                            <th>权限</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(array_key_exists('file', $content))
                            @foreach($content['file'] as $key => $item)
                            <tr>
                                <td class="path" data="{{ Session::get('pwd') }}/{{ $item['path'] }}">
                                    <img src="{{ asset('/imgs/file.gif') }}" alt="" width="18" height="18"/>
                                    {{ $item['path'] }}
                                </td>
                                <td>{{ $item['size'] }} kb</td>
                                <td>{{ $item['last_update_time'] }}</td>
                                <td>{{ $item['power'] }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            选择操作 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#" class="edit">查看/编辑</a></li>
                                            <li><a href="#">下载</a></li>
                                            <li><a href="#">删除</a></li>
                                            <li><a href="#">重命名</a></li>
                                            <li><a href="#">文件权限</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                        @if(array_key_exists('dir', $content))
                            @foreach($content['dir'] as $key => $item)
                            <tr>
                                <td class="path"  data="{{ Session::get('pwd') . '/' . $item['path'] }}">
                                    <img src="{{ asset('/imgs/folder.jpg') }}" alt="" width="18" height="18"/>
                                    <a class="pwd" data="{{ Session::get('pwd') . '/' . $item['path'] }}" onclick="changePwd(this)">{{ $item['path'] }}</a>
                                </td>
                                <td> --- </td>
                                <td> --- </td>
                                <td>{{ $item['power'] }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            选择操作 <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#">下载</a></li>
                                            <li><a href="#">删除</a></li>
                                            <li><a href="#">重命名</a></li>
                                            <li><a href="#">文件权限</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name=""/>


<script type="text/javascript">

    function createHtml(result, pwd) {
        $('tbody').html('');

        var fileList = '';
        var dirList = '';
        var fileData = result.content.file ? result.content.file : [];
        var dirData = result.content.dir ? result.content.dir : [];
        if (fileData.length > 0) {
            for (var i = 0; i < fileData.length; i++) {
                var filename = result.pwd + '/' + fileData[i].path;
                fileList += "<tr>";
                fileList += "<td class='path' data='" + filename + "'>";
                fileList += "<img src=\"{{ asset('/imgs/file.gif') }}\" width=\"18\" height=\"18]\"/> ";
                fileList += fileData[i].path;
                fileList += "</td>";
                fileList += "<td>" + fileData[i].size + " kb</td>";
                fileList += "<td>" + fileData[i].last_update_time + "</td>";
                fileList += "<td>" + fileData[i].power + "</td>";
                fileList +=  '<td><div class="btn-group"> <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 选择操作 <span class="caret"></span> </button>';
                fileList += '<ul class="dropdown-menu" role="menu"> <li><a href="#" class="edit">查看/编辑</a></li> <li><a href="#">下载</a></li> <li><a href="#">删除</a></li> <li><a href="#">重命名</a></li> <li><a href="#">文件权限</a></li> </ul> </div></td>';
                fileList += "</tr>";
            }
        }
        if (dirData.length > 0) {
            for (var i = 0; i < dirData.length; i++) {
                var path = dirData[i].path;
                var dirname = result.pwd + '/' + path;
                dirList += "<tr>";
                dirList += "<td class='path' data='" + dirname + "'>";
                dirList += "<img src=\"{{ asset('/imgs/folder.jpg') }}\" width=\"18\" height=\"18]\"/> ";
                dirList += '<a class="pwd" data="' + dirname + '" onclick="changePwd(this)">' + path + '</a>';
                dirList += "</td>";
                dirList += "<td> --- </td>";
                dirList += "<td> --- </td>"
                dirList += "<td>" + dirData[i].power + "</td>";
                dirList +=  '<td><div class="btn-group"> <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 选择操作 <span class="caret"></span> </button>';
                dirList += '<ul class="dropdown-menu" role="menu"> <li><a href="#" class="edit">查看/编辑</a></li> <li><a href="#">下载</a></li> <li><a href="#">删除</a></li> <li><a href="#">重命名</a></li> <li><a href="#">文件权限</a></li> </ul> </div></td>';
                dirList += "</tr>";
            }
        }
        $('tbody').html(fileList + dirList);
    }
    function changePwd(_this)
    {
        var lay = layer.load('进入中...', 0);
        var pwd = $(_this).attr('data');

        $.ajax({
            url: "{{ URL('main/changPwd') }}",
            type: 'post',
            data: {
                'pwd' : pwd
            },
            success: function(result)
            {
                if(result.msg == 'ok')
                {
                    createHtml(result);
                    $('#remote-folder').val(result.pwd);
                    layer.close(lay);
                }
            },
            error: function()
            {
                layer.close(lay);
                layer.alert('噢，你的网络似乎不太行~');
            },
            dataType: 'JSON'
        });

        return false;
    }

    $('.edit').click(function() {
        var index = $('.e')
        $.ajax({
            url: "{{ URL('main/download') }}",
            type: 'post',
            data: {
                'path': path
            },
            success: function () {

            }
        });

        return false;
    });

    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var back = {

            init: function()
            {
                $('#back').click(function() {
                    var lay = layer.load('返回中...', 0);

                    $.ajax({
                        url: "{{ URL('main/backToPrev') }}",
                        type: 'post',
                        success: function(result)
                        {
                            if(result.msg == 'ok')
                            {
                                createHtml(result);
                                $('#remote-folder').val(result.pwd);
                                layer.close(lay);
                            }
                        },
                        error: function()
                        {
                            layer.close(lay);
                            layer.alert('噢，你的网络似乎不太行~');
                        },
                        dataType: 'JSON'
                    })
                });
            }
        };

        back.init();
    });
</script>
@endsection
