<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>长盛车品订单打印</title>
    <meta name="description" content="这是一个 index 页面">
    <meta name="keywords" content="index">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="icon" type="image/png" href="http://www.xhjh9999.cn/assets/i/favicon.png">
    <link rel="apple-touch-icon-precomposed" href="http://www.xhjh9999.cn/assets/i/app-icon72x72@2x.png">
    <meta name="apple-mobile-web-app-title" content="Amaze UI" />
    <script src="http://www.xhjh9999.cn/assets/js/echarts.min.js"></script>
    <link rel="stylesheet" href="http://www.xhjh9999.cn/assets/css/amazeui.min.css" />
    <link rel="stylesheet" href="http://www.xhjh9999.cn/assets/css/amazeui.datatables.min.css" />
    <link rel="stylesheet" href="http://www.xhjh9999.cn/assets/css/app.css">
    <script src="http://www.xhjh9999.cn/assets/js/jquery.min.js"></script>
    <script src="http://www.xhjh9999.cn/js/common.js?v=0.0.0.7"></script>
    <script src="http://www.xhjh9999.cn/js/view.js?v=0.0.0.5"></script>
    <script src="http://www.xhjh9999.cn/js/models/customer_out_order_edit.js?v=0.0.0.5"></script>
    <style type="text/css">
    th{vertical-align: inherit;border-top:1px solid #000;border-left:1px solid #000;text-align: center;padding: 3px; font-size: 12px;}
    td{vertical-align: inherit;border-top:1px solid #000;border-left:1px solid #000;text-align: center;padding: 3px;}
    table{border-right:1px solid #000;border-bottom:1px solid #000;}
    h2{text-align: center;}
    .botoom_right{line-height: 28px; text-align: right;padding: 5px; font-size: 12px;}
    .botoom_right u{padding-left: 15px; font-size: 12px;}
    .bottom_text{float: left; font-size: 12px;}
    .bottom_text p{line-height: 23px;margin: 0px; font-size: 12px;};
    .header_title{width: 100%;padding: 5px; font-size: 12px;}
    .header_title .header_line{float:left;padding: 5px 10px; font-size: 12px;}
    .header_title .header_line_r{float:right; font-size: 12px;}
    .header_title u{padding: 0px 5px; font-size: 12px;}
    .gradeX td{font-size: 12px;}
    tfoot td{font-size: 12px;}
     
    @media print{
     /*CSS-Code;*/
     .noprint{display:none}
    }
    </style>
</head>

<body data-type="index" class="theme-white">
    <div class="am-g tpl-g">
        <!-- 内容区域 -->
        <!-- 内容区域 -->
        <div class="am-cf">
            <div class="row">
                <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                    <div class="widget am-cf" style="padding:13px 0px;" >
                        <div class="widget-head am-cf noprint">
                            <div class="widget-title  am-cf">打印订单</div>
                        </div>
                        <div class="widget-body" style="padding:13px 0px;" >
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-6 noprint">
                                <div class="am-form-group">
                                    <div class="am-btn-toolbar">
                                        <div class="am-btn-group am-btn-group-xs">
                                            <button type="button" id="print_button" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 打印</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="am-u-sm-12">
                                <h2><u style="font-size:24px;">&nbsp;长盛车品&nbsp;</u>订单</h2>
                                <div class="header_title">
                                    <div class="header_line">客户:<u>&nbsp; &nbsp; &nbsp; {{$orders['member_name']}}&nbsp; &nbsp; &nbsp; </u></div>
                                    <div style="clear:both;"></div>
                                </div>
                                <div class="header_title">
                                    <div class="header_line">客户编号:<u>&nbsp; &nbsp; &nbsp; {{$orders['member_id']}}&nbsp; &nbsp; &nbsp; </u></div>
                                    <div style="clear:both;"></div>
                                </div>
                                <div class="header_title">
                                    
                                    <div class="header_line">备注:<u>{{ !empty($orders['remark']) ? $orders['remark'] : '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;'}}</u></div>
                                    <div class="header_line">打印日期:<u>&nbsp; {{date('Y-m-d H:i:s')}}&nbsp; </u></div>
                                    <!-- <div class="header_line">制单人:<u>&nbsp; {{$goods_price['opera_user']}}&nbsp; </u></div> -->
                                    <div class="header_line_r">编号:<u>&nbsp; {{empty($orders['order_sn']) ? date('YmdHis').rand(100,999) : $orders['order_sn']}}&nbsp; </u></div>
                                    <div style="clear:both"></div>
                                </div>

                                <table width="100%" class=" " id="example-r">
                                    <thead>
                                        <tr>
                                            <th >商品名称</th>
                                            <th >商品规格</th>
                                            <th >商品车型</th>
                                            <th >商品数量</th>
                                            <th colspan="2">备注</th>
                                        </tr>
                                       
                                    </thead>
                                    <tbody>
                                        @foreach($order_info as $info)
                                        <tr class="gradeX">
                                            <td>{{$info['goods_title']}}</td>
                                            <td>{{$info['type_name']}}</td>
                                            <td>{{$info['car_brand']}}</td>
                                            <td>{{$info['number']}}</td>
                                            <!-- <td>{{$goods['count_price']}}</td> -->
                                            <td colspan="2">{{ !empty($info['remark']) ? $info['remark'] : '&nbsp;'}}</td>
                                         
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>                                        
                                        <td>收货人</td>
                                        <td colspan="1">{{$orders['name']}}</td>
                                        <td>收货电话</td>
                                        <td colspan="3">{{$orders['phone']}}</td>
                                    </tr>
                                    <tr>
                                        <td>收货地址</td>
                                        <td colspan="5">{{$orders['phone']}}</td>
                                    </tr>
                                    <tr>
                                        <td>发货时间:</td>
                                        <td colspan="1">{{$orders['express_time']}}</td>
                                        <td>快递公司:</td>
                                        <td colspan="1">{{$orders['express_name']}}</td>
                                        <td>快递单号:</td>
                                        <td colspan="1">{{$orders['express_sn']}}</td>

                                    </tr>
                                    </tfoot>
                                </table>

                               
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="http://www.xhjh9999.cn/assets/js/amazeui.min.js"></script>
    <script type="text/javascript">
    // $(function(){
    //     CommonTools.request('/menu',{},function(data){
    //         // console.log(data);
    //         CommonView.menuView(data.result);
    //     })
    // })
    $(function(){
        // setSum();
        $('#print_button').click(function(){
            window.print();
        })
    });
    function setSum(){
        var index = [2,3,4];
        var weight = {};
        $.each($('tbody tr'),function(i,n){
            for(var i in index){
                var data = $(n).find('td').get(index[i]).innerHTML;
                console.log(data);
                if(isNaN(data) || data == ''){
                    data = 0;
                }else{
                    data = parseFloat(data);
                }
                if(typeof weight[index[i]] != 'undefined'){
                    weight[index[i]] +=  data;
                }else{
                    weight[index[i]] = data;
                }
            }
        });
        console.log(weight);
        for(var i in weight){
            $('tfoot tr td').get(i).innerHTML = weight[i].toFixed(2);
        }
    }
    </script>
</body>

</html>