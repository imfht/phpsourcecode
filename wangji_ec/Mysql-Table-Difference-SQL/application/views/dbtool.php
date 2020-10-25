<!DOCTYPE html>
<html>
    <head>
        <title>数据表对比升级工具 </title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
        <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
        <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
        <script src="http://cdn.bootcss.com/jquery.form/3.51/jquery.form.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h2 class="text-center">输入连接信息</h2>
                    <form id="new" class="form-horizontal" method="post">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">HOST</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="host"  placeholder="localhost">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">USER</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="user" placeholder="root">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">PASS</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" name="pass" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">DB_NEW</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="db_new" placeholder="new">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">DB_OLD</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="db_old" placeholder="new">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-default">SUBMIT</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8">
                    <textarea id="target" style="width:100%;" rows="30"></textarea>
                </div>
            </div>
            <div class="row">

            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                var options = {
                    type: "POST",
                    url: "tabletool/go",
                    success: function (data) {
                        $("#target").html(data);
                    }
                };

                // ajaxForm
                $("#new").ajaxForm(options);

                // ajaxSubmit
                $("#btnAjaxSubmit").click(function () {
                    $("#form1").ajaxSubmit(options);
                });
            });
        </script>
    </body>
</html>
