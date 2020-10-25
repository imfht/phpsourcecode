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
        <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i>系统管理<span class="c-999 en">&gt;</span>操作日志</nav>
        <div class="text-c mt-20">
            <form>
                <input type="text" name="content_id" style="width:150px" class="input-text" value="{{$content_id}}" placeholder="被操作内容的id" />
                <input type="text" name="operation_name" style="width:150px" class="input-text" value="{{$operation_name}}" placeholder="管理员名" />
                <input type="text" name="controller" style="width:150px" class="input-text" value="{{$controller}}" placeholder="来自的模块" />

                操作类型： <span class="select-box inline">
                    <select name="operation_type" class="select">
                        <option value="" selected>全部</option>
                        <option value=1 @if($operation_type==1) selected @endif >增</option>
                        <option value=2 @if($operation_type==2) selected @endif >删</option>
                        <option value=3 @if($operation_type==3) selected @endif >改</option>
                        <option value=4 @if($operation_type==4) selected @endif >充值</option>
                        <option value=5 @if($operation_type==5) selected @endif >实名</option>
                    </select>
                </span>

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
            <form name="delform" action="{{ url('admin/system_log/del')}}" method="post">
                <div class="panel-body">
                    <table class="table table-border table-bordered table-striped mt-20">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>操作者id </th>
                            <th>来自的模块</th>
                            <th>操作类型</th>
                            <th>管理员名</th>
                            <th>操作内容的id</th>
                            <th>操作内容</th>
                            <th class="col2">创建时间</th>
                            <th class="col2">更新时间</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $index = 1;
                        foreach($systemLog as $line){ $index ++; ?>
                        <tr>
                            <td>{{$line['system_log_id']}}</td>
                            <td>{{$line['operation_id']}}</td>
                            <td>{{$line['controller']}}</td>
                            <td><?php $filed=[1=>'增',2=>'删',3=>'改',4=>'充值',5=>'实名']; ?>{{$filed[$line['operation_type']]}}</td>
                            <td>{{$line['operation_name']}}</td>
                            <td>{{$line['content_id']}}</td>
                            <td width="36%"><?php echo htmlspecialchars_decode($line['operation_remark']); ?></td>
                            <td>{{date('Y-m-d H:i:s',strtotime($line['created_at'])+28800)}}</td>
                            <td>{{date('Y-m-d H:i:s',strtotime($line['updated_at'])+28800)}}</td>




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
        <div class="modal-dialog" style="height:100%;">
            <div class="modal-content radius">
                <div class="modal-header">
                    <h3 class="modal-title">信息修改</h3>
                    <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
                </div>
                <div class="modal-body">
                    <input type="hidden" value="" name="id">
                    <div class="row cl">
                        <label class="form-label col-xs-3">所属企业编号：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="company_id" placeholder="所属企业编号" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_real">
                        <label class="form-label col-xs-3">电话：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="phone" placeholder="电话" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_real">
                        <label class="form-label col-xs-3">真实姓名：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="real_name" placeholder="真实姓名" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">昵称：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="display_name" placeholder="昵称" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_real">
                        <label class="form-label col-xs-3">身份证号码：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="idcard" placeholder="身份证号码" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_real">

                        <label class="form-label col-xs-3">实名图片：</label>
                        <div class="formControls col-xs-8">
                            <div class="up_load_file" style="display:none;" ></div>
                            <input type="hidden" id="upload_file_control" name="head_pic" class="text_input middle_length" />
                        </div>
                    </div>

                    <div class="row cl is_real" id="real_img">

                    </div>
                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">余额：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="余额" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">总充值金额：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="charge" placeholder="总充值金额" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">总赠送金额：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="present" placeholder="总赠送金额" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">总消费金额：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="consume" placeholder="总消费金额" name="username" id="username">
                        </div>
                    </div>

                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">充值金额额：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="money" placeholder="余额" name="username" id="username">
                        </div>
                    </div>

                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">充值说明：</label>
                        <div class="formControls col-xs-8">
                            <select name="remark">
                                <option value="充电补贴">充电补贴</option>
                                <option value="活动赠送">活动赠送</option>
                                <option value="停车费补贴">停车费补贴</option>
                                <option value="事故补贴">事故补贴</option>
                                <option value="">其他</option>
                            </select>
                        </div>
                    </div>


                    <div class="row cl is_money">
                        <label class="form-label col-xs-3">充值说明(其他情况请填写)：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="remark_other" placeholder="余额" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">保证金：</label>
                        <div class="formControls col-xs-8">
                            <input type="text" class="input-text" name="base_money" placeholder="保证金" name="username" id="username">
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">保证金状态：</label>
                        <div class="formControls col-xs-8">
                            <select name="base_status">
                                <option value="1">正常</option>
                                <option value="2">冻结</option>
                            </select>
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">拉黑状态：</label>
                        <div class="formControls col-xs-8">
                            <select name="status">
                                <option value="1">正常</option>
                                <option value="2">拉黑</option>
                            </select>
                        </div>
                    </div>
                    <div class="row cl">
                        <label class="form-label col-xs-3">性别：</label>
                        <div class="formControls col-xs-8">
                            <input type="radio" name="sex" placeholder="性别" value="1"/>男
                            <input type="radio" name="sex" placeholder="性别" value="2"/>女
                        </div>
                    </div>
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