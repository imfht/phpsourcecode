@extends('layouts.common')

@section('content')

<script type="text/javascript">
    var global_submit_bool = true;

    var url = "{{url('admin/article/upload')}}";
    function set_func(){
        global_submit_bool = true;
        $('#modal-demo input[name]').val('');
        $('#modal-demo select[name]').val('');
        $('#modal-demo textarea[name]').val('');
        $("#modal-demo").modal("show");
    }
    function edit_func(id,view_id){
        global_submit_bool = false;
        sendData('{{url('admin/business/edit')}}','id='+id,function(obj){
            if(typeof obj == 'object'){
                console.log(obj);
                setDefaultValue(obj,view_id);
                $("#"+view_id).modal("show");
                if(typeof obj.bupLoadFile != 'undefined'){
                  if(obj.bupLoadFile.indexOf('http') != 0){
                    obj.bupLoadFile = 'https://www.fengkouwl.com/' + obj.bupLoadFile;
                  }
                  $('#bupLoadFile').html('<input type="hidden" name="bupLoadFile" value="'+obj.bupLoadFile+'" /> <img src="'+obj.bupLoadFile +'" width="200" />');
                }
            }
        },'GET');
    }


    function upload_file(obj,name){
        document.myform.action = url;
        document.myform.target = 'uploaded';
        document.myform.actions.value = obj;

        $('input[name="input_name"]').val(name);
        //parent.show_wait(1);
        document.myform.submit();
    }

    function sure_add(id){
        if(!global_submit_bool){
            return sure_edit(id);
        }
        send_request('{{url('admin/business/add')}}',get_form_data(id),function(msg){
            if(msg != 1){
                show_message.alert(msg);
            }else{
                location.reload();
            }
        },function(){

        })
    }

    function sure_edit(id){
        send_request('{{url('admin/business/update')}}',get_form_data(id),function(msg){
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
            send_request('{{url('admin/business/del')}}',query.join('&'),function(msg){
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
    function setWxPayCode(img){
      $('#wx_code').attr('src',img);
      $("#wx-code").modal("show");
    }
    function setAliCode(id){
        sendData('{{url('admin/business/pay-qrcode')}}','bid='+id,function(obj){
            if(typeof obj == 'object'){
                console.log(obj);
                $('#ali_code').attr('src',obj.path+'?'+Math.random());
                $("#ali-code").modal("show");
            }
        },'GET');
    }
    function setWxPay(){

    }
    function setAliPay(){

    }
    function getAli(id){
        sendData('{{url('admin/business/get-file')}}','bid='+id,function(obj){
            if(typeof obj == 'object'){
                // console.log(obj);
                // window.open(obj.path,'_blank');
                location.href = obj.path;
            }
        },'GET');

    }
    function toOrder(obj){
      parent.Hui_admin_tab(obj);
    }
</script>
<style type="text/css">
  .col1 a{margin-top: 5px;}
</style>
<section >
    <nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> <a class="maincolor" href="/">商户管理</a><span class="c-999 en">&gt;</span>商户注册表管理<span class="c-999 en">&gt;</span><span class="c-666">列表</span></nav>
    <div class="text-c mt-20">
        <form>

            <input type="text" name="id" style="width:126px" class="input-text" value="{{isset($search['id']) ? $search['id'] :'' }}" placeholder="商户注册表编号" />

            日期范围：
            <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'logmax\')||\'%y-%M-%d\'}'})" id="logmin"  name="start_time" class="input-text Wdate" style="width:120px;" value={{isset($search['start_time']) ? $search['start_time'] :'' }}>
            -
            <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'logmin\')}',maxDate:'#now'})" id="logmax" name="end_time" class="input-text Wdate" style="width:120px;" value={{isset($search['end_time']) ? $search['end_time'] :''}}>
            <button name=""  id="" class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i>&nbsp;查&nbsp;询</button>
        </form>
    </div>
    <div class="panel panel-default mt-20">


    <div class="panel-header">
        商户注册表
    </div>
    <form name="delform" action="{{ url('admin/business/del')}}" method="post">
    <div class="panel-body">
      <table class="table table-border table-bordered table-striped mt-20">
        <thead>
          <tr>
            <th class="col1" width="6%"><input type="checkbox" /></th>
            <th class="col1">编号</th>
            <!-- <th>会员姓名</th>
            <th>会员电话</th> -->
            <th>商户姓名</th>
            <th>审核状态</th>
            <th>微信状态</th>
            <th>商户电话</th>
            <th>商户名称</th>
            <th>商户设备号</th>
            <th>所属行业</th>
            <th>微信升级状态</th>
            <th>收款类型</th>
            <th class="col2">签约时间</th>  
            <th width="200">签约错误信息</th>

            <th class="col1" align="center" >操作</th>
          </tr>
        </thead>
        <tbody>

            <?php
            $index = 1;
            foreach($business as $line){ $index ++; ?>
            <tr>
                <td class="col1"><input type="checkbox" value="{{ $line['id'] }}" name="ids[]" /></td>
                <td class="col1">{{ $line['id']}}</td>

                <!-- <td>{{$line['name']}}</td>
                <td>{{$line['phone']}}</td> -->
                <td>{{$line['bankAccName']}}</td>
                <td>{{$line['state'] == 0 ? '审核中':'已审核'}}</td>
                <td>{{$line['status'] == 1 ? '微信入件申请中':($line['status'] == 2 ? (empty($line['wx_mch_id']) ? '待签约' : '已完成签约') :'待入件')}}</td>
                <td>{{$line['contactPhone']}}</td>
                <td>{{$line['rname']}}</td>
                <td>{{$line['sys_code']}}</td>
                <td>{{$line['bname']}}</td>

                <td class="col1">
                  <?php $filed=[0=>'未申请',1=>'待升级资料审核',2=>'升级资料审核中',3=>'待签约',4=>'申请失败',5=>'成功升级']; ?>
                  {{isset($filed[$line['upgrade_status']]) ? $filed[$line['upgrade_status']] : $line['upgrade_status']}}
                  @if($line['upgrade_status'] == 4)
                  <br /> 失败原因:{{$line['error_desc']}}
                  @endif
                </td>

                <td>{{$line['is_sale'] == 1 ? '直收': '第三方平台代收'}}</td>
                <td class="col2">{{$line['add_time']}}</td>
                <td>{{$line['error']}}</td>

                @if(isset($line['id']))
                <td class="col1" width="6%">
                  @if(!empty($line['wx_sign_url']))
                   <a href="#" class="btn btn-warning radius" onclick="setWxPayCode('{{urldecode($line['wx_sign_url'])}}');">微信扫码配置</a>  
                  @endif

                  @if($line['upgrade_status'] == 3)    
                   <a href="#" class="btn btn-warning radius" onclick="setWxPayCode('{{urldecode($line['wx_sign_url'])}}');" >微信升级扫码签约</a> 
                  @endif


                  @if($line['upgrade_status'] == 0)    
                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}},'wx-apply');" >微信升级</a> 
                  @endif


                  @if($line['status'] == 2)    
                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}},'wx-pay');">微信配置</a> 
                  @endif

                   <a href="#" class="btn btn-warning radius" onclick="getAli({{$line['id']}});">支付宝进件资料</a> 

                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}},'ali-pay');">支付宝配置</a> 


                  
                  @if(!empty($line['ali_appid']))
                  <a href="#" class="btn btn-warning radius" onclick="setAliCode({{$line['id']}});">支付宝授权二维码</a>
                  @endif



                  @if($line['state'] == 0)


                   <a href="#" class="btn btn-warning radius" onclick="edit_func({{$line['id']}},'modal-demo');">审核</a> 
                  @else  

                   <a data-href="{{url('admin/sale-order/index?bid='.$line['id'])}}" data-title="销售订单列表" onclick="toOrder(this)" class="btn btn-warning radius">查看订单记录</a> 
                  @endif
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


