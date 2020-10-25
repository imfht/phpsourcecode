<!doctype html>
<html>
    <head>
        <title>miniLog 日志实例</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- 新 Bootstrap 核心 CSS 文件 -->
        <link href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

        <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
        <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>

        <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
        <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <h2>miniLog 日志实例</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                     <ul class="list-group">
                        <li class="list-group-item">
                            <a href="#" id="change_dir" class="btn btn-block btn-success">更改存储目录</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" id="change_file_name" class="btn btn-block btn-primary">存储不同的文件名(以日期为文件名)</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" id="change_save_format" class="btn btn-block btn-info">更改存储格式(txt)</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" id="pack_ok" class="btn btn-block btn-warning">封装好,全局快速调用</a>
                        </li>
                        <li class="list-group-item">
                            <a href="#" id="flush_data" class="btn btn-block btn-danger">清空数据</a>
                        </li>                        
                        <li class="list-group-item">
                            结果:<a target="_blank" href="" id="result"></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
<script>
var url = 'action.php';
$(function(){    
    $("#change_dir").click(function(){
        var _this = $(this);
        actionFun(_this);
    });
    $("#change_file_name").click(function(){
        var _this = $(this);
        actionFun(_this);
    });
    $("#change_save_format").click(function(){
        var _this = $(this);
        actionFun(_this);
    });
    $("#pack_ok").click(function(){
        var _this = $(this);
        actionFun(_this);
    });
    $("#flush_data").click(function(){
        var _this = $(this);
        actionFun(_this);
    });
});
function actionFun(_this) {
    var o = {};
    o.act = _this.attr("id");
    $.getJSON(url, o, function(data){
        if(data.status == 0) {
            $("#result").attr('href',data.msg);
            $("#result").html(data.msg);
        } else {
            $("#result").html(data.msg);
        }
    });
}
</script>
    </body>
</html>