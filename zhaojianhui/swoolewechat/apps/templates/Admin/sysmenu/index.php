<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php'); ?>
<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/AdminInspinia/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">
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
                        <div class="col-md-4">
                            <select class="form-control" name="moduleType" id="moduleType">
                                <?php foreach ($moduleTypeList as $moduleK => $moduleV) {
    ?>
                                <option value="<?php echo $moduleK; ?>" <?php echo $moduleType == $moduleK ? 'selected' : ''?>><?php echo $moduleV; ?></option>
                                <?php 
}?>
                            </select>
                        </div>
                        <button type="button" data-action="expand-all" class="btn btn-white btn-sm">全部展开</button>
                        <button type="button" data-action="collapse-all" class="btn btn-white btn-sm">全部收缩</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm add" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>添加菜单</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>菜单列表</h5>
                        </div>
                        <div class="ibox-content">

                            <p class="m-b-lg">
                                你可以通过拖拽来调整菜单所属层级和菜单顺序。
                            </p>
                            <div class="dd" id="nestable">
                                <?php echo $nestableHtml; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">添加菜单</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="form" action="/Admin/SysMenu/save">
                            <input type="hidden" name="moduleType" id="moduleType" value="<?php echo $moduleType; ?>">
                            <input type="hidden" name="menuId" id="menuId" value="0">
                            <div class="form-group">
                                <label>菜单名称</label>
                                <input type="text" placeholder="输入菜单名称" class="form-control" name="menuName" id="menuName" required>
                            </div>
                            <div class="form-group">
                                <label>父级菜单</label>
                                <select class="form-control m-b __web-inspector-hide-shortcut__" name="parentId">
                                </select>
                            </div>
                            <div class="form-group">
                                <label>访问链接</label>
                                <input type="text" placeholder="例如：/Admin/Index/index" class="form-control" name="url" required>
                            </div>
                            <div class="form-group">
                                <label>菜单图标样式</label>
                                <input type="text" placeholder="例如：fa fa-sitemap" class="form-control" name="iconClass" id="iconClass">
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
                url: "/admin/sysmenu/saveSort",
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
        //载入树结构select的option的html
        function loadOption() {
            var secId = arguments[0] ? arguments[0] : 0;
            $.ajax({
                type: "get",
                url: "/Admin/SysMenu/getTreeOption",
                data: {
                    'moduleType' : $("#moduleType").val(),
                    'secId' : secId,
                },
                success: function (data) {
                    $("#form select[name='parentId']").html(data);
                }
            });
        }
        //弹窗
        $(".add").on('click', function () {
            //加载父级菜单选择项
            loadOption();
            $(".modal-title").html('添加菜单');
            $("#form")[0].reset();
            $("#form input[name='menuId']").val(0);
        });
        $(".addchild").on('click', function () {
            $(".modal-title").html('添加菜单');
            $("#form")[0].reset();
            $("#form input[name='menuId']").val(0);
            //加载父级菜单选择项
            loadOption($(this).parents("li").attr('data-id'));
        });
        $(".edit").on('click', function () {
            //加载父级菜单选择项
            loadOption();
            $(".modal-title").html('编辑菜单');
            $.ajax({
                type: "get",
                url: "/Admin/SysMenu/get",
                data: {
                    'menuId' : $(this).parents("li").attr('data-id'),
                },
                datatype: "json",
                success: function (data) {
                    $("#form input[name='menuId']").val(data.data.menuId);
                    $("#form input[name='menuName']").val(data.data.menuName);
                    $("#form select[name='parentId']").val(data.data.parentId);
                    $("#form input[name='url']").val(data.data.url);
                    $("#form input[name='iconClass']").val(data.data.iconClass);
                }
            });
        });
        $(".del").on('click', function () {
            var menuId = $(this).parents("li").attr('data-id');
            $.confirm({
                title: '你确定删除么？',
                content: '删除后将无法恢复',
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/SysMenu/Del",
                            data: {
                                'menuId' : menuId,
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
        //表单验证
        $("#form").validate({
            rules: {
                menuName:{
                    required: true,
                },
                url: {
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
    });
</script>
<!-- 文档页脚代码结束 -->
<?php echo $this->fetch('common/footer-end.php'); ?>