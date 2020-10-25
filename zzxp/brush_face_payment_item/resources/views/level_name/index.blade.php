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
        sendData('{{url('admin/level-name/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                setDefaultValue(obj,'modal-demo');
                $("#modal-demo").modal("show");
            }
        },'GET');
    }  
    function setSale(id){
        global_submit_bool = false;
        sendData('{{url('admin/level-name/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                var rate = JSON.parse(obj.rate);

                setDefaultValue(obj,'setSale');
                $("#setSale").modal("show");
            }
        },'GET');

    }
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        send_request('{{url('admin/level-name/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/level-name/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/level-name/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">参数配置</a><span class="c-999 en">&gt;</span>级别管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="级别编号" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        级别
        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/level_name/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th class="col2">名称</th>
            <th>技术服务费</th>
            <th>购机价格</th>
            <th width="150">发展会员条件</th>
            <th width="100">权益信息</th>
    				<th width="200">提成比例</th>
    				<th class="col2">创建时间</th>
    				<th class="col2">更新时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($level_name as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>

                <td>{{$line['name']}}</td>
                <td>{{$line['price']}}</td>
                <td>{{$line['sale_machine']}}</td>
      					<td>
                  直推高级合伙人:{{$line['introduce_gold']}}<br />
                  直推钻石合伙人:{{$line['introduce_pt']}}<br />
                  团队总高级合伙人数:{{$line['team_gold']}}<br />
                  团队安装设备数:{{$line['team_machine']}}<br />
                </td>
      					<td>
                  直推收益:{{$line['introduce_money']}}<br />
                  平级分润:{{$line['same_rate']}}<br />
                </td>
                <td>
                  <?php
                    $rate = json_decode($line['rate'],true);
                    foreach ($rate as $cdata) {
                      # code...
                      foreach ($cdata['rate'] as $value) {
                        if(!empty($value['type'])){
                          $name = $value['type'] == 1 ? '特惠分润' : '正常分润';
                          $remark = $name.'分润:万'.round(10000*$value['rate']);
                          echo $remark."<br />";
                        }else{
                          $name = $cdata['name'];
                          $remark = $name.'分润:万'.round(10000*$value['rate']);
                          echo $remark."<br />";
                        }
                      }
                    }
                  ?>

                </td>
      					<td class="col2">{{$line['created_at']}}</td>
      					<td class="col2">{{$line['updated_at']}}</td>

                @if(isset($line['id']))
                <td class="col1" width="6%">
                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}});">修改</a>
                   <!-- <a href="#" class="btn btn-warning radius" onclick="setSale({{$line['id']}});">分润规则设置</a> -->
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
<div id="set-sale" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">分润规则设置</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div id="sale_line">
              
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

<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">级别修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
                          <label class="form-label col-xs-3">排序：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="orders" placeholder="排序">
                          </div>
                        </div>
    <div class="row cl">
                          <label class="form-label col-xs-3">等级名称：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="name" placeholder="名称">
                          </div>
                        </div>
    <div class="row cl">
                          <label class="form-label col-xs-3">技术服务费：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="price" placeholder="技术服务费">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">直推高级合伙人：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="introduce_gold" placeholder="直推高级合伙人">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">直推钻石合伙人：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="introduce_pt" placeholder="直推钻石合伙人">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">团队总高级合伙人数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="team_gold" placeholder="团队总高级合伙人数">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">团队安装设备数：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="team_machine" placeholder="团队安装设备数">
                          </div>
                        </div>
	<!-- 	<div class="row cl">
                          <label class="form-label col-xs-3">购机价格：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="sale_machine" placeholder="购机奖励">
                          </div>
                        </div> -->
		<div class="row cl">
                          <label class="form-label col-xs-3">直推收益：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="introduce_money" placeholder="直推收益">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">平级分润：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="same_rate" placeholder="平级分润">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">分润规则：</label>
                          <div class="formControls col-xs-8">

                            <textarea name="rate" cols="50" rows="10" placeholder="分润规则"></textarea>
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