@extends('layouts.admin_template')

@section('content')
    @include('admin.messages')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">添加用户头像</h3>
                </div>

                <div class="box-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th>ID</th>
                            <td>{{$user->id}}</td>
                        </tr>
                        <tr>
                            <th>用户名</th>
                            <td>{{$user->name}}</td>
                        </tr>
                        <tr>
                            <th>上传</th>
                            <td><img width="150" id="user-avatar"
                                     src="{{$user->avatar or asset("/bower_components/adminLTE/dist/img/avatar5.png")}}">
                                <div id="validation-errors"></div>
                                <span id="upload-avatar"></span>
                                {!! Form::open( [ 'route' => ['user.avatarUpload','user'=>$user->id], 'method' => 'POST', 'id' => 'upload', 'files' => true ] ) !!}
                                <a href="#" class="btn button-change-profile-picture">
                                    <label for="upload-profile-picture">

                                        <input name="image" id="image" type="file"
                                               class="manual-file-chooser js-manual-file-chooser js-avatar-field">
                                    </label>
                                </a>
                                <div><a class="btn-upload btn btn-primary" href="javascript:;">更换头像</a></div>
                                <div class="span5">
                                    <div id="output" style="display:none">
                                    </div>
                                </div>
                                <span id="filename"></span>
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
                <div class="box-footer">
                    <a href="{{route('user.index')}}" class="btn btn-default pull-right">返回</a>
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    @push('runningScripts')
    <script src="{{ asset("/js/jquery.form.min.js")}}"></script>
    <script>
        $(document).ready(function () {
            var options = {
                beforeSubmit: showRequest,
                success: showResponse,
                dataType: 'json'
            };
            $('.btn-upload').on('click', function () {
                if($('#image').val()==''){alert('请选择图片。');$('#image').focus();return false;}
                $('#upload-avatar').html('正在上传...');
                $('#upload').ajaxForm(options).submit();
            });
        });
        function showRequest() {
            $("#validation-errors").hide().empty();
            $("#output").css('display', 'none');
            return true;
        }

        function showResponse(response) {
            if (response.success == false) {
                $('#upload-avatar').html('');
                var responseErrors = response.errors;
                $.each(responseErrors, function (index, value) {
                    if (value.length != 0) {
                        $("#validation-errors").append('<div class="alert alert-error"><strong>' + value + '</strong><div>');
                    }
                });
                $("#validation-errors").show();
            } else {
                $('#upload-avatar').html('上传成功！');
                $('#user-avatar').attr('src', response.avatar);
                setTimeout(function () {
                    $('#upload-avatar').html('');
                },5000);

            }
        }
    </script>
    @endpush
@endsection