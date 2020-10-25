<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php'); ?>
<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
<!-- jsTree -->
<link href="<?php echo $siteconf['cdnurl']?>/jstree/dist/themes/default/style.min.css" rel="stylesheet">
<!-- dataTables-->
<link rel="stylesheet" type="text/css" href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/css/plugins/dataTables/datatables.min.css">
<!--jasny-->
<link href="<?php echo $siteconf['cdnurl']?>/jasny-bootstrap/dist/css/jasny-bootstrap.min.css" rel="stylesheet">
<!-- 头部结束部分代码 -->
<?php echo $this->fetch('common/header-end.php'); ?>
<body>
<div id="wrapper">
    <!-- 主体内容导航栏 -->
    <?php echo $this->fetch('common/main-left-navbar.php'); ?>
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <!-- 主体顶部导航 -->
        <?php echo $this->fetch('common/main-top-navbar.php'); ?>
        <!-- 主体内容 -->
        <div class="wrapper wrapper-content  animated fadeInRight">
            <div class="row">
                <div class="col-md-4">
                    <div id="nestable-menu">
                        <button type="button" class="btn btn-outline btn-primary btn-sm syncOnline"><i class="fa fa-download"></i>同步线上素材</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm add" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i>添加图片素材</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>素材列表</h5>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-striped table-bordered table-hover" id="tableBox">
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--模板新增-->
        <div class="modal inmodal" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">新增图片素材</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="addform" action="/Admin/WxMediaImage/add" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="title">素材标题</label>
                                <input type="text" placeholder="输入素材标题" class="form-control" name="title" id="title" required>
                            </div>
                            <div class="form-group">
                                <label for="intro">素材说明</label>
                                <textarea class="form-control" name="intro" id="intro" placeholder="输入用户备注" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>素材类别</label>
                                <div class="i-checks"><label><input type="radio" name="mediaType" value="image" checked required> <i></i>永久素材</label></div>
                                <div class="i-checks"><label><input type="radio" name="mediaType" value="imagetemp" required> <i></i> 临时素材</label></div>
                            </div>
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">选择图片</span>
                                    <span class="fileinput-exists">重选图片</span>
                                    <input type="file" accept="image/gif,image/jpeg,image/jpg,image/png" name="mediafile"/>
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">删除</a>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#addform').submit();">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 主体页脚 -->
        <?php echo $this->fetch('common/main-footer.php'); ?>
    </div>
    <!-- 聊天窗口 -->
    <?php echo $this->fetch('common/small-chat-box.php'); ?>
    <!-- 右侧边栏 -->
    <?php echo $this->fetch('common/right-sidebar.php'); ?>
</div>
<!-- 文档页脚代码开始 -->
<?php echo $this->fetch('common/footer-start.php'); ?>
<!-- jsTree -->
<script src="<?php echo $siteconf['cdnurl']?>/jstree/dist/jstree.min.js"></script>
<!-- dataTables -->
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/dataTables/datatables.min.js" type="text/javascript"></script>
<!-- Jasny -->
<script src="<?php echo $siteconf['cdnurl']?>/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

