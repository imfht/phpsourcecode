@extends('layouts.common')

@section('content')

<script type="text/javascript">
    var global_submit_bool = true;
    function set_func(){
        global_submit_bool = true;
        $('#modal-demo input[name]').val('');
        $('#modal-demo select[name]').val('');
        $('#modal-demo textarea[name]').val('');
        $('#modal-demo .edit_pro').hide();
        $("#modal-demo").modal("show");
    }

    //改写点击增加按钮跳出#modal-demo为#modal-demo-add
    // function modalDemoAdd(){
    //     $('#modal-demo .edit_pro').hide();
    // }

    function edit_func(id){
        global_submit_bool = false;
        sendData('{{url('admin/system_user/edit')}}','system_user_id='+id,function(obj){
            if(typeof obj == 'object'){

                $('#modal-demo .edit_pro').show();
                setDefaultValue(obj,'modal-demo');
                $("#modal-demo").modal("show");

                //防止下拉框的角色状态因js的bug出错（危险）
                $('select[name="system_role_id"]').val(obj.system_role_id);

                //获取数据库加密后的密码
                var password=$("input[name='password']").val();
                //将数据库内的加密后密码赋值给隐藏表单
                $("input[name='yuan_md5_password']").val(password);
                //清空密码框的密码
                $("input[name='password']").val('');
            }
        },'GET');
    }

    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        send_request('{{url('admin/system_user/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/system_user/update')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function del_sub(){
        var bool = false;
        var query = [];
        $.each($(document.delform).find('input[name="ids[]"]'),function(i,n){
            if(n.checked){
                bool = true;
                query[query.length] = 'ids[]='+n.value;
            }
        });
        if(!bool){
            return show_message.alert('没有选中任何记录,不能删除');
        }
        show_message.confirm('您确定要删除吗？',function(){
            send_request('{{url('admin/system_user/del')}}',query.join('&'),function(msg){
                if(msg != 1){
                    show_message.alert(msg);
                }else{
                    location.reload();
                }
            },function(){

            })
            // document.delform.submit();
        },function(){});
    }

</script>
<script type="text/javascript" src="{{ asset('lib/jquery/1.9.1/jquery.min.js') }}"></script>
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 系统管理<span class="c-999 en">&gt;</span>系统管理员管理<span class="c-999 en"></span></nav>
    <div class="text-c mt-20">
        <input id='roleName' type="hidden" value="{{$system_role_id}}">
        <form>
            {{--角色:<input type="text" name="total" style="width:120px" class="input-text" value="{{}}" placeholder="角色" />--}}

            角色:
            <span class="select-box inline">
            <select name="system_role_id" class="select" id="filterRoleName">
                <option value="" selected>全部</option>
                <?php if(Session::get('grade')==1){ ?>
                    @foreach($system_role as $role)
                        <option value="{{$role['system_role_id']}}">{{$role['name']}}</option>
                    @endforeach
                <?php }else{ ?>
                    @foreach($system_role as $role)
                        @if(/*Session::get('system_role_id')==$role['system_role_id'] || */$role['creator']==Session::get('sys_id'))
                            <option value="{{$role['system_role_id']}}">{{$role['name']}}</option>
                        @endif
                    @endforeach
                <?php } ?>
            </select>
            </span>

            <script>
                $('#filterRoleName').val($('#roleName').val());
                //alert($('#roleName').val());
            </script>

            姓名: <input type="text" name="nick_name" style="width:120px" class="input-text" value="{{$nick_name}}" placeholder="姓名" />
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">

        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        {{--<button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>--}}
    </div>
    <form name="delform" action="{{ url('admin/system_user/del')}}" method="post">
    <div class="panel-body">
    <table class="table table-border table-bordered table-striped mt-20">
        <thead>
            <tr>
                {{--<th class="col1" width="6%"><input type="checkbox" /></th>--}}
                <th class="col1">编号</th>
                <th>用户编号</th>
                <th>账号</th>
                <th>角色</th>
                <th>姓名</th>
                <th>手机</th>
                <th>邮箱</th>
                <th class="clo1">是否启用</th>
                <th class="clo1">状态</th>
                <th class="col2">备注</th>
                <th class="col2">创建者</th>
                <th class="col2">创建/更新时间</th>
            <th class="col1" align="center" >操作</th>
            </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($system_user['result'] as $line){ $index ++; ?>
                <?php if($line['creator']==Session::get('sys_id') || Session::get('grade')==1){ ?>
                    <tr>
                        {{--<td class="col1"><input type="checkbox" value="{{ $line['system_user_id'] }}" name="ids[]" /></td>--}}
                        <td class="col1">{{ $line['system_user_id']}}</td>

                        <td>{{$line['member_id']}}</td>
                        <td>{{$line['user_name']}}</td>
                        <td>{{$line['name']}}</td>
                        <td>{{$line['nick_name']}}</td>
                        <td>{{$line['phone']}}</td>
                        <td>{{$line['email']}}</td>
                        <td class="col1"><?php $filed=[0=>'关闭',1=>'启用']; ?>{{isset($filed[$line['enabled']]) ? $filed[$line['enabled']] : $line['enabled']}}</td>
                        <td class="col1"><?php $filed=[0=>'已删除',1=>'未删除']; ?>{{isset($filed[$line['status']]) ? $filed[$line['status']] : $line['status']}}</td>
                        <td class="col2">{{$line['remarks']}}</td>
                        <td class="col2">{{$line['creator']}}</td>
                        <td class="col2">创:{{$line['created_at']}}<br>更:{{$line['updated_at']}}</td>

                        <td class="col1" width="6%">
                            <a  class="btn btn-warning radius" onclick="edit_func({{$line['system_user_id']}});">修改</a>
                        </td>
                    </tr>
                <?php } ?>
          <?php } ?>
        </tbody>
    </table>
    <?php
        if($system_user['total']<=0){
    ?>
        <div style="color:red;font-size:20px;text-align:center">您搜索的角色没有数据</div>
    <?php
        }else{
    ?>
        @include('layouts.page')
    <?php } ?>
    </div>
    </form>
  </div>

