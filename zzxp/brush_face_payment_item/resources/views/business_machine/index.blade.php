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
        sendData('{{url('admin/business-machine/edit')}}','id='+id,function(obj){
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
        send_request('{{url('admin/business-machine/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/business-machine/update')}}',get_form_data(id),function(msg){
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
            return show_message.alert('没有选中任何记录,不能解绑');
        }
        show_message.confirm('您确定要解绑吗？',function(){
            send_request('{{url('admin/business-machine/del')}}',query.join('&'),function(msg){
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
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">参数配置</a><span class="c-999 en">&gt;</span>行业配置管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>

            <input type="text" name="name" style="width:126px" class="input-text" value="{{isset($search['name']) ? $search['name'] :'' }}" placeholder="商户姓名" />
            <input type="text" name="phone" style="width:126px" class="input-text" value="{{isset($search['phone']) ? $search['phone'] :'' }}" placeholder="商户电话" />
            <input type="text" name="shortName" style="width:126px" class="input-text" value="{{isset($search['shortName']) ? $search['shortName'] :'' }}" placeholder="商户店名" />
            <input type="text" name="sys_code" style="width:126px" class="input-text" value="{{isset($search['sys_code']) ? $search['sys_code'] :'' }}" placeholder="设备号" />
            
            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        商户设备信息
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">解绑</button>
    </div>
    <form name="delform" action="{{ url('admin/business_machine/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>商户姓名</th>
            <th>商户电话</th>
            <th>商铺名称</th>
            <th>设备号</th>
            <th>设备类型</th>
            <th>绑订时间</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($business_machine as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>
                
                <td>{{$line['name']}}</td>
                <td>{{$line['phone']}}</td>
                <td>{{$line['shortName']}}</td>
                <td>{{$line['sys_code']}}</td>
                <td>{{$line['type'] == 1 ? '支付宝蜻蜓' : '其他设备'}}</td>
                <td>{{$line['created_at']}}</td>

                     
            </tr>
          <?php } ?>
        </tbody>
      </table>
        @include('layouts.page')
    </div>
    </form>
  </div>

</section>



<style type="text/css">
</style>
@stop