<script>

    $(document).ready(function(){
        //列表
        var table = $('#tableBox').DataTable({
            language: {
                url: '//static.tudouyu.cn/datatables/language/zh-CN.json'
            },
            pageLength: 10,
            responsive: true,
            sClass:'text-center',
            dom: '<"html5buttons"B>lTfgtip',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFile'},
                {extend: 'pdf', title: 'ExampleFile'},

                {extend: 'print',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
            bStateSave: true,
            processing: true,
            //开启服务器模式
            serverSide: true,
            //数据来源（包括处理分页，排序，过滤） ，即url，action，接口，等等
            ajax: {
                url : '/Admin/WxMediaImage/getPageList',
                type : 'POST',
            },
            columns:[
                {data: "mediaId",title:"序号",orderable:false,searchable:true,},
                {data: "wxMediaId",title:"微信端素材ID",orderable:false,searchable:true,},
                {data: "title",title: "图片标题",orderable:false, searchable:true,},
                {data: "intro",title: "图片说明",orderable:false, searchable:true,},
                {
                    data: "remoteUrl",title: "预览",orderable:false, searchable:true,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        if (cellData){
                            html = '<img src="' + cellData + '" class="img-circle img-sm">';
                        }
                        $(td).html(html);
                    }
                },
                {
                    data: 'statusIs',title: "状态",orderable:false, searchable:true,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        if (cellData == 1){
                            html = '<span class="label label-primary">启用</span>';
                        }else{
                            html = '<span class="label label-warning">禁用</span>';
                        }
                        $(td).html(html);
                    }
                },
                {
                    data:null, title: "操作", orderable:false, searchable:false,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        if (cellData.statusIs == 1){
                            html += '<button type="button" setStatus=0 class="btn btn-outline btn-warning btn-xs setStatus"><i class="fa fa-unlock"></i>禁用</button>';
                        }else{
                            html += '<button type="button" setStatus=1 class="btn btn-outline btn-success btn-xs setStatus"><i class="fa fa-lock"></i>启用</button>';
                        }
                        $(td).html(html);
                    }
                },
            ],
        });
        //新增素材
        var validator = $("#addform").validate({
            rules:{
                imagefile:{
                    required:true,
                }
            },
            messages:{
                imagefile:{
                    required:'请选择图片文件',
                }
            },
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#addModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
        //设置使用场景
        var groupValidator = $("#setKeyform").validate({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#keyModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
        $("#tableBox").on('click', '.setGroup', function () {
            //清除错误提示
            groupValidator.resetForm();
            $.ajax({
                type: "get",
                url: "/Admin/WxTemplate/get",
                data: {
                    'id' : $(this).parents("tr").attr('id'),
                },
                datatype: "json",
                success: function (data) {
                    $("#setKeyform input[name='id']").val(data.data.templateId);
                    $("#setKeyform select[name='usekey']").val(data.data.usekey);
                }
            });
        });
        //同步所有用户信息
        $(".syncOnline").on('click', function(){
            $.confirm({
                title: "同步所有用户数据",
                content: "此同步过程可能比较耗时",
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxMediaImage/syncOnline",
                            datatype: "json",
                            success: function (data) {
                                showToastr(data);
                                if (data.status == 'success'){
                                    table.ajax.reload();
                                }
                            }
                        });
                    },
                    '取消': function () {
                    },
                }
            });
        });
        //删除
        $("#tableBox").on('click', '.setStatus', function () {
            var id = $(this).parents("tr").attr('id');
            var status = $(this).attr('setStatus');
            if (status == 1){
                var title = '你确定启用该模板么？';
                var content = '开启后该即可正常发送模板消息';
            }else{
                var title = '你确定禁用该账号么？';
                var content = '禁用后该将无法正常发送模板消息';
            }
            $.confirm({
                title: title,
                content: content,
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxTemplate/setStatus",
                            data: {
                                'id' : id,
                                'status' : status
                            },
                            datatype: "json",
                            success: function (data) {
                                showToastr(data);
                                if (data.status == 'success'){
                                    table.ajax.reload();
                                }
                            }
                        });
                    },
                    '取消': function () {
                    },
                }
            });
        });
        $("#tableBox").on('click', '.del', function () {
            var id = $(this).parents("tr").attr('id');
            $.confirm({
                title: '你确定删除么？',
                content: '删除后将无法恢复',
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxTemplate/del",
                            data: {
                                'id' : id,
                            },
                            datatype: "json",
                            success: function (data) {
                                showToastr(data);
                                if (data.status == 'success'){
                                    table.ajax.reload();
                                }
                            }
                        });
                    },
                    '取消': function () {
                    },
                }
            });
        });
    });
</script>
<!-- 文档页脚代码结束 -->
<?php echo $this->fetch('common/footer-end.php'); ?>