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
        sendData('{{url('admin/goods_car/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/goods_car/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/goods_car/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/goods_car/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">购物车</a><span class="c-999 en">&gt;</span>购物车管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>
            <input type="text" name="car_no" style="width:250px" class="input-text" value="" placeholder="车牌号" />
            <input type="text" name="name" style="width:250px" class="input-text" value="" placeholder="司机姓名" />
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        用户

        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/goods_car/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>商品编号</th>
				<th>会员编号</th>
				<th>数量</th>
				<th>价格</th>
				<th class="col2">创建时间</th>
				<th class="col2">更新时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($goods_car as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>{{$line['goods_id']}}</td>
					<td>{{$line['member_id']}}</td>
					<td>{{$line['number']}}</td>
					<td>{{$line['price']}}</td>
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
                          <label class="form-label col-xs-3">商品编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="goods_id" placeholder="商品编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">会员编号：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="member_id" placeholder="会员编号" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">数量：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="number" placeholder="数量" name="username" id="username">
                          </div>
                        </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">价格：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="price" placeholder="价格" name="username" id="username">
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