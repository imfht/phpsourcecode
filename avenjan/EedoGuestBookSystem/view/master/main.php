  <?php
include $_SERVER['DOCUMENT_ROOT']."/libs/function.php";
  ?>
  <!DOCTYPE html>
  <html lang="zh-cn">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>后台首页</title>
    <link rel="stylesheet" href="/src/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="/plugins/font-awesome/css/font-awesome.min.css" media="all">
    <style>
    .info-box {
        height: 85px;
        background-color: white;
        background-color: #ecf0f5;
    }
    .info-box .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 85px;
        width: 85px;
        text-align: center;
        font-size: 45px;
        line-height: 85px;
        background: rgba(0, 0, 0, 0.2);
    }

    .info-box .info-box-content {
        padding: 5px 10px;
        margin-left: 85px;
    }

    .info-box .info-box-content .info-box-text {
        display: block;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
    }

    .info-box .info-box-content .info-box-number {
        
        font-weight: bold;
        font-size: 18px;
    }
    .major {
        font-weight: 10px;
        color: #01AAED;
    }
    .main {
        margin-top: 25px;
    }
    .main .layui-row {
        margin: 10px 0;
    }
</style>
</head>
<body>
    <div class="layui-fluid main">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md3">
                <div class="info-box">
                    <span class="info-box-icon" style="background-color:#00c0ef !important;color:white;"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">留言管理</span>
                        <p>留言数：<span class="info-box-number"><?php echo count_tab("book","id",">","0");?></span></p>
                        <p>未审核：<span class="info-box-number"><?php echo count_tab("book","view","=","0");?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-row">
            <div class="layui-col-md12">
                <ul class="layui-timeline">
                    <blockquote class="layui-elem-quote explain">
                        <h3 >当前版本：<?php echo $system_version;?></h3>
                        <p>
                        <a href="https://gitee.com/avenjan/EedoGuestBookSystem/wikis/pages?title=%E6%9B%B4%E6%96%B0%E6%97%A5%E5%BF%97" target="_blank" class="layui-btn layui-btn layui-btn-danger ">查看更新</a>
                        <br/>
                            功能意见建议敬请联系admin@eedo.net<br/>
                            QQ群：90595994<br/>
                            功能定制请联系QQ:920105110
                        </p>
                        <hr/>
                        <h3>开源计划：</h3>
                        <p style="color:#FF5722">
                        截止2018年9月30日，本系统将结束测试预览版的调试，发布升级后的Pro.**版本，有需要的朋友请加QQ群获取升级版本、技术交流以及后续问题的反馈和功能建议。
                        </p>
                        <hr/>
                        <p>
                        	如果本系统能够获得您的认可，或者您愿意支持本系统的开发运营和维护或许您可以通过点击下方的连接“打赏作者”，当然您的赞赏与否并不影响您使用该系统的全部功能。
                        	<br/>
                        	——By avenjan
                        	 
                        	2018年9月19日
                        	<br/>
                        	<a href="http://guestbook.eedo.net/goodjob.php" target="_blank" class="layui-btn layui-btn layui-btn-danger ">打赏作者</a>
                        </p>
                    </blockquote>
                    <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis">&#xe63f;</i>
                        <div class="layui-timeline-content layui-text">
                            <div class="layui-timeline-title"><input type="button" class="layui-btn" value="点击我测试message组件" id="test" /></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script src="/src/layui/layui.js"></script>
    <script>
        layui.use('jquery', function() {
            var $ = layui.jquery;
            $('#test').on('click', function() {
                parent.message.show({
                    skin: 'cyan'
                });
            });
        });
    </script>
</body>
</html>