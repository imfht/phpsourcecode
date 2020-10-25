@extends('layouts.common')

@section('content')

<script type="text/javascript" src="{{asset('/js/ckeditor/ckeditor/ckeditor.js')}}" ></script>
<script type="text/javascript">

    var url = "{{url('admin/article/upload')}}";
    var editor;
    window.onload = function(){
        editor = CKEDITOR.replace("content",{
                width:800,
                height:300,
                skin:"office2003"
        });
    }



    function upload_file(obj){
        document.myform.action = url;
        document.myform.target = 'uploaded';
        document.myform.actions.value = obj;
        //parent.show_wait(1);
        document.myform.submit();

    }

    var global_submit_bool = true;
    function set_func(){
        global_submit_bool = true;
        $('#modal-demo input[name]').val('');
        $('#modal-demo select[name]').val('');
        $('#modal-demo textarea[name]').val('');
        $("#modal-demo").modal("show");
    }
  
    function setImage(src,input_name){
        var div = document.createElement('div');
        var img = document.createElement('img');
        img.src = src;
        img.width = "200";
        img.height = "200";
        var input = document.createElement('input');
        input.name = input_name;
        input.value = src;
        input.type = 'hidden';
        var a = parent.document.createElement('a');
        a.innerHTML = '删除';
        a.className = "delNode"; 
        a.onclick = function(){
            this.parentNode.parentNode.removeChild(this.parentNode);
        }

        var show_frame = document.getElementById('show_frame');
        show_frame.appendChild(div);
        div.appendChild(img);
        div.appendChild(input);
        div.appendChild(a);
    }
    function edit_func(id){
        global_submit_bool = false;
        sendData('{{url('admin/goods/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                setDefaultValue(obj,'modal-demo');
                $("#modal-demo").modal("show");
                setTimeout(function(){editor.setData(obj.content);},200);
                // setImage(obj.pic,'pic');

                for(var i in obj.pic_list){
                  setImage(obj.pic_list[i],'pic[]');
                }
            }
        },'GET');
    }  
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }

        var data = get_form_data(id);
        data.content = editor.getData();

        send_request('{{url('admin/goods/add')}}',data,function(msg){
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
        // console.log(data);
        // alert(data);
        send_request('{{url('admin/goods/update')}}',data,function(msg){
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
            send_request('{{url('admin/goods/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">物料管理</a><span class="c-999 en">&gt;</span>物料表管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
          <form>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="物料表编号" />
            <input type="text" name="title" style="width:126px" class="input-text" value="{{isset($search['title']) ? $search['title'] :'' }}" placeholder="物料名称" />

            <span class="select-box inline">
            <select name="type" class="select">
              <option value="">请选择物料类型</option>
              <option value="0" {{isset($search['type']) && $search['type'] === 0 ? 'selected' : ''}}>物料</option>
              <option value="1" {{isset($search['type']) && $search['type'] == 1 ? 'selected' : ''}}>设备</option>
              <option value="2" {{isset($search['type']) && $search['type'] == 2 ? 'selected' : ''}}>收银</option>
            </select>
            </span>

            <span class="select-box inline">
            <select name="status" class="select">
              <option value="">请选择物料状态</option>
              <option value="0" {{isset($search['status']) && $search['status'] === 0 ? 'selected' : ''}}>下线</option>
              <option value="1" {{isset($search['status']) && $search['status'] == 1 ? 'selected' : ''}}>上线</option>
            </select>
            </span>
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        物料表
        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/goods/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>标题</th>
				    <th class="clo1">类型</th>
    				<th >备注</th>
    				<th>价格</th>
            <th>vip价格</th>
    				<th>邮费</th>
    				<th >图片</th>
    				<th >创建时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($goods as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>{{$line['title']}}</td>
      					<td class="col1"><?php $filed=[0=>'物料',1=>'设备',2=>'收银)']; ?>{{isset($filed[$line['type']]) ? $filed[$line['type']] : $line['type']}}</td>
      					<td >{{$line['remark']}}</td>
      					<td>{{$line['price']}}</td>
                <td>{{$line['vip_price']}}</td>
      					<td>{{$line['post_fee'] > 0 ?  $line['post_fee'] : '包邮'}}</td>
      					<td ><img width=200 src="{{$line['pic']}}" /></td>
      					<td >{{$line['created_at']}}</td>

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
  <div class="modal-dialog" style="width:900px; height:500px;" >
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">物料表修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate" name="myform"  enctype="multipart/form-data">
            <input type="hidden" name="input_name" value="pic" />
            <input type="hidden" value="" name="id">
            <div class="row cl">
                          <label class="form-label col-xs-3">标题：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="title" placeholder="标题" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">类型：</label>
                      <div class="formControls col-xs-8">
                        <select name="type">
                          <option value="0">物料</option>
                					<option value="1">设备</option>
                					<option value="2">收银</option>
                          </select>
                      </div>
                    </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">备注：</label>
                      <div class="formControls col-xs-8">
                       <textarea cols="" rows="" class="textarea valid" name="remark" id="beizhu" placeholder="说点什么...最少输入10个字符" onkeyup="$.Huitextarealength(this,500)"></textarea>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">邮费：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="post_fee" placeholder="邮费（0元表示包邮）" name="username" id="username">
                          </div>
                        </div>

    <div class="row cl">
                          <label class="form-label col-xs-3">价格：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="price" placeholder="价格" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">vip价格：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="vip_price" placeholder="vip价格" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">状态：</label>
                          <div class="formControls col-xs-8">
                          <select name="status">
                            <option value="0">下架</option>
                            <option value="1">上架</option>
                          </select>
                          </div>
                        </div>
    <div class="row cl">
                      <label class="form-label col-xs-3">图片：</label>
                      <div class="formControls col-xs-8">
                        <input type="file" name="files" id="upload_file_control" onchange="upload_file('files')" class="text_input middle_length"  />
                        <div id="show_frame"></div>
                        <span style="font-size:12px; color:green"> 375*195 </span>
                      </div>
		<!-- <div class="row cl">
                      <label class="form-label col-xs-3">图片列表：</label>
                      <div class="formControls col-xs-8">
                        <input type="file" id="upload_file_control" onchange="uploadImg(this,0,'pic_list')" class="text_input middle_length" />
                      </div>
                    </div> -->
		<div class="row cl">
                          <label class="form-label col-xs-3">商品说明：</label>
                          <div class="formControls col-xs-8">
                            <input class="infoTableInput2" name="attach" type="file" id="name" onchange="upload_file('attach')" placeholder="插入图片" />
                            <input type="hidden" name="actions" value="file" />
                            <div id="hidden_id"></div>
                          </div>
                        </div>

            <div class="row cl">
                <textarea name="content" id="content" placeholder="商品说明"></textarea>
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