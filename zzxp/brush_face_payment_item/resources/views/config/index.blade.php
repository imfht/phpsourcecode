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
    function edit_func(id,view_id){
        global_submit_bool = false;
        sendData('{{url('admin/config/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                console.log(obj);
                setDefaultValue(obj,view_id);
                $("#"+view_id).modal("show");
            }
        },'GET');
    }
    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        send_request('{{url('admin/config/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/config/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/config/del')}}',query.join('&'),function(msg){
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
    window.onload = function(){

        $('#ali-code').modal('show');
    }
</script>
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">配置</a><span class="c-999 en">&gt;</span>参数配置<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>

</section>


<div id="ali-code" class="modal fade middle" style="display:block">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">参数配置</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="{{isset($config['id']) ? $config['id'] : '' }}" name="id">
            <div class="row cl">
              <label class="form-label col-xs-3">提现参数-起提金额：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="min_money" value="{{isset($config['min_money']) ? $config['min_money'] : '' }}" placeholder="起提金额" >
              </div>
            </div>
            <div class="row cl">
              <label class="form-label col-xs-3">提现参数-封顶金额：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="max_money" value="{{isset($config['max_money']) ? $config['max_money'] : '' }}" placeholder="封顶金额" >
              </div>
            </div>

            <div class="row cl">
              <label class="form-label col-xs-3">提现参数-手续费：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="rate" value="{{isset($config['rate']) ? $config['rate'] : '' }}" placeholder="提现手续费%" >
              </div>
            </div>

            <div class="row cl">
              <label class="form-label col-xs-3">APP下载地址：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="app_download" value="{{isset($config['app_download']) ? $config['app_download'] : '' }}" placeholder="APP下载地址" >
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="sure_edit('ali-code')">修改</button>
      </div>
    </div>
  </div>
</div>




<style type="text/css">
</style>
@stop