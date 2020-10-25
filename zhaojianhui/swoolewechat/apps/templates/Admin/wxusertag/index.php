<!-- 头部开始部分代码 -->
<?php echo $this->fetch('common/header-start.php'); ?>
<!-- jsTree -->
<link href="<?php echo $siteconf['cdnurl']?>/jsTree/dist/themes/default/style.min.css" rel="stylesheet">
<!-- Gritter -->
<link href="<?php echo $siteconf['cdnurl']?>/iCheck/skins/square/green.css" rel="stylesheet">
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
                        <button type="button" class="btn btn-outline btn-primary btn-sm syncOnline"><i class="fa fa-download"></i>同步线上数据</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm add"><i class="fa fa-plus"></i>新增</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm addchild"><i class="fa fa-plus"></i>添加子项</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm edit"><i class="fa fa-pencil"></i>编辑</button>
                        <button type="button" class="btn btn-outline btn-danger btn-sm del"><i class="fa fa-trash-o"></i>删除</button>
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
                                你可以通过鼠标右键选择<b>增加子项、编辑、删除</b>操作，你还可以通过<b>拖拽</b>来调整用户标签所属层级及顺序。
                            </p>
                            <div id="jstree">
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
                        <h4 class="modal-title">添加用户标签</h4>
                    </div>
                    <div class="modal-body">
                        <form role="form" id="form" action="/Admin/WxUserTag/save">
                            <input type="hidden" name="tagId" id="tagId" value="0">
                            <div class="form-group">
                                <label>用户标签名称</label>
                                <input type="text" placeholder="输入用户标签名称" class="form-control" name="tagName" id="tagName" required>
                            </div>
                            <div class="form-group">
                                <label>父级用户标签</label>
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
<!-- iCheck -->
<script src="<?php echo $siteconf['cdnurl']?>/iCheck/icheck.min.js"></script>
<script>
    //载入树结构select的option的html
    function loadOption() {
        var secId = arguments[0] ? arguments[0] : 0;
        $.ajax({
            type: "get",
            url: "/Admin/WxUserTag/getTreeOption",
            data: {
                'secId' : secId,
            },
            success: function (data) {
                $("#form select[name='parentId']").html(data);
            }
        });
    }
    //同步所有线上
    $(".syncOnline").on('click', function(){
        $.confirm({
            title: "同步所有线上数据",
            content: "此同步过程可能比较耗时",
            buttons: {
                '确定': function () {
                    $.ajax({
                        type: "post",
                        url: "/Admin/WxUserTag/syncOnline",
                        datatype: "json",
                        success: function (data) {
                            showToastr(data);
                            if (data.status == 'success'){
                                //重新加载树结构
                                var tree = $.jstree.reference("#jstree");
                                tree.refresh();
                            }
                        }
                    });
                },
                '取消': function () {
                },
            }
        });
    });
    //增加
    function add(parentId)
    {
        //加载父级菜单选择项
        loadOption(parentId);
        $("#form")[0].reset();
        $("#form input[name='tagId']").val(0);
        if (parentId){
            $(".modal-title").html('添加用户子标签');
        }else{
            $(".modal-title").html('添加用户标签');
        }
        $('#myModal').modal('show');
    }
    //修改
    function edit(id) {
        //加载父级菜单选择项
        loadOption();
        $(".modal-title").html('编辑用户标签');
        $.ajax({
            type: "get",
            url: "/Admin/WxUserTag/get",
            data: {
                'id' : id,
            },
            datatype: "json",
            success: function (data) {
                if (data.data == null){
                    toastr.error('数据不存在', '错误');
                    return false;
                }
                $("#form input[name='tagId']").val(data.data.tagId);
                $("#form input[name='tagName']").val(data.data.tagName);
                $("#form select[name='parentId']").val(data.data.parentId);

                $('#myModal').modal('show');
            }
        });
    }
    //删除
    function del(id) {
        $.confirm({
            title: '你确定删除么？',
            content: '删除后将无法恢复',
            buttons: {
                '确定': function () {
                    $.ajax({
                        type: "post",
                        url: "/Admin/WxUserTag/del",
                        data: {
                            'id' : id,
                        },
                        datatype: "json",
                        success: function (data) {
                            showToastr(data);
                            if (data.status == 'success'){
                                $('#myModal').modal('hide');
                                //重新加载树结构
                                var tree = $.jstree.reference("#jstree");
                                tree.refresh();
                            }
                        }
                    });
                },
                '取消': function () {
                },
            }
        });
    }
    $(document).ready(function(){
        //加载树结构
        $('#jstree').jstree({
            'core' : {
                "multiple": false,
                'check_callback' : true,
                "themes" : {
                    "variant" : "large"
                },
                'data' : {
                    'url' : '/Admin/WxUserTag/getJsTreeData',
                    'data' : function (node) {
                        //return {'id' : node.id};
                    }
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
            "plugins" : [ 'types', 'dnd', 'wholerow','unique','contextmenu'],
            "contextmenu": {
                "items": {
                    "create": null,
                    "rename": null,
                    "remove": null,
                    "ccp": null,
                    "add": {
                        "label": "添加子标签",
                        "action": function (obj) {
                            var inst = jQuery.jstree.reference(obj.reference);
                            var clickedNode = inst.get_node(obj.reference);
                            add(clickedNode.id);
                        }
                    },
                    "edit": {
                        "label": "编辑",
                        "action": function (obj) {
                            var inst = jQuery.jstree.reference(obj.reference);
                            var clickedNode = inst.get_node(obj.reference);
                            edit(clickedNode.id);
                        }
                    },
                    "delete": {
                        "label": "删除",
                        "action": function (obj) {
                            var inst = jQuery.jstree.reference(obj.reference);
                            var clickedNode = inst.get_node(obj.reference);
                            del(clickedNode.id);
                        }
                    }
                }
            }
        });
        //移动事件
        $('#jstree').on('move_node.jstree', function(e,data){
            $.post("/Admin/WxUserTag/saveSort",
                {
                    id : data.node.id,
                    parent : data.parent,
                    position:data.position
                },
                function(data,status){
                    showToastr(data);
                }, 'json');

        })
        //表单
        $("#form").validate({
            tags: {
                groupName:{
                    required: true,
                },
            },
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    type:'post',
                    dataType:'json',
                    success:function(data) {
                        showToastr(data);
                        if (data.status == 'success'){
                            $('#myModal').modal('hide');
                            //重新加载树结构
                            var tree = $.jstree.reference("#jstree");
                            tree.refresh();
                        }
                    }
                });
            }
        });
        //弹窗
        $(".add").on('click', function () {
            add(0);
        });
        $(".addchild").on('click', function () {
            var ref = $('#jstree').jstree(true),
                sel = ref.get_selected();
            if(!sel.length) {
                toastr.error('请选中您想要添加子项的用户标签', '错误');
                return false;
            }
            add(sel[0]);
        });
        $(".edit").on('click', function () {
            var ref = $('#jstree').jstree(true),
                sel = ref.get_selected();
            if(!sel.length) {
                toastr.error('请选中您想要编辑的用户标签', '错误');
                return false;
            }
            edit(sel[0]);
        });
        $(".del").on('click', function () {
            var ref = $('#jstree').jstree(true),
                sel = ref.get_selected();
            if(!sel.length) {
                toastr.error('请选中您想要删除的用户标签', '错误');
                return false;
            }
            del(sel[0]);
        });
    });
</script>
<!-- 文档页脚代码结束 -->
<?php echo $this->fetch('common/footer-end.php'); ?>