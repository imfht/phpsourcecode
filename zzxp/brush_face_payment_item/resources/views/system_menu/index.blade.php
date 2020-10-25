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
        sendData('{{url('admin/system_menu/edit')}}','system_menu_id='+id,function(obj){
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
        send_request('{{url('admin/system_menu/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/system_menu/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/system_menu/del')}}',query.join('&'),function(msg){
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
    <style>
        .folds{width:12px;height:12px;font-size:16px;line-height:12px;text-align:center;cursor:pointer;/*border:1px solid black;*/}
        #shenSuo{border:1px solid #666;width:70px;height:20px;line-height:20px;text-align:center;cursor:pointer;}
    </style>
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 系统管理<span class="c-999 en">&gt;</span>权限表管理<span class="c-999 en"></span></nav>
    {{--<div class="text-c mt-20">
        <form>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>--}}
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
        <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
    </div>
    <form name="delform" action="{{ url('admin/system_menu/del')}}" method="post">
    <div class="panel-body">
      <div id="shenSuo">全部展开</div>
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <th>权限名</th>
			<th>菜单请求链接</th>
			<th class="clo1">状态</th>
            <th class="clo1">排序</th>
			<th class="col2">创建/更新时间</th>
            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($system_menu as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['system_menu_id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['system_menu_id']}}</td>
                <td>
                    <?php
                        for($i =0 ;$i<$line['menu_level'];$i++){
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                        }
                    ?>
                    <span class="folds" parent_id="{{$line['parent_id']}}" menu_level="{{$line['menu_level']}}" system_menu_id="{{$line['system_menu_id']}}">+</span>
                    <?php
                        echo $line['title'];
                    ?>
                </td>
      			<td>{{$line['action_url']}}</td>
      			<td class="col1"><?php $filed=[0=>'启用',1=>'关闭']; ?>{{isset($filed[$line['status']]) ? $filed[$line['status']] : $line['status']}}</td>
                <td>{{$line['orders']}}</td>
      			<td class="col2">创:{{$line['created_at']}}<br>更:{{$line['updated_at']}}</td>

                @if(isset($line['system_menu_id']))
                <td class="col1" width="6%">
                   <a  class="btn btn-warning radius" onclick="edit_func({{$line['system_menu_id']}});">修改</a>
                </td>
                @else
                <td></td>
                @endif          
            </tr>
          <?php } ?>
        </tbody>
      </table>
        {{--@include('layouts.page')--}}
    </div>
    </form>
  </div>

</section>
<script type="text/javascript" src="{{ asset('lib/jquery/1.9.1/jquery.min.js') }}"></script>

<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="height:100%;">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">信息修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a> 
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="system_menu_id">
            <div class="row cl">
                          <label class="form-label col-xs-3">名称：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="title" placeholder="权限名">
                          </div>
                        </div>
            <div class="row cl">
                          <label class="form-label col-xs-3">上级名称：</label>
                          <div class="formControls col-xs-8">
                            <select name="parent_id">
                                <option value="0">根目录</option>
                                @foreach($system_menu as $menu)
                                  @if($menu['menu_level'] < 2)
                                  <option value="{{$menu['system_menu_id']}}"> <?php for($i =0 ;$i<$menu['menu_level'];$i ++){echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';} echo str_pad($menu['title'],$menu['menu_level']*4,'&nbsp;'); ?></option>
                                  @endif
                                @endforeach
                              </select>
                          </div>
                        </div>
              <div class="row cl">
                  <label class="form-label col-xs-3">关联权限id(多个用空格隔开)：</label>
                  <div class="formControls col-xs-8">
                      <input type="text" class="input-text" name="relation" placeholder="菜单请求链接">
                  </div>
              </div>
		        <div class="row cl">
                          <label class="form-label col-xs-3">菜单请求链接：</label>
                          <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="action_url" placeholder="菜单请求链接" name="username" id="username">
                          </div>
                        </div>
		        <div class="row cl">
                      <label class="form-label col-xs-3">状态：</label>
                      <div class="formControls col-xs-8">
                        <select name="status"><option value="0">启用</option>
					               <option value="1">关闭</option></select>
                      </div>
                    </div>
		<div class="row cl">
                          <label class="form-label col-xs-3">菜单级别(总共三级)：</label>
                          <div class="formControls col-xs-8">
                              <select name="menu_level">
                                <option value="0">一级</option>
                                <option value="1">二级</option>
                                <option value="2">三级</option>
                              </select>
                          </div>
                        </div>
              <div class="row cl">
                  <label class="form-label col-xs-3">排序：</label>
                  <div class="formControls col-xs-8">
                      <input type="text" class="input-text" name="orders" placeholder="从小到大排序">
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

<script>
    //点击折叠按钮收缩或打开
    $('.folds').on('click',function(){
        //获取该点击按钮的system_menu_id
        var system_menu_id=$(this).attr('system_menu_id');
        var symbol=$(this).html();
        if(symbol=='+'){
            //将parent_id为该system_menu_id的元素显示出来
            $('span[parent_id='+system_menu_id+']').each(function(){
                $(this).parent().parent().show();
            });
            $(this).html('-');
        }
        if(symbol=='-'){
            //将parent_id为该system_menu_id的元素显示出来
            $('span[parent_id='+system_menu_id+']').each(function(){
                $(this).html('+');
                //将该级的所有下级搜索起来：
                //1.获取该等级下所有子并隐藏、将其-变成+
                $('span[parent_id='+$(this).attr('system_menu_id')+']').parent().parent().hide();
                //alert();return;
                //将该级收缩起来
                $(this).parent().parent().hide();
            });
            $(this).html('+');
        }
    });
    //等级(menu_level)为2的是-
    $('span[menu_level=2]').each(function(){
        $(this).html('');
        //去掉其绑定的事件
        $(this).unbind();
        $(this).css({"border":"0px"});
        //将该等级隐藏
        $(this).parent().parent().hide();
    });
    //把等级(menu_level)为1的权限折叠起来
    $('span[menu_level=1]').each(function(){
        //将该等级隐藏
        $(this).parent().parent().hide();
    });
    //点击伸缩按钮
    $('#shenSuo').on('click',function(){
        $('.folds').each(function(){
            $(this).parent().parent().show();
            if($(this).html()=='+'){
                $(this).html('-');
            }
        });
    });

</script>
@stop