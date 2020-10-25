<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link href="//cdn.bootcss.com/bootstrap/3.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <!-- <link href="//cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="/hellomarkertest/Mobile/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/jquery.mobile-1.4.5.min.css">
    <!-- <link rel="stylesheet" href="//cdn.bootcss.com/jquery-mobile/1.4.5/jquery.mobile.min.css"> -->
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/jquery.mobile-1.4.5.min.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script src="/hellomarkertest/Mobile/Home/View/Public/js/marker.js"></script>
    <script>
      var appUrl="/hellomarkertest/mobile.php";
    </script>
</head>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-calculator.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/jquery-charts.js"></script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/bootstrap-datepicker.js"></script>
    
    


<div data-role="page" >
    <div data-role="header" data-position="fixed"data-tap-toggle="false" data-theme="h">
      <a href="#sharePanel" style="display:inline-block;margin-top:10px;" ><i class="fa fa-list"></i></a>
      <h1>添加新账目</h1>
    </div>
    <div data-role="panel" id="sharePanel"> 
            <h4><small><strong>应用列表</strong></small></h4>
            <li class="list-group-item" ><i class="fa fa-file-text-o"></i> 一本正经</li>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/newWork/" data-ajax="false">
                    <font>添加新记事</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/" data-ajax="false">
                    <font>查看记事本</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Work/myWork/" data-ajax="false">
                    <font>所有记事列表</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
            <li class="list-group-item" ><i class="fa fa-cny"></i> 柴米油盐</li>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/newAccount/" data-ajax="false">
                    <font>添加记账</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/setBudget/" data-ajax="false">
                    <font>设置预算</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/" data-ajax="false">
                    <font>查看账本</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
                <a class="list-group-item  text-center" href="/hellomarkertest/mobile.php/Home/Account/allReport" data-ajax="false">
                    <font>详细报表</font>
                    <i class="pull-right fa fa-angle-right"></i>
                </a>
            <a class="list-group-item text-left" href="/hellomarkertest/mobile.php/Home/Weather/" data-ajax="false">未雨绸缪<i class="pull-right fa fa-meh-o"></i></a>
            <?php if($myChangeErrorFlag == 1): ?><div class="alert alert-danger"><strong><i class="fa fa-info-circle"></i><?php echo ($myChangeError); ?></strong></div><?php endif; ?>
    </div>
<div data-role="main" class="ui-content">
 <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="/hellomarkertest/mobile.php/Home/Account/addAccount/" data-ajax="false"method="post" accept-charset="utf-8">
        <div class="modal-body">
            <div class="form-group">
              <label for="accountname">※ 名称： </label>
              <input id="accountnameInput" type="text" name="accountname" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="accounttime">※ 选择时间 </label>
              <input id="accounttimeInput" type="text" name="accounttime" class="form-control" placeholder="点击选择..." required>
              <script>
                $('#accounttimeInput').datepicker({
                    format: "yyyy-mm-dd",
                    language: "cn",
                    orientation: "bottom auto",
                    autoclose: true,
                    todayHighlight: true
                });  
              </script>
            </div>
            <div class="form-group">
              <label for="accountmoney">※ 金额： </label>
              <input id="accountmoneyInput"type="number" name="accountmoney" class="form-control" placeholder="金额..."required>
            </div>
            <div class="form-group">
              <label for="accounttype">※ 类别：</label>
              <select id="accounttypeInput"type="text" name="accounttype" class="form-control" placeholder="..."required>
              <option value="1">一日三餐</option>
              </select>
              <script>
                  showTypeOption("#accounttypeInput");
              </script> 
            </div>
            <div class="form-group">
              <label for="accountother">备注： </label>
              <textarea id="accountotherInput"  name="accountother" class="form-control" placeholder="可以为空..." rows="3" wrap="hard"></textarea> 
            </div>
        </div>
        <div class="modal-footer">
          <button id="addAccountBtn" type="submit" class="btn btn-info ">添加</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
        </div>
      </form>
    </div>
  </div>

</div>