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
        sendData('{{url('admin/introduce_money/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/introduce_money/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/introduce_money/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/introduce_money/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">代理商管理</a><span class="c-999 en">&gt;</span>奖励明细管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>


            <span class="select-box inline">
            <select name="sale_type" class="select" >
              <option value="">奖励类型</option>
              <option value="1" {{isset($search['sale_type']) && $search['sale_type'] == 1 ? 'selected' :'' }}>推荐奖励</option>
              <option value="2" {{isset($search['sale_type']) && $search['sale_type'] == 2 ? 'selected' :'' }}>分润奖励</option>
              
            </select>
            </span>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="推荐奖励明细编号" />
            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="会员电话" />
            <input type="text" name="introduce_phone" style="width:126px" class="input-text" value="{{isset($search['introduce_phone']) ? $search['introduce_phone'] :'' }}" placeholder="被推荐人电话" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        奖励明细
    </div>
    <form name="delform" action="{{ url('admin/introduce_money/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>会员信息</th>
    				<th>被推荐人信息</th>
            <th>奖励类型</th>
    				<th>订单编号</th>
    				<th class="col2">备注</th>
    				<th>收入金额</th>
    				<th>分成金额</th>
    				<th class="col2">创建时间</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($introduce_money as $line){ $index ++; ?>
            <tr>
                <td >
                  <input type="checkbox" value="{{ $line['id'] }}" name="ids[]" />
                </td>
                <td >{{ $line['id']}}</td>
                
                <td>
                  姓名:{{$line['name']}}<br />
                  电话:{{$line['phone']}}<br />
                </td>
                <td>
                  姓名:{{$line['introduce_name']}}<br />
                  电话:{{$line['introduce_phone']}}<br />
                </td>
                <td>{{$line['sale_type'] == 2 ? '分润奖励' : '推荐奖励'}}</td>
      					<td>{{$line['order_sn']}}</td>
      					<td class="col2">{{$line['remark']}}</td>
      					<td>{{$line['money']}}</td>
      					<td>{{$line['real_money']}}</td>
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
        <h3 class="modal-title">推荐奖励明细修改</h3>
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
                          <label class="form-label col-xs-3">推荐会员编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="introduce_id" placeholder="推荐会员编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">订单编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="order_sn" placeholder="订单编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="type"><option value="0">一级分成</option>
					<option value="1">二级分成</option>
					<option value="2">级分成)</option></select>
                      </div>
                    </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">备注：</label>
                      <div class="formControls col-xs-8">
                       <textarea cols="" rows="" class="textarea valid" name="remark" id="beizhu" placeholder="说点什么...最少输入10个字符" onkeyup="$.Huitextarealength(this,500)"></textarea>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">收入金额：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="收入金额" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">分成金额：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="real_money" placeholder="分成金额" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">分成比率：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="rate" placeholder="分成比率" name="username" id="username">
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