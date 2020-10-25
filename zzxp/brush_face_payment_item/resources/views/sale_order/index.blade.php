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
        sendData('{{url('admin/sale_order/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/sale_order/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/sale_order/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/sale_order/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">商户管理</a><span class="c-999 en">&gt;</span>销售订单管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>
            <input type="hidden" name="bid" value="{{isset($search['bid']) ? $search['bid'] : ''}}" />

            <span class="select-box inline">
            <select name="status" class="select" >
              <option value="">请选择支付状态</option>
              <option value="0" {{isset($search['status']) && $search['status'] === '0' ? 'selected' :'' }}>待支付</option>
              <option value="1" {{isset($search['status']) && $search['status'] == 1 ? 'selected' :'' }}>支付中</option>
              <option value="2" {{isset($search['status']) && $search['status'] == 2 ? 'selected' :'' }}>已支付</option>
              
            </select>
            </span>
            <input type="text" name="order_sn" style="width:126px" class="input-text" value="{{isset($search['order_sn']) ? $search['order_sn'] :'' }}" placeholder="销售订单编号" />
            <input type="text" name="rname" style="width:126px" class="input-text" value="{{isset($search['rname']) ? $search['rname'] :'' }}" placeholder="商户名称" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        销售订单
    </div>
    <form name="delform" action="{{ url('admin/sale_order/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>商户信息</th>
				<th>订单号</th>
        <th>支付平台订单编号</th>
				<th>收款来源</th>
				<!-- <th>随机数</th> -->
				<th class="clo1">支付方式</th>
				<th>总费用</th>
				<th class="clo1">支付状态</th>
				<th class="col2">交易时间</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($sale_order as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>
                  {{$line['rname']}}<br />
                  {{$line['name']}}<br />
                  {{$line['phone']}}<br />
                </td>
      					<td>{{$line['order_sn']}}</td>
                <td>{{$line['transaction_id']}}</td>
      					<td>{{$line['source'] == 1 ? '刷脸设备收款' : '第三方插件收款'}}</td>
      					<!-- <td>{{$line['nonce_str']}}</td> -->
      					<td class="col1"><?php $filed=[1=>'微信',2=>'支付宝']; ?>{{isset($filed[$line['pay_channel']]) ? $filed[$line['pay_channel']] : $line['pay_channel']}}</td>
      					<td>{{$line['total_fee']}}</td>
      					<td class="col1"><?php $filed=[0=>'否',1=>'支付中',2=>'已支付',3=>'已完成']; ?>{{isset($filed[$line['status']]) ? $filed[$line['status']] : $line['status']}}</td>
      					<td class="col2">{{$line['created_at']}}</td>

                    
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
        <h3 class="modal-title">销售订单修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
                          <label class="form-label col-xs-3">商户编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="bid" placeholder="商户编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">商户会员编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="member_id" placeholder="商户会员编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">订单号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="order_sn" placeholder="订单号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">支付平台订单编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="transaction_id" placeholder="支付平台订单编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">随机数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="nonce_str" placeholder="随机数" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="pay_channel"><option value="0">微信</option>
					<option value="1">支付宝)</option></select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">总费用：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="total_fee" placeholder="总费用" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">商户编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="mid" placeholder="商户编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="status"><option value="0">否</option>
					<option value="1">是)</option></select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">查询次数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="is_search" placeholder="查询次数" name="username" id="username">
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