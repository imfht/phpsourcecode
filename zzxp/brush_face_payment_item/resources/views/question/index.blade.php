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
        sendData('{{url('admin/question/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                setDefaultValue(obj,'modal-demo');                
                setTimeout(function(){editor.setData(obj.answer);},200);
                $("#modal-demo").modal("show");
            }
        },'GET');
    }  
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        var data = get_form_data(id);
        data.content = editor.getData();

        send_request('{{url('admin/question/add')}}',data,function(msg){
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
        data.answer = editor.getData();
        send_request('{{url('admin/question/update')}}',data,function(msg){
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
            send_request('{{url('admin/question/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">内容管理</a><span class="c-999 en">&gt;</span>管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form> 
            <span class="select-box inline">
              <select name="status" class="select">
                  <option value="0" >是否已经回答</option>
                  <option value="1" {{isset($search['status'] ) && $search['status'] == 1 ? 'selected' :'' }}>未回答
                  </option>
                  <option value="2" {{isset($search['status'] ) && $search['status'] == 2 ? 'selected' :'' }}>已回答</option>
              </select>
            </span>

            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="提问者电话" />
            <input type="text" name="name" style="width:126px" class="input-text" value="{{isset($search['name']) ? $search['name'] :'' }}" placeholder="提问者姓名" />
            <input type="text" name="answer_name" style="width:126px" class="input-text" value="{{isset($search['answer_name']) ? $search['answer_name'] :'' }}" placeholder="回答者姓名" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/question/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
                    <th>提问者</th>
                    <th>提问者电话</th>
                    
                    <th>问题标题</th>
                    <th>问题内容</th>
                    <!-- <th>回答</th> -->
                    <th>回答时间 </th>
                    <th class="clo1">是否已经回答</th>
                    <th>回答者</th>
                    <th>提问时间</th>

            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($question as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
        
                <td>{{$line['name']}}</td>
                <td>{{$line['phone']}}</td>
                <td>{{$line['title']}}</td>
                <td>{{$line['content']}}</td>

                <td>{{$line['status'] == 1 ? '未回答' : $line['answer_time']}}</td>
                <td>{{$line['status'] == 1 ? '未回答' : '已回答'}}</td>
                <td>{{$line['status'] == 1 ? '未回答' : $line['answer_name']}}</td>
                
                <td>{{$line['created_at']}}</td>
                @if(isset($line['id']))
                <td class="col1" width="6%">
                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}});">回答</a>
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
        <h3 class="modal-title">修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate" name="myform"  enctype="multipart/form-data">
            <input type="hidden" value="" name="id">

        <div class="row cl">
                          <label class="form-label col-xs-3">问题标题：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="title" placeholder="新闻标题" >
                          </div>
                        </div>
        <div class="row cl">
                      <label class="form-label col-xs-3">问题内容</label>
                      <div class="formControls col-xs-8">
                        <textarea name="content" cols="50" rows="10"></textarea>
                      </div>
                    </div>
            <div class="row cl">
                  <label class="form-label col-xs-3">回答</label>
                  <div class="formControls col-xs-8">

                    <input class="infoTableInput2" name="attach" type="file" id="name" onchange="upload_file('attach')" placeholder="插入图片" />
                    <input type="hidden" name="actions" value="file" />
                    <div id="hidden_id"></div>
                  </div> 
            </div>
            <div class="row cl">
                <textarea name="question" id="content" placeholder="新闻内容"></textarea>
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