<div id="wx-apply" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">商户微信升级资料确认及修改</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate" name="myform"  enctype="multipart/form-data">
            <input type="hidden" value="" name="id">
            <input type="hidden" value="" name="apply" value="1">
            <input type="hidden" value="" name="input_name" value="bupLoadFile">
            <input type="hidden" name="actions" value="file" />
            

            <div class="row cl">
              <label class="form-label col-xs-3">法人姓名:</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="legalPerson" placeholder="法人姓名" >
              </div>
            </div>


            <div class="row cl">
              <label class="form-label col-xs-3">营业执照的商户名称：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="shortName" placeholder="商户名称" >
              </div>
            </div>
            <div class="row cl">
              <label class="form-label col-xs-3">营业执照号：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="bno" placeholder="营业执照号" >
              </div>
            </div>

            <div class="row cl">
              <label class="form-label col-xs-3">营业执照有效期：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="licenceExp" placeholder="营业执照有效期(如:2018-08-08,长期)" >
              </div>
            </div>
           
            <div class="row cl">
              <label class="form-label col-xs-3">营业执照图片：</label>
              <div class="formControls col-xs-8">
                <input type="file" name="file" onchange="upload_file('file','bupLoadFile')" class="text_input middle_length"  />
                <div id="bupLoadFile"></div>
              </div>
            </div>


            <div class="row cl">
              <label class="form-label col-xs-3">费率结算规则:</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="scanRate" placeholder="费率结算规则" >
              </div>
            </div>


          </form>
      </div>
      <div class="modal-footer">

        <button class="btn btn-primary" onclick="sure_edit('wx-apply')">确认申请</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>

