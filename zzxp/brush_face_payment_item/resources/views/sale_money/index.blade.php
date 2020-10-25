@extends('layouts.common')

@section('content')

<script type="text/javascript">
    var global_submit_bool = true;
    function set_func(){
        global_submit_bool = true;
        $('#modal-demo input[name]').val('');
        $('#modal-demo select[name]').val('');
        $('#modal-demo textarea[name]').val('');
        $("#modal-demo").modal("show");
    }
    function edit_func(id){
        global_submit_bool = false;
        sendData('{{url('admin/sale_money/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                setDefaultValue(obj,'modal-demo');
                $("#modal-demo").modal("show");
            }
        },'GET');
    }  
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        send_request('{{url('admin/sale_money/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/sale_money/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/sale_money/del')}}',query.join('&'),function(msg){
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
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">商户管理</a><span class="c-999 en">&gt;</span>销售收入管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="销售收入编号" />
            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="客户电话" />
            <input type="text" name="name" style="width:126px" class="input-text" value="{{isset($search['name']) ? $search['name'] :'' }}" placeholder="客户姓名" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        销售收入
    </div>
    <form name="delform" action="{{ url('admin/sale_money/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>会员信息</th>
				<th>行业信息</th>
				<th>金额</th>
				<th>订单号</th>
				<th class="clo1">状态</th>
				<th class="col2">消费时间</th>
				<th class="col2">备注</th>
				<th class="col2">创建时间</th>
				<th class="col2">更新时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($sale_money as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>
                  姓名:{{$line['name']}}<br />
                  电话:{{$line['phone']}}<br />
                </td>
      					<td>{{$line['bname']}}</td>
      					<td>{{$line['money']}}</td>
      					<td>{{$line['order_sn']}}</td>
      					<td class="col1"><?php $filed=[0=>'未处理',1=>'已处理']; ?>{{isset($filed[$line['status']]) ? $filed[$line['status']] : $line['status']}}</td>
      					<td class="col2">{{$line['sale_time']}}</td>
      					<td class="col2">{{$line['remark']}}</td>
      					<td class="col2">{{$line['created_at']}}</td>
      					<td class="col2">{{$line['updated_at']}}</td>

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

<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">销售收入修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
                          <label class="form-label col-xs-3">会员编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="member_id" placeholder="会员编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">行业编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="code" placeholder="行业编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">金额：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="金额" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">订单号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="order_sn" placeholder="订单号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="status"><option value="0">未处理</option>
					<option value="1">已处理)</option></select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">消费时间：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="sale_time" placeholder="消费时间" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">备注：</label>
                      <div class="formControls col-xs-8">
                       <textarea cols="" rows="" class="textarea valid" name="remark" id="beizhu" placeholder="说点什么...最少输入10个字符" onkeyup="$.Huitextarealength(this,500)"></textarea>
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