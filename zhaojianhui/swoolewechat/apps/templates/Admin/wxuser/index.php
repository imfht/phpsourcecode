<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php'); ?>
<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
<!-- jsTree -->
<link href="<?php echo $siteconf['cdnurl']?>/jsTree/dist/themes/default/style.min.css" rel="stylesheet">
<!-- dataTables-->
<link rel="stylesheet" type="text/css" href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/css/plugins/dataTables/datatables.min.css">
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
                        <button type="button" class="btn btn-outline btn-primary btn-sm syncOnline"><i class="fa fa-download"></i>同步线上用户</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>用户列表</h5>
                        </div>
                        <div class="ibox-content">
                            <table class="table table-striped table-bordered table-hover" id="tableBox">
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- 用户分组编辑model -->
        <div class="modal inmodal" id="groupModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">设置用户分组</h4>
                    </div>
                    <div class="modal-body">
                        <form role="groupform" id="groupform" action="/Admin/WxUser/setGroup">
                            <input type="hidden" name="ids[]" id="ids" value="0">
                            <div class="form-group">
                                <label>所属用户组</label>
                                <select class="form-control m-b __web-inspector-hide-shortcut__" name="groupId" id="groupId" required>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#groupform').submit();">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 用户备注编辑modal -->
        <div class="modal inmodal" id="remarkModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">备注设置</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="remarkform" action="/Admin/WxUser/setRemark">
                            <input type="hidden" name="id" id="id" value="0">
                            <div class="form-group">
                                <label for="remark">备注内容</label>
                                <textarea class="form-control" name="remark" id="remark" placeholder="输入用户备注"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#remarkform').submit();">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!--用户标签管理-->
        <div class="modal inmodal" id="tagModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">标签设置</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="tagform" action="/Admin/WxUser/setTag">
                            <input type="hidden" name="id" id="id" value="0">
                            <div id="jstree">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#tagform').submit();">保存</button>
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
<script src="<?php echo $siteconf['cdnurl']?>/jsTree/dist/jstree.min.js"></script>
<!-- dataTables -->
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/dataTables/datatables.min.js" type="text/javascript"></script>
<script>
    //载入树结构select的option的html
    function loadGroupIdOption() {
        var secId = arguments[0] ? arguments[0] : 0;
        $.ajax({
            type: "get",
            url: "/Admin/WxUserGroup/getTreeOption",
            data: {
                'secId' : secId,
            },
            success: function (data) {
                $("#groupform select[name='groupId']").html(data);
            }
        });
    }
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
                url : '/Admin/WxUser/getPageList',
                type : 'POST',
            },
            columns:[
                {data: "userId",title:"用户ID",orderable:false,searchable:true,},
                {
                    data: "headimgurl",title:"用户头像",orderable:false,searchable:true,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        if (cellData){
                            html = '<img src="' + cellData + '" class="img-circle img-sm">';
                        }
                        $(td).html(html);
                    }
                },
                {data: "openId",title:"openId",orderable:false,searchable:true,},
                {data: "unionId",title:"unionId",orderable:false,searchable:true,},
                {data: "nickName",title: "用户昵称",orderable:false, searchable:true,},
                {data: "groupName",title: "用户组",orderable:false, searchable:true,},
                {data: "sex",title: "性别",orderable:false, searchable:true,},
                {data: "country",title: "国家",orderable:false, searchable:false,},
                {data: "province",title: "省份",orderable:false, searchable:false,},
                {data: "city",title: "城市",orderable:false, searchable:false,},
                {
                    data: "tagidList",title: "标签",orderable:false, searchable:false,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '<ul class="tag-list" style="padding: 0">';
                        if (cellData != '[]'){
                            $.each(cellData, function(i, n){
                                html += '<li><a href="#">' + n + '</a></li>';
                            });
                        }
                        html += '</ul>';
                        $(td).html(html);
                    }
                },
                {data: "remark",title: "备注",orderable:false, searchable:false,},
                {
                    data: 'isBlock',title: "状态",orderable:false, searchable:true,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        if (cellData == 1){
                            html = '<span class="label label-danger">拉黑</span>';
                        }else{
                            html = '<span class="label label-primary">正常</span>';
                        }
                        $(td).html(html);
                    }
                },
                {data: "subscribeTime",title: "关注时间",searchable : false,orderable:false, searchable:false,},
                {
                    data:null, title: "操作", orderable:false, searchable:false,
                    createdCell: function (td, cellData, rowData, row, col) {
                        var html = '';
                        html += '<button type="button" class="btn btn-outline btn-primary btn-xs setGroup" data-toggle="modal" data-target="#groupModal"><i class="fa fa-group"></i>设置分组</button>';
                        html += '<button type="button" class="btn btn-outline btn-primary btn-xs setRemark" data-toggle="modal" data-target="#remarkModal"><i class="fa fa-pencil"></i>设置备注</button>';
                        html += '<button type="button" class="btn btn-outline btn-primary btn-xs setTag" data-toggle="modal" data-target="#tagModal"><i class="fa fa-pencil"></i>设置标签</button>';
                        if (cellData.isBlock == 1){
                            html += '<button type="button" setBlock=0 class="btn btn-outline btn-success btn-xs setBlock"><i class="fa fa-lock"></i>解锁</button>';
                        }else{
                            html += '<button type="button" setBlock=1 class="btn btn-outline btn-danger btn-xs setBlock"><i class="fa fa-unlock"></i>拉黑</button>';
                        }
                        $(td).html(html);
                    }
                },
            ],
        });
        //设置分组
        var groupValidator = $("#groupform").validate({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#groupModal').modal('hide');
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
                url: "/Admin/WxUser/get",
                data: {
                    'id' : $(this).parents("tr").attr('id'),
                },
                datatype: "json",
                success: function (data) {
                    $("#groupform input[name='ids[]']").val(data.data.userId);
                    //载入用户组选项
                    loadGroupIdOption(data.data.groupId);
                }
            });
        });
        //设置备注
        var remarkValidator = $("#remarkform").validate({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#remarkModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
        $("#tableBox").on('click', '.setRemark', function () {
            //清除错误提示
            remarkValidator.resetForm();
            $.ajax({
                type: "get",
                url: "/Admin/WxUser/get",
                data: {
                    'id' : $(this).parents("tr").attr('id'),
                },
                datatype: "json",
                success: function (data) {
                    $("#remarkform input[name='id']").val(data.data.userId);
                    $("#remarkform textarea[name='remark']").val(data.data.remark);
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
                            url: "/Admin/WxUser/syncOnline",
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
        //设置拉黑
        $("#tableBox").on('click', '.setBlock', function () {
            var id = $(this).parents("tr").attr('id');
            var setBlock = $(this).attr('setBlock');
            if (setBlock == 1){
                var title = '你确定拉黑该账号么？';
                var content = '拉黑后该账号将无法正常使用服务号';
            }else{
                var title = '你确定开启该账号么？';
                var content = '开启后该账号可以正常使用服务号';
            }
            $.confirm({
                title: title,
                content: content,
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxUser/setBlock",
                            data: {
                                'ids' : [id],
                                'status' : setBlock
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
        //加载树结构
        $('#jstree').jstree({
            'core' : {
                'data' : {
                    'url' : '/Admin/WxUserTag/getJsTreeData',
                    'data' : function (node) {
                        //return {'id' : id};
                    },
                }
            },
            'types' : {
                'default' : {
                    'icon' : 'fa fa-folder'
                },
            },
            "checkbox" : {
                "keep_selected_style" : false
            },
            "plugins" : [ 'types', 'checkbox'],
        });
        //编辑标签
        $("#tableBox").on('click', '.setTag', function () {
            var id = $(this).parents("tr").attr('id');
            $("#tagform input[name='id']").val(id);

            $("#jstree").jstree('uncheck_all');
            $.ajax({
                type: "get",
                url: "/Admin/WxUser/get",
                data: {
                    'id' : id,
                },
                datatype: "json",
                success: function (data) {
                    // 批量选中节点
                    $("#jstree").jstree('check_node', data.data.tagidList);
                }
            });
        });
        //用户标签设置表单验证
        $("#tagform").validate({
            submitHandler: function(form) {
                var checkedNode = $("#jstree").jstree('get_checked');
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    data:{
                        tagIds:checkedNode
                    },
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#tagModal').modal('hide');
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
    });
</script>
<!-- 文档页脚代码结束 -->
<?php echo $this->fetch('common/footer-end.php'); ?>