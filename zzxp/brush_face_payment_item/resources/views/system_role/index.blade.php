@extends('layouts.common')

@section('content')
    <script type="text/javascript" src="{{ asset('lib/jquery/1.9.1/jquery.min.js') }}"></script>
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
            sendData('{{url('admin/system_role/edit')}}','id='+id,function(obj){
                if(typeof obj == 'object'){
                    setDefaultValue(obj,'modal-demo');
                    var data = obj.menu_list.split(',');
                    $.each($('input[name="menu_list[]"]'),function(i,n){
                        if(data.indexOf(n.value) >= 0){
                            n.checked = true;
                        }
                    })
                    $("#modal-demo").modal("show");

                    //将获取到的正确的select值丢到select中
                    $('.bigGrade').val(obj.grade);

                    //将车辆轨迹查询new的多选框隐藏(防止出现两个指向一个功能的复选框)
                    $('input[value=207]').parent().css("display","none");

                }
            },'GET');
        }

        function sure_add(id){
            if(!global_submit_bool){
                return sure_edit(id);
            }
            send_request('{{url('admin/system_role/add')}}',get_form_data(id),function(msg){
                if(msg != 1){
                    show_message.alert(msg);
                }else{
                    location.reload();
                }
            },function(){

            })
        }

        function sure_edit(id){
            send_request('{{url('admin/system_role/update')}}',get_form_data(id),function(msg){
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
                send_request('{{url('admin/system_role/del')}}',query.join('&'),function(msg){
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
        /*function check_all_menu(id,obj){
            $.each($('.menu_pid_'+id),function(i,n){
                n.checked = obj.checked;
            });
        }*/
        function check_all_menu(id,obj){
            $.each($('.menu_pid_'+id),function(i,n){
                n.checked = obj.checked;
            });
            //获取点击的多选框的relation
            var relations=$('input[value='+id+']').attr('relation');
            var relationsArr=[];
            relationsArr=relations.split(' ');
            for(var i=0;i<relationsArr.length;i++){
                //如果其中的relation值不为0，则进行关联勾选
                if(relationsArr[i]!=0){
                    $.each($('input[value='+relationsArr[i]+']'),function(i,n){
                        n.checked = obj.checked;
                    });
                }
            }
        }

        function new_check_all_menu(id,obj){
            $.each($('.menu_pid_'+id),function(i,n){
                n.checked = obj.checked;
            });
            //获取点击的多选框的relation
            var relations=typeof $('input[value='+id+']').attr('relation')!='undefined'?$('input[value='+id+']').attr('relation'):'undefined';
            var relationsArr=[];
            relationsArr=relations.split(' ');

            if(relations!='undefined'){
                for(var i=0;i<relationsArr.length;i++){
                    //如果其中的relation值不为0，则进行关联勾选
                    if(relationsArr[i]!=0){
                        $.each($('input[value='+relationsArr[i]+']'),function(i,n){
                            n.checked = obj.checked;
                        });
                    }
                }
            }


            //当勾选子权限时，顶级权限也勾选
            var topVal=obj.getAttribute('class').split('_')[2];
            $.each($('input[value='+topVal+']'),function(i,n){
                //alert(n.checked);alert(obj.checked);
                if(obj.checked==true){
                    n.checked = obj.checked;
                }
            });
        }

        {{--//更新缓存--}}
        {{--function updateCache(){--}}
            {{--$.ajax({--}}
                {{--url:"{{url('admin/system_role/updateCache')}}",--}}
                {{--type:'get'--}}
            {{--});--}}
        {{--}--}}

    </script>
    <section >
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 系统管理<span class="c-999 en">&gt;</span>角色表管理<span class="c-999 en"></span></nav>
        {{--<div class="text-c mt-20">
            <form>
                <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
            </form>
        </div>--}}
        <div class="panel panel-default mt-20">


            <div class="panel-header">
                <button class="btn btn-success radius" type="button" onclick="set_func()">增加</button>
                <button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>
                {{--<button class="btn btn-success radius" type="button" onclick="updateCache()">更新缓存</button>--}}
            </div>
            <form name="delform" action="{{ url('admin/system_role/del')}}" method="post">
                <div class="panel-body">
                    <table class="table table-border table-bordered table-striped mt-20">
                        <thead>
                        <tr>
                            <th class="col1" width="6%"><input type="checkbox" /></th>
                            <th class="col1">编号</th>
                            <th>角色</th>
                            <th>启用</th>
                            <th>创建者</th>
                            <th class="col2">创建时间</th>
                            <th class="col2">更新时间</th>
                            <th>等级</th>
                            <th class="col1" align="center" >操作</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $index = 1;
                        foreach($system_role as $line){ $index ++; ?>
                        <tr>
                            <td class="col1"><input type="checkbox" value="{{ $line['system_role_id'] }}" name="ids[]" /></td>
                            <td class="col1">{{ $line['system_role_id']}}</td>

                            <td>{{$line['name']}}</td>
                            <td>{{$line['status'] == 1 ? '启用':'不启用' }}</td>
                            <td>{{$line['creator']}}</td>
                            <td class="col2">{{$line['created_at']}}</td>
                            <td class="col2">{{$line['updated_at']}}</td>
                            <td>{{$line['grade']}}</td>

                            @if(isset($line['system_role_id']))
                                <td class="col1" width="6%">
                                    <a  class="btn btn-warning radius" onclick="edit_func({{$line['system_role_id']}});">修改</a>
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
    <style>
        .zheDie{font-weight:bold;color:#aaa;cursor:pointer;width:12px;height:20px;float:left;text-align:center;line-height:20px;}
    </style>
    <div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="height:100%;">
            <div class="modal-content radius">
                <div class="modal-header">
                    <h3 class="modal-title">信息修改</h3>
                    <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
                </div>
                <div class="modal-body">

                    <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
                        <input type="hidden" value="" name="system_role_id">
                        <div class="row cl">
                            <label class="form-label col-xs-3">角色：</label>
                            <div class="formControls col-xs-8">
                                <input type="text" class="input-text" name="name" placeholder="角色" name="username" id="username">
                            </div>
                        </div>


                        <?php if(Session::get('grade')==1){ ?>
                            <div class="row cl">
                                <label class="form-label col-xs-3">等级：</label>
                                <div class="formControls col-xs-8">
                                    <select class="bigGrade" name="grade">
                                        <option value="1">超级管理员</option>
                                        <option value="2">普通管理员</option>
                                    </select>
                                </div>
                            </div>
                        <?php }else{ ?>
                            <div class="row cl">
                                <label class="form-label col-xs-3">等级：</label>
                                <div class="formControls col-xs-8">
                                    <select name="grade">
                                        <option value="2">普通管理员</option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row cl">
                            <label class="form-label col-xs-3">状态：</label>
                            <div class="formControls col-xs-8">
                                <select name="status">
                                    <option value="1">启用</option>
                                    <option value="-1">禁用</option>
                                </select>
                            </div>
                        </div>
                        <div style="padding:5px; width:85%;margin:auto;">
                            @foreach($system_menu as $index => $menu)
                                @if($menu['menu_level'] == 0)
                                    @if($index > 0)
                                        <div style="clear:both;"></div>
                        </div>
                        @endif
                        <div style="background-color:#ccc;line-height:30px; padding-left:10px;">
                            <input type="checkbox" value="{{$menu['system_menu_id']}}" name="menu_list[]" onclick="check_all_menu({{$menu['system_menu_id']}},this)"> &nbsp; {{$menu['title']}}
                        </div>
                        <div>
                            @else
                                <div style="float:left; width:150px; ">
                                    <div class="zheDie">+</div><input type="checkbox" level="lv{{$menu['menu_level']}}" value="{{$menu['system_menu_id']}}" name="menu_list[]" relation="{{$menu['relation']}}" class="menu_pid_{{$menu['parent_id']}}" onclick="new_check_all_menu({{$menu['system_menu_id']}},this)">{{$menu['title']}} &nbsp;
                                </div>
                            @endif
                            @endforeach
                            <div style="clear:both;"></div>
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


    <script>

        //将所有二级(level=lv1)的复选框加灰色(增加层次感)
        $('input[level=lv1]').each(function(){
            $(this).parent().css('background','#eee');
        });

        //将所有三级(level=lv2)复选框移出视线。同时将三级(level=lv2)复选框前的+去掉
        $('input[level=lv2]').each(function(){
            $(this).prev().remove();
            $(this).parent().css({"position":"absolute","left":"-99999px"});
        });

        //点击+展开(回到视线)，同时变为-。再点击收起(移出视线)，同时变为+
        $('.zheDie').on('click',function(){
            //获取当前点击+号的下个元素的value
            var inputVal=$(this).next().val();
            if($(this).html()=='+'){
                //点击+变为-之前，将所有变为+
                $('.zheDie').each(function(){
                    $(this).html('+');
                });
                $(this).html('-');
                //展开前将所有三级(level=lv2)复选框移出视线
                $('input[level=lv2]').each(function(){
                    $(this).parent().css({"position":"absolute","left":"-99999px"});
                });
                $('.menu_pid_'+inputVal).each(function(){
                    $(this).parent().css({"position":"","left":""});
                });
            }else{
                $(this).html('+');
                $('.menu_pid_'+inputVal).each(function(){
                    $(this).parent().css({"position":"absolute","left":"-99999px"});
                });
            }
        });

        //点击二级(level=lv1)的复选框则自动将父级
//        $('input[level=lv1]').each(function(){
//            $(this).parent().css('background','#eee');
//        });
//        $('input[level=lv1]').on('click',function(){
//            var topVal=$(this).attr('class').split('_')[2];
//            //$('input[value='+topVal+']').attr("checked", "checked");
//
//            $.each($('input[value='+topVal+']'),function(i,n){
//                n.checked = $(this).checked;
//            });
//
//
//        });


    </script>

    <style type="text/css">
    </style>
@stop