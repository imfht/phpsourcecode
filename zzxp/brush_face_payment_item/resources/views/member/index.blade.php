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
        sendData('{{url('admin/member/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/member/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/member/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/member/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">代理商管理</a><span class="c-999 en">&gt;</span>代理商管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="会员表编号" />
            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="电话" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        代理商列表
    </div>
    <form name="delform" action="{{ url('admin/member/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>姓名</th>
    				<th>电话</th>
    				<th class="clo1">冻结状态</th>
    				<th>级别</th>
    				<th>昵称</th>
    				<th>推荐人信息</th>    				
            <th>金额信息</th>
            <th>团队信息</th>
            <th>身份证号</th>
    				<th>提现信息</th>
    				<th class="col2">创建时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($member as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>{{$line['name']}}</td>
					<td>{{$line['phone']}}</td>
					<td class="col1"><?php $filed=[0=>'否',1=>'是)']; ?>{{isset($filed[$line['type']]) ? $filed[$line['type']] : $line['type']}}</td>
					<td>{{$line['level_name']}}</td>
					<td>{{$line['display_name']}}</td>
					<td>
            姓名:{{$line['introduce_name']}}<br />
            电话:{{$line['introduce_phone']}}<br />
          </td>
					<td>
					    总收入{{$line['total_money']}}<br />
              分润收入:{{$line['sale_money']}}<br />
              可提现收入:{{$line['money']}}<br />
              推荐收入:{{$line['introduce_money']}}<br />
          </td>
					<td>
          高级合伙人数：{{$line['gold_user']}}</br>
          设备数:{{$line['machine_user']}}</br>
					商户总数:{{$line['business_user']}}</br>
          </td>

					<td>{{$line['idcard']}}</td>
          <td>
            支付宝姓名:{{$line['ali_name']}}<br />
            支付宝姓名:{{$line['ali_account']}}<br />
          </td>
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

<div id="password-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">会员表修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
              <label class="form-label col-xs-3">电话：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="phone" placeholder="电话" >
              </div>
            </div>
            <div class="row cl">
              <label class="form-label col-xs-3">密码：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="password" placeholder="密码" >
              </div>
            </div>
            <div class="row cl">
              <label class="form-label col-xs-3">重复密码：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="re_password" placeholder="重复密码" >
              </div>
            </div>

          </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="sure_add('password-demo')">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
</div>

</div>

<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">会员表修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id" />
            <div class="row cl">
                <label class="form-label col-xs-3">姓名：</label>
                <div class="formControls col-xs-8">
                  <input type="text" class="input-text" name="name" placeholder="姓名" >
                </div>
            </div>
		        <div class="row cl">
                <label class="form-label col-xs-3">电话：</label>
                <div class="formControls col-xs-8">
                  <input type="text" class="input-text" name="phone" placeholder="电话" >
                </div>
            </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">级别：</label>
                          <div class="formControls col-xs-8">
                            <!-- <input type="text" class="input-text" name="level_id" placeholder="级别" > -->
                            <span class="select-box inline">
                              <select name="level_id" class="select">
                                <option value="0">请选择级别</option>
                                <option value="1">普通合伙人</option>
                                <option value="2">高级合伙人</option>
                                <option value="3">钻石合伙人</option>
                                <option value="4">黑钻合伙人</option>
                              </select>
                              
                            </span>
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">推荐人编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="introduce_id" placeholder="推荐人编号" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">销售收入：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="sale_money" placeholder="销售收入" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">可提现金额：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="可提现金额" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">团队高级合伙人总数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="gold_user" placeholder="团队高级合伙人总数" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">团队钻石合伙人总数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="pt_user" placeholder="团队钻石合伙人总数" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">团队总设备数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="machine_user" placeholder="团队总设备数" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">商户总数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="business_user" placeholder="商户总数" >
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">身份证号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="idcard" placeholder="身份证号" >
                          </div>
                        </div>

    <div class="row cl">
                          <label class="form-label col-xs-3">支付宝姓名：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="ali_name" placeholder="支付宝姓名" >
                          </div>
                        </div>

    <div class="row cl">
                          <label class="form-label col-xs-3">支付宝账号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="ali_account" placeholder="支付宝账号" >
                          </div>
                        </div>
		<div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                            <span class="select-box inline">
                              <select name="is_status" class="select">
                                  <option value="0">未处理</option>
      					                  <option value="1">已处理</option>
                               </select>
                            </span>
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