<div id="wx-code" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">商户微信入件确认</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
           
            <div class="row cl" style="text-align:center">
              请让商户通过微信扫描以下二维码进行入件操作
            </div>

            <div class="row cl"style="text-align:center">
              <img src="" id="wx_code" width="300" />
            </div>
                
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>
<div id="ali-code" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">商户支付宝入件确认</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
           
            <div class="row cl" style="text-align:center">
              请让商户通过支付宝扫描以下二维码进行入件操作
            </div>
            </div>

            <div class="row cl" style="text-align:center">
              <img src="" id="ali_code" width="300" />
            </div>
                
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>

<div id="wx-pay" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">微信配置</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
              <label class="form-label col-xs-3">微信商户ID：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="wx_mch_id" placeholder="微信商户ID" >
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="sure_edit('wx-pay')">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>

<div id="ali-pay" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">商户支付宝配置</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
            <div class="row cl">
              <label class="form-label col-xs-3">支付宝商户ID：</label>
              <div class="formControls col-xs-8">
                <input type="text" class="input-text" name="ali_appid" placeholder="支付宝商户ID" >
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="sure_edit('ali-pay')">确定</button>
        <button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
      </div>
    </div>
  </div>
</div>
<div id="modal-demo" class="modal fade middle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content radius">
      <div class="modal-header">
        <h3 class="modal-title">商户审核</h3>
        <a class="close" data-dismiss="modal" aria-hidden="true" href="javascript:void();">×</a>
      </div>
      <div class="modal-body">

          <form action="" method="post" class="form form-horizontal responsive" id="demoform" novalidate="novalidate">
            <input type="hidden" value="" name="id">
           
   <!--  <div class="row cl">
                      <label class="form-label col-xs-3">收款平台：</label>
                      <div class="formControls col-xs-8">
                        <span class="select-box inline">
                          <select name="is_sale" class="select">
                            <option value="1">直收</option>
                            <option value="2">第三方平台代收</option>
                          </select>
                        </span>
                      </div>
                    </div> -->

    <div class="row cl">
                      <label class="form-label col-xs-3">审核状态:</label>
                      <div class="formControls col-xs-8">
                        <span class="select-box inline">
                          <select name="state" class="select">
                            <option value="0">审核中</option>
                            <option value="1">已审核</option>
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

<div style="display:none">
    <iframe name="uploaded"></iframe>
</div>

<style type="text/css">
</style>
@stop