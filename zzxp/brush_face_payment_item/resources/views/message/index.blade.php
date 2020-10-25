@extends('layouts.common')

@section('content')

<script type="text/javascript" src="{{asset('/js/ckeditor/ckeditor/ckeditor.js')}}" ></script>
<script type="text/javascript">
    var editor;
    window.onload = function(){
        editor = CKEDITOR.replace("content",{
                width:800,
                height:300,
                skin:"office2003"
        });
    }

    var url = "{{url('admin/article/upload')}}";

    var global_submit_bool = true;
    function set_func(){
        global_submit_bool = true;
        $('#modal-demo input[name]').val('');
        $('#modal-demo select[name]').val('');
        $('#modal-demo textarea[name]').val('');
        $("#modal-demo").modal("show");
    }
    function upload_file(obj){
        document.myform.action = url;
        document.myform.target = 'uploaded';
        document.myform.actions.value = obj;
        //parent.show_wait(1);
        document.myform.submit();
    }

    function setImage(src,input_name){

        var img = document.createElement('img');
        img.src = src;
        img.width = "200";
        img.height = "200";
        var input = document.createElement('input');
        input.name = input_name;
        input.value = src;
        input.type = 'hidden';
        var show_frame = document.getElementById('show_frame');
        show_frame.innerHTML = '';
        show_frame.appendChild(img);
        show_frame.appendChild(input);
    }

    function edit_func(id){
        global_submit_bool = false;
        sendData('{{url('admin/message/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                setDefaultValue(obj,'modal-demo');                
                setTimeout(function(){editor.setData(obj.message);},200);
                $("#modal-demo").modal("show");
            }
        },'GET');
    }  
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        var data = get_form_data(id);
        data.message = editor.getData();

        send_request('{{url('admin/message/add')}}',data,function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){

        var data = get_form_data(id);
        data.content = editor.getData();
        send_request('{{url('admin/message/update')}}',data,function(msg){
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
            send_request('{{url('admin/message/del')}}',query.join('&'),function(msg){
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

    function addAttach(src){
        editor.insertHtml('<img src="'+src+'" />');
    }
    function addFile(src,name){
      editor.insertHtml('<a href="'+src+'" target="_blank">下载附件：'+name+'</a>');
    }
</script>
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">代理商管理</a><span class="c-999 en">&gt;</span>消息列表管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>
             <span class="select-box inline">
              <select name="type" class="select">
                  <option value="0" >消息类型</option>
                  <option value="1" {{isset($search['type'] ) && $search['type'] == 1 ? 'selected' :'' }}>私信
                  </option>
                  <option value="2" {{isset($search['type'] ) && $search['type'] == 2 ? 'selected' :'' }}>公告</option>
              </select>
            </span>

            
            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="会员电话" />
            <input type="text" name="name" style="width:126px" class="input-text" value="{{isset($search['name']) ? $search['name'] :'' }}" placeholder="会员姓名" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        消息列表
        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/message/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>接收人</th>
            <th>标题</th>
            <th>外链</th>
    				<th class="clo1">类型</th>
    				<th class="col2">创建时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($message as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                <td>
                  姓名:{{$line['name']}}<br />
                  电话:{{$line['phone']}}<br />
                </td>
                
                <td>{{$line['title']}}</td>
                <td>{{$line['links']}}</td>
      					<td class="col1"><?php $filed=[1=>'私信',2=>'公告']; ?>{{isset($filed[$line['type']]) ? $filed[$line['type']] : $line['type']}}</td>
      				
      					<td class="col2">{{$line['created_at']}}</td>

                @if(isset($line['id']))
                <td class="col1" width="6%">
                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}});">修改</a>
                </td>
                @else
                <td>''</td>
                @endif          
            </tr>
          <?php } ?>
        </tbody>
      </table>
        @include('layouts.page')
    </div>
    </form>
  </div>

</section>

<div id="modal-demo" style="height:600px;" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px; height:500px;" >
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">消息列表修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate" name="myform"  enctype="multipart/form-data">
            <input type="hidden" value="" name="id">
            <div class="row cl">
              <label class="form-label col-xs-3">标题：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="title" placeholder="标题" name="username" id="username">
              </div>
            </div>

            <div class="row cl">
              <label class="form-label col-xs-3">接收人电话：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="phone" placeholder="接收人电话" >
              </div>
            </div>
		        <div class="row cl">
              <label class="form-label col-xs-3">类型：</label>
              <div class="formControls col-xs-8">
                <select name="type">
                  <option value="0">请选择</option>
                  <option value="1">私信</option>
	                <option value="2">公告</option>
                </select>
              </div>
            </div>
            <div class="row cl">
              <label class="form-label col-xs-3">外链</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="links" placeholder="如填写外链(如:https://www.baidu.com)时，用户点消息详情会跳转出APP打开" >
              </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-3">内容</label>
                <div class="formControls col-xs-8">

                  <input class="infoTableInput2" name="attach" type="file" id="name" onchange="upload_file('attach')" placeholder="插入图片" />
                  <input type="hidden" name="actions" value="file" />
                  <div id="hidden_id"></div>
                </div> 
            </div>
            <div class="row cl">
                <textarea name="message" id="content" placeholder="内容"></textarea>
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


<div style="display:none">
    <iframe name="uploaded"></iframe>
</div>

<style type="text/css">
</style>
@stop