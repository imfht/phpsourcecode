<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php'); ?>
<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
<!-- jsTree -->
<link href="<?php echo $siteconf['cdnurl']?>/jsTree/dist/themes/default/style.min.css" rel="stylesheet">
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
                        <button type="button" data-action="expand-all" class="btn btn-white btn-sm">全部展开</button>
                        <button type="button" data-action="collapse-all" class="btn btn-white btn-sm">全部收缩</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm synconline"><i class="fa fa-download"></i>同步线上数据</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm add" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>添加用户组</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>用户组列表</h5>
                        </div>
                        <div class="ibox-content">

                            <p class="m-b-lg">
                                你可以通过拖拽来调整用户组所属层级及顺序。
                            </p>
                            <div class="dd" id="nestable">
                                <?php echo $nestableHtml; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- 分组编辑model -->
        <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">添加用户组</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="form" action="/admin/WxUserGroup/save">
                            <input type="hidden" name="groupId" id="groupId" value="0">
                            <div class="form-group">
                                <label>用户组名称</label>
                                <input type="text" placeholder="输入用户组名称" class="form-control" name="groupName" id="groupName" required>
                            </div>
                            <div class="form-group">
                                <label>父级分组</label>
                                <select class="form-control m-b __web-inspector-hide-shortcut__" name="parentId">
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#form').submit();">保存</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 权限编辑model -->
        <div class="modal inmodal" id="ruleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">权限控制</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="ruleform" action="/Admin/WxUserGroup/saveRule">
                            <input type="hidden" name="id" id="id" value="0">
                            <div id="jstree">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" onclick="javascript:$('#ruleform').submit();">保存</button>
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
<!-- Nestable List -->
<script src="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/nestable/jquery.nestable.js"></script>
<!-- jsTree -->
<script src="<?php echo $siteconf['cdnurl']?>/jsTree/dist/jstree.min.js"></script>
<script>
    $(document).ready(function(){
        //模块选择
        $("#moduleType").on('change', function () {
            window.location.href = '<?php echo $this->currentUrl?>?moduleType='+$(this).val();
        });
        //可嵌套列表
        // activate Nestable for list
        $('#nestable').nestable({
            group: 1
        }).on('change', function (e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            $.ajax({
                type: "post",
                url: "/Admin/WxUserGroup/saveSort",
                data: {
                    'sortData' : list.nestable('serialize'),
                },
                datatype: "json",
                success: function (data) {
                    showToastr(data);
                }
            });
        });

        $('#nestable-menu').on('click', function (e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
        //表单验证
        $("#form").validate({
            rules: {
                groupName:{
                    required: true,
                },
            },
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data, true);
                    }
                });
            }
        });
        //载入树结构select的option的html
        function loadOption() {
            var secId = arguments[0] ? arguments[0] : 0;
            $.ajax({
                type: "get",
                url: "/Admin/WxUserGroup/getTreeOption",
                data: {
                    'secId' : secId,
                },
                success: function (data) {
                    $("#form select[name='parentId']").html(data);
                }
            });
        }
        //弹窗
        $(".add").on('click', function () {
            $(".modal-title").html('添加用户组');
            //加载选择项
            loadOption();
            $("#form")[0].reset();
            $("#form input[name='groupId']").val(0);
        });
        $(".addchild").on('click', function () {
            $(".modal-title").html('添加用户组');
            $("#form")[0].reset();
            $("#form input[name='groupId']").val(0);
            //加载父级菜单选择项
            loadOption($(this).parents("li").attr('data-id'));
        });
        $(".edit").on('click', function () {
            $(".modal-title").html('编辑用户组');
            //加载选择项
            loadOption();
            $.ajax({
                type: "get",
                url: "/Admin/WxUserGroup/get",
                data: {
                    'id' : $(this).parents("li").attr('data-id'),
                },
                datatype: "json",
                success: function (data) {
                    $("#form input[name='groupId']").val(data.data.groupId);
                    $("#form input[name='groupName']").val(data.data.groupName);
                    $("#form select[name='parentGroupId']").val(data.data.parentGroupId);
                }
            });
        });
        $(".del").on('click', function () {
            var id = $(this).parents("li").attr('data-id');
            $.confirm({
                title: '你确定删除么？',
                content: '删除后将无法恢复',
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxUserGroup/del",
                            data: {
                                'id' : id,
                            },
                            datatype: "json",
                            success: function (data) {
                                showToastr(data, true);
                            }
                        });
                    },
                    '取消': function () {
                    },
                }
            });
        });
        //同步线上数据
        $(".synconline").on('click', function(){
            $.confirm({
                title: "同步所有线上数据",
                content: "此同步过程可能比较耗时",
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxUserGroup/syncOnline",
                            datatype: "json",
                            success: function (data) {
                                showToastr(data, true);
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