</section>

<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="height:100%;">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">信息修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="system_user_id">
            <div class="row cl">
                          <label class="form-label col-xs-3">角色：</label>

                          <div class="formControls col-xs-8">
                           <select name="system_role_id">
                             <?php if(Session::get('grade')==1){ ?>
                                 @foreach($system_role as $role)
                                     <option value="{{$role['system_role_id']}}">{{$role['name']}}</option>
                                 @endforeach
                             <?php }else{ ?>
                                 @foreach($system_role as $role)
                                     @if(/*Session::get('system_role_id')==$role['system_role_id'] || */$role['creator']==Session::get('sys_id'))
                                         <option value="{{$role['system_role_id']}}">{{$role['name']}}</option>
                                     @endif

                                 @endforeach
                             <?php } ?>
                           </select>
                          </div>
                        </div>
            <div class="row cl">
              <label class="form-label col-xs-3">用户编号：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="member_id" placeholder="用户编号" >
              </div>
            </div>
		        <div class="row cl">
                          <label class="form-label col-xs-3">账号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="user_name" placeholder="账号" name="username" id="username">
                          </div>
                        </div>
              {{--<div class="row cl edit_pro">
                  <label class="form-label col-xs-3">原密码<span style="color:red">(输入原密码才能进行修改)</span>:</label>
                  <div class="formControls col-xs-8">
                      <input type="text" class="input-text" name="originalPassword" placeholder="原密码" >
                  </div>
              </div>--}}
		          <div class="row cl">
                          <label class="form-label col-xs-3">密码：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="password" placeholder="密码" >
                          </div>
                        </div>
              <input type="hidden" name="yuan_md5_password">
              
    <div class="row cl">
                      <label class="form-label col-xs-3">类型：</label>
                      <div class="formControls col-xs-8">
                        <select name="type">
                          <option value="1">超级管理员</option>
                          <option value="2">经销商</option>
                          <!-- <option value="3">代理商</option> -->
                        </select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">姓名：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="nick_name" placeholder="姓名" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="sex"><option value="0">男</option>
					<option value="1">女</option>
					<option value="2">未知</option></select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">手机：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="phone" placeholder="手机" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">邮箱：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="email" placeholder="邮箱" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">管理是否启用:0关闭1启用：</label>
                          <div class="formControls col-xs-8">
                            <select name="enabled">
                                <option value="0">关闭</option>
                                <option value="1">启用</option>
                            </select>
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">备注：</label>
                      <div class="formControls col-xs-8">
                       <textarea cols="" rows="" class="textarea valid" name="remarks" id="beizhu" placeholder="说点什么...最少输入10个字符" onkeyup="$.Huitextarealength(this,500)"></textarea>
                      </div>
                    </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="status">
                            <option value="1">激活</option>
                            <option value="0">禁用</option>
                        </select>
                      </div>
                    </div>
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="sure_add('modal-demo')">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>


<style type="text/css">
</style>
@stop