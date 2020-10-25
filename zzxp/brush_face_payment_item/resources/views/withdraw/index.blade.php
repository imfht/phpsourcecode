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
        sendData('{{url('admin/withdraw/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/withdraw/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/withdraw/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/withdraw/del')}}',query.join('&'),function(msg){
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
    function exportData(){
        // alert('xxx');
        $('#search').attr('action',"{{url('admin/withdraw/export')}}");
        document.getElementById('search').submit();
        $('#search').removeAttr('action');
    }
    function uploadFile(){
        $('#search').attr('action',"{{url('admin/withdraw/import')}}");
        $('#search').attr('method',"POST");
        $('#search').attr('enctype',"multipart/form-data");
        document.getElementById('search').submit();
        $('#search').removeAttr('action');
        $('#search').removeAttr('method');
        $('#search').removeAttr('enctype');


    }
</script>
<style type="text/css">
    .upload_file{
        border: 1px solid #000;
        position: absolute;
        background: #fff;
        height: 36px;
        width: 60px;
        cursor: pointer;
        opacity:0;
        filter:alpha(opacity=0);
    }

</style>
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">提现记录</a><span class="c-999 en">&gt;</span>提现记录管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form id="search">

            <span class="select-box inline">
            <select name="status" class="select">
              <option value="">请选择提现状态</option>
              <option value="0" {{isset($search['status']) && $search['status'] === '0' ? 'selected' :'' }}>待付款</option>
              <option value="1" {{isset($search['status']) && $search['status'] == 1 ? 'selected' :'' }}>已付款</option>
            </select>
            </span>

            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""   id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
            <a  onclick="exportData()" class="btn btn-warning radius">导&nbsp;出</a>
            <input type="file" name="import" class="upload_file" onchange="uploadFile()" />
            <a   class="btn btn-success radius">导&nbsp;入</a>

        </form>

    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        用户
    </div>
    <form name="delform" action="{{ url('admin/withdraw/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>会员信息</th>
                <th>提现金额</th>
                <th>实际到账金额</th>
				<th>手续费</th>
				<th class="clo1">状态</th>
				<th>说明</th>
				<th class="col2">创建时间</th>
				<th class="col2">更新时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($withdraw as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>
                    电话:{{$line['phone']}}<br />
                    姓名:{{$line['name']}}<br />
                    支付宝姓名:{{$line['ali_name']}}<br />
                    支付宝账号:{{$line['ali_account']}}<br />
                </td>
                <td>{{$line['total_money']}}</td>
                <td>{{$line['money']}}</td>
				<td>{{$line['fee']}}</td>
				<td class="col1"><?php $filed=[0=>'待付款',1=>'已付款']; ?>{{isset($filed[$line['status']]) ? $filed[$line['status']] : $line['status']}}</td>
				<td>{{$line['remark']}}</td>
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
        <h3 class="modal-title">信息修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
		<div class="row cl">
                          <label class="form-label col-xs-3">提现金额：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="提现金额" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="status"><option value="0">待付款</option>
					<option value="1">已付款)</option></select>
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