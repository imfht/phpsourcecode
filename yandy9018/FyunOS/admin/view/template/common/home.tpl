

        <!-- upper main stats -->
        
        <div id="main-stats">
        
            <div class="row stats-row">
             
                <div class="col-md-3 col-sm-3 stat">
                               
                    <div class="data">
     
                        <span class="number">￥<?php echo $total_sale; ?></span>
                        元
                    </div>
                    <span class="date"><?php echo $text_total_sale; ?></span>
                </div>
                <div class="col-md-3 col-sm-3 stat">
                    <div class="data">
                        <span class="number"><?php echo $total_order; ?></span>
                        个
                    </div>
                    <span class="date"><?php echo $text_total_order; ?></span>
                </div>
                <div class="col-md-3 col-sm-3 stat">
                    <div class="data">
                        <span class="number"><?php echo $total_customer; ?></span>
                        位
                    </div>
                    <span class="date"><?php echo $text_total_customer; ?></span>
                </div>
                <div class="col-md-3 col-sm-3 stat last">
                    <div class="data">
                        <span class="number">￥<?php echo $total_shipping; ?></span>
                        元
                    </div>
                    <span class="date"><?php echo $text_total_shipping; ?></span>
                </div>
            </div>
        </div>
        <!-- end upper main stats -->
 
      <div class="latest">
      <div id="pad-wrapper">
       
	  <?php if ($error_logs) { ?>
	  <div class="alert alert-danger"><i class="icon-remove-sign"></i><?php echo $error_logs; ?><a class="close" data-dismiss="alert">×</a></div>
	  <?php } ?>
      
<div id="fyuncloud">
 <div class="alert alert-danger"><i class="icon-remove-sign"></i>还未开启云推送服务！（如需开启常规设置中开启）<a class="close" data-dismiss="alert">×</a></div>
</div>

 <div class="table-products" style="margin-top:0px;">
  <div class="row section" style="margin-top:0px;">
                <div class="col-md-12">
                    <h4 class="title">
                       数据统计 <small>直观的帮您分析大数据</small>
                   
                    </h4>
                </div>
               <div class="col-md-12">
                      <div id="sale-trend" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
                </div>
            </div>
            
            

           <!-- table sample -->
            <!-- the script for the toggle all checkboxes from header is located in js/theme.js -->
            <div class="table-products">
                <div class="row section">
                    <div class="col-md-12">
                        <h4 class="title">最新订单 <small>最新10个订单</small></h4>
                    </div>
                </div>

                <div class="row filter-block">
                  
                </div>

                <div class="row">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="col-md-2">
                                  <?php echo $column_order; ?>
                                </th>
                                <th class="col-md-2">
                                    <span class="line"></span><?php echo $column_customer; ?>
                                </th>
                                <th class="col-md-2">
                                    <span class="line"></span><?php echo $column_status; ?>
                                </th>
                                 <th class="col-md-2">
                                    <span class="line"></span><?php echo $column_date_added; ?>
                                </th>
                                 <th class="col-md-2">
                                    <span class="line"></span><?php echo $column_total; ?>
                                </th>
                                 <th class="col-md-2">
                                    <span class="line"></span><?php echo $column_action; ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php if ($orders1) { ?>
              <?php foreach ($orders1 as $order) { ?>
                            <tr>
                                <td>
                                 <a href="#"><?php echo $order['order_id']; ?></a>
                                </td>
                                <td>
                                   <?php echo $order['customer']; ?>(<?php echo $order['telephone']; ?>)
                                </td>
                                
                                <td>
                                    <span class="label label-success"><?php echo $order['status']; ?></span> <!--  <span class="label label-info">Standby</span>-->
                                </td>
                                 <td><?php echo $order['date_added']; ?></td>
                <td><?php echo $order['total']; ?></td>
                                 <td><?php foreach ($order['action'] as $action) { ?>
                  [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
                  <?php } ?></td>
                            </tr>
                             <?php } ?>
                                <?php } else { ?>
              <tr>
                <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
                             
                        </tbody>
                    </table>
                </div>
            
            </div>
            </div>
            <!-- end table sample -->
       
      </div>
   <script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'sale-trend',
                type: 'line'
            },
            title: {
                text: '一周数据走向',
                x: -20 //center
            },
         
            xAxis: {
                categories: [<?php echo $range;?>]
            },
            yAxis: {
                title: {
                    text: '数量'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y ;
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: '销售额(百元)',
                data: [<?php echo $order_total;?>]
            }, {
                name: '订单数',
                data: [<?php echo $orders;?>]
            }, {
                name: '注册客户数',
                data: [<?php echo $customers?>]
            },  {
                name: '评价数',
                data: [<?php echo $review;?>]
            }]
        });
    });
    
});
</script>	
<script type="text/javascript" src="view/javascript/highcharts.js"></script>
<script type="text/javascript" src="view/javascript/exporting.js"></script>