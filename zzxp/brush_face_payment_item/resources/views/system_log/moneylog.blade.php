@extends('layouts.common')
@section('content')

    <script type="text/javascript">
        var global_submit_bool = 1;
        function set_func(){
            global_submit_bool = 1;
            $('#modal-demo input[name]').val('');
            $('#modal-demo select[name]').val('');
            $('#modal-demo textarea[name]').val('');
            $('#modal-demo .row').show();
            $('#modal-demo .is_real').hide();
            $('#modal-demo .is_money').hide();

            $("#modal-demo").modal("show");
        }
        function edit_func(id){
            global_submit_bool = 2;
            sendData('{{url('admin/member/edit')}}','id='+id,function(obj){
                if(typeof obj == 'object'){
                    setDefaultValue(obj,'modal-demo');
                    // console.log(obj.real_img);
                    if(obj.real_img.length > 0){
                        for(var i in obj.real_img){
                            $('#real_img').append('<img src="'+obj.real_img[i]+'" width=200 />')
                            $('#real_img').append('<input type="hidden"  value="'+obj.real_img[i]+'" name="real_img[]" />');
                        }
                    }
                    $('#modal-demo .row').show();
                    $('#modal-demo .is_real').hide();
                    $('#modal-demo .is_money').hide();
                    $("#modal-demo").modal("show");
                }
            },'GET');
        }
        function real_func(id){

            global_submit_bool = 3;
            sendData('{{url('admin/member/edit')}}','id='+id,function(obj){
                if(typeof obj == 'object'){
                    setDefaultValue(obj,'modal-demo');
                    if(obj.real_img.length > 0){
                        for(var i in obj.real_img){
                            $('#real_img').append('<img src="'+obj.real_img[i]+'" width=200 />')
                            $('#real_img').append('<input type="hidden"  value="'+obj.real_img[i]+'" name="real_img[]" />');
                        }
                    }
                    $('#modal-demo .row').hide();
                    $('#modal-demo .is_real').show();
                    $("#modal-demo").modal("show");
                }
            },'GET');

        }
        function money_func(id){

            global_submit_bool = 4;
            sendData('{{url('admin/member/edit')}}','id='+id,function(obj){
                if(typeof obj == 'object'){
                    setDefaultValue(obj,'modal-demo');
                    $('#modal-demo .row').hide();
                    // $('#modal-demo .is_real').show();
                    $('#modal-demo .is_money').show();
                    $("#modal-demo").modal("show");
                }
            },'GET');

        }
        function sure_add(id){
            if(global_submit_bool == 2){
                return sure_edit(id);
            }else if(global_submit_bool == 3){
                return sure_real(id);
            }else if(global_submit_bool == 4){
                return sure_money(id);
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
        function sure_real(id){
            send_request('{{url('admin/member/real')}}',get_form_data(id),function(msg){
                if(msg != 1){
                    show_message.alert(msg);
                }else{
                    location.reload();
                }
            },function(){});
        }
        function sure_money(id){
            send_request('{{url('admin/member/send-money')}}',get_form_data(id),function(msg){
                if(msg != 1){
                    show_message.alert(msg);
                }else{
                    location.reload();
                }
            },function(){});
        }
        function sure_edit(id){
            send_request('{{url('admin/member/update')}}',get_form_data(id),function(msg){
                if(msg != 1){
                    show_message.alert(msg);
                }else{
                    location.reload();
                }
            },function(){});
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
            },function(){});
        }

        function base_apply(id) {
            $.get('{{ url('admin/base_withdraw/apply') }}', {'id':id} ,function(msg) {
                if (msg != 1) {
                    show_message.alert(msg);
                } else {
                    show_message.alert('申请成功');
                }
            });
        }

        function excel_export()
        {
            //获取制定时间段
            var logmin = document.getElementById("logmin");
            var logmax=document.getElementById("logmax");
            //alert(logmin.value+' '+logmax.value); //获取

            var car_no = $("#car_no").val();
            layer.confirm("确认导出?",function (index){
                var url = "{{ url('/admin/member/export')}}";
                //url += '?car_no='+car_no;
                url += '?car_no='+car_no+'&start_time='+logmin.value+'&end_time='+logmax.value;
                window.open(url);
                layer.msg('导出成功', {icon: 1, time: 1000});
            });
        }

        //日期插件
        $("#datetimepicker").datetimepicker({
            format: 'yyyy-mm-dd',
            minView: "month",
            todayBtn:  1,
            autoclose: 1,
            endDate : new Date()
        }).on('hide',function(e) {
            //此处可以触发日期校验。
        });

    </script>
    <script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/lib/layer/2.4/layer.js"></script>
    {{--日期插件--}}
    <script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>

    <style> .img_box img{max-width:150px;max-height:150px;}</style>

    <section >
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>日志管理<span class="c-999 en">&gt;</span>{{$title}}</nav>
        <div class="text-c mt-20">
            <form>
                <input type="text" name="member_id" style="width:150px" class="input-text" value="{{$member_id}}" placeholder="会员ID" />
                {{--<input type="text" name="card_no" style="width:150px" class="input-text" value="{{$card_no}}" placeholder="卡号" />--}}
                日期范围：
                <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value="{{$start_time}}">
                -
                <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value="{{$end_time}}">
                <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
            </form>
        </div>
        <div class="panel panel-default mt-20">


            <div class="panel-header">
                {{--<button class="btn btn-danger radius" type="button" onclick="del_sub()">删除</button>--}}

                {{--<button style="" class="btn btn-success radius" type="button" onclick="excel_export();">导出</button>--}}
            </div>
            <form name="delform" action="{{ url('admin/system_log/moneylog')}}" method="post">
                <div class="panel-body">
                    <table class="table table-border table-bordered table-striped mt-20">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>会员信息</th>
                            <th>影响金额</th>
                            <th>操作后余额</th>
                            <th>操作类型</th>
                            <th>操作备注</th>
                            <th class="col2">创建时间</th>
                            <th class="col2">更新时间</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $index = 1;
                        foreach($systemLog as $line){ $index ++; ?>
                        <tr>
                            <td>{{$line['id']}}</td>
                            <td>
                                会员编号:{{$line['member_id']}}<br />
                                会员昵称:{{$line['display_name']}}<br />
                                会员电话:{{$line['phone']}}<br />
                            </td>
                            <td>{{$line['effect_money']}}</td>
                            <td>{{$line['money']}}</td>
                            <td>{{$line['type'] == 1 ? '收入' : '支出'}}</td>
                            <td>{{$line['remark']}}</td>
                            <td>{{date('Y-m-d H:i:s',strtotime($line['created_at']))}}</td>
                            <td>{{date('Y-m-d H:i:s',strtotime($line['updated_at']))}}</td>
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
@section('scripts')
    <script type="text/javascript">

        $(function(){

            $('.up_load_file').uploadfile({
                url : '{{ url("testoss")}}',
                width : 50,
                height : 5,
                canDrag : true,
                canMultiple : true,
                success: function (fileName) {
                    alert(fileName + '上传成功');
                    $('#real_img').append('<img src="'+fileName+'" width=200 />')
                    $('#real_img').append('<input type="hidden"  value="'+fileName+'" name="real_img[]" />');

                },
                error:function(fileName){
                    alert(fileName + '上传失败');
                }

            });
        });



    </script>
@stop