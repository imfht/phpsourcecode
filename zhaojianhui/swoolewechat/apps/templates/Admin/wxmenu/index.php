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
                        <button type="button" data-action="expand-all" class="btn btn-white btn-sm">全部展开</button>
                        <button type="button" data-action="collapse-all" class="btn btn-white btn-sm">全部收缩</button>
                        <!--<button type="button" class="btn btn-outline btn-primary btn-sm synconline"><i class="fa fa-download"></i>同步线上数据</button>-->
                        <button type="button" class="btn btn-outline btn-primary btn-sm pushline"><i class="fa fa-upload"></i>推送到线上</button>
                        <button type="button" class="btn btn-outline btn-primary btn-sm add" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i>添加菜单</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>注意事项
                        </div>
                        <div class="ibox-content">
                            1、自定义菜单最多包括3个一级菜单，每个一级菜单最多包含5个二级菜单。<br>
                            2、一级菜单最多4个汉字，二级菜单最多7个汉字，多出来的部分将会以“...”代替。<br>
                            3、创建自定义菜单后，菜单的刷新策略是，在用户进入公众号会话页或公众号profile页时，如果发现上一次拉取菜单的请求在5分钟以前，就会拉取一下菜单，如果菜单有更新，就会刷新客户端的菜单。测试时可以尝试取消关注公众账号后再次关注，则可以看到创建后的效果。<br>
                        </div>
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
                        <form role="form" id="form" action="/Admin/WxMenu/save">
                            <div class="row">
                                <div class="col-lg-6 b-r form-left">
                                    <input type="hidden" name="menuId" id="menuId" value="0">
                                    <div class="form-group">
                                        <label>菜单类别</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="menuType" id="menuType">
                                            <?php foreach ($menuTypeList as $key => $name) {?>
                                            <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>菜单名称</label>
                                        <input type="text" placeholder="输入菜单名称" class="form-control" name="menuName" id="menuName" required>
                                    </div>
                                    <div class="form-group">
                                        <label>父级菜单</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="parentId">
                                        </select>
                                    </div>
                                    <div class="form-group needChange view">
                                        <label>网页链接</label>
                                        <input type="text" placeholder="例如：https://mp.weixin.qq.com/" class="form-control" name="url" required>
                                    </div>
                                    <div class="form-group needChange click scancode_waitmsg scancode_push pic_sysphoto pic_photo_or_album pic_weixin location_select">
                                        <label>菜单KEY值</label>
                                        <input type="text" placeholder="例如：kefu" class="form-control" name="key" required>
                                    </div>
                                    <div class="form-group needChange miniprogram">
                                        <label>appid</label>
                                        <input type="text" placeholder="小程序的appid" class="form-control" name="appid" required>
                                    </div>
                                    <div class="form-group needChange miniprogram">
                                        <label>页面路径</label>
                                        <input type="text" placeholder="小程序的页面路径" class="form-control" name="pagePath" required>
                                    </div>
                                    <div class="form-group needChange media_id view_limited">
                                        <label>素材ID</label>
                                        <input type="text" placeholder="" class="form-control" name="mediaId" id="mediaId">
                                    </div>
                                    <div class="form-group">
                                        <label>是否个性化菜单</label>
                                        <div class="i-checks"><label><input type="radio" name="isConditional" value="1"> <i></i>是</label></div>
                                        <div class="i-checks"><label><input type="radio" name="isConditional" value="0" checked> <i></i> 否</label></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 form-right">
                                    <div class="form-group">
                                        <label>用户标签</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="tag_id">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>用户性别</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="sex">
                                            <option value="">选择性别</option>
                                            <option value="1">男</option>
                                            <option value="2">女</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>国家</label>
                                        <input type="text" placeholder="输入国家" class="form-control" name="country">
                                        <span class="help-block m-b-none">例如：中国</span>
                                    </div>
                                    <div class="form-group">
                                        <label>省份</label>
                                        <input type="text" placeholder="输入省份" class="form-control" name="province">
                                        <span class="help-block m-b-none">例如：广东</span>
                                    </div>
                                    <div class="form-group">
                                        <label>城市</label>
                                        <input type="text" placeholder="输入城市" class="form-control" name="city">
                                        <span class="help-block m-b-none">例如：广州</span>
                                    </div>
                                    <div class="form-group">
                                        <label>客户端版本</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="client_platform_type">
                                            <option value="">请选择客户端</option>
                                            <?php foreach ($clientPlatformTypeList as $key => $name) {?>
                                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>语言</label>
                                        <select class="form-control m-b __web-inspector-hide-shortcut__" name="language">
                                            <option value="">请选择语言</option>
                                            <?php foreach ($languageList as $key => $name) {?>
                                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>
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
    /**
     * 根据菜单类别显示输入框
     * @param menuType
     */
    function selectMenuType(menuType)
    {
        $("#form .needChange").hide();
        $("#form ." + menuType).show();
    }

    /**
     * 根据是否个性话菜单显示输入框
     * @param isConditional
     */
    function selectIsConditional(isConditional)
    {
        if (isConditional == 1){//是个性化菜单
            $(".form-left").addClass('col-lg-6 b-r');
            $(".form-right").addClass('col-lg-6');
            $(".modal-dialog").addClass(' modal-lg');
            $(".form-right").show();
        }else{
            $(".form-left,.form-right").removeClass('col-lg-6');
            $(".modal-dialog").removeClass(' modal-lg');
            $(".form-right").hide();
        }
    }
    /**
     * 载入标签树结构select的option的html
     */
    function loadTagIdOption() {
        var secId = arguments[0] ? arguments[0] : 0;
        $.ajax({
            type: "get",
            url: "/Admin/WxUserTag/getTreeOption",
            data: {
                'secId' : secId,
            },
            success: function (data) {
                $("#form select[name='tag_id']").html(data);
            }
        });
    }

    /**
     * 载入菜单树结构select的option的html
     */
    function loadMenuOption() {
        var secId = arguments[0] ? arguments[0] : 0;
        $.ajax({
            type: "get",
            url: "/Admin/WxMenu/getTreeOption",
            data: {
                'moduleType' : $("#moduleType").val(),
                'secId' : secId,
            },
            success: function (data) {
                $("#form select[name='parentId']").html(data);
            }
        });
    }
    $(document).ready(function(){
        //可嵌套列表
        // activate Nestable for list
        $('#nestable').nestable({
            group: 1,
            maxDepth:2,
        }).on('change', function (e) {
            var list = e.length ? e : $(e.target),
                output = list.data('output');
            $.ajax({
                type: "post",
                url: "/admin/WxMenu/saveSort",
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

        //弹窗
        $("#form select[name='menuType']").on('change', function(){
            selectMenuType($(this).val());
        });
        $("#form input[name='isConditional']").on('change', function(){
            selectIsConditional($(this).val());
        });
        $(".add").on('click', function () {
            //加载父级菜单选择项
            loadMenuOption();
            loadTagIdOption();
            $(".modal-title").html('添加菜单');
            $("#form")[0].reset();
            $("#form input[name='menuId']").val(0);
            selectMenuType('click');
            selectIsConditional(0);
        });
        $(".edit").on('click', function () {
            //加载父级菜单选择项
            loadMenuOption();
            loadTagIdOption();
            $(".modal-title").html('编辑菜单');
            $.ajax({
                type: "get",
                url: "/Admin/WxMenu/get",
                data: {
                    'menuId' : $(this).parents("li").attr('data-id'),
                },
                datatype: "json",
                success: function (data) {
                    selectMenuType(data.data.menuType);
                    $("#form input[name='menuId']").val(data.data.menuId);
                    $("#form input[name='menuName']").val(data.data.menuName);
                    $("#form select[name='menuType']").val(data.data.menuType);
                    $("#form select[name='parentId']").val(data.data.parentId);
                    $("#form input[name='key']").val(data.data.key);
                    $("#form input[name='url']").val(data.data.url);
                    $("#form input[name='appid']").val(data.data.appid);
                    $("#form input[name='pagePath']").val(data.data.pagePath);
                    $("#form input[name='mediaId']").val(data.data.mediaId);
                    var isConditional = data.data.isConditional;
                    $("#form input[name='isConditional'][value=" + isConditional +"]").prop('checked', 'checked');
                    selectIsConditional(data.data.isConditional);
                    $("#form select[name='tag_id']").val(data.data.matchrule.tag_id);
                    $("#form select[name='sex']").val(data.data.matchrule.sex);
                    $("#form select[name='client_platform_type']").val(data.data.matchrule.client_platform_type);
                    $("#form select[name='language']").val(data.data.matchrule.language);
                    $("#form input[name='country']").val(data.data.country);
                    $("#form input[name='province']").val(data.data.province);
                    $("#form input[name='city']").val(data.data.city);
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
                            url: "/Admin/WxMenu/Del",
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
        //同步线上数据
        $(".synconline").on('click', function(){
            $.confirm({
                title: "同步所有线上数据",
                content: "此同步过程可能比较耗时",
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxMenu/syncOnline",
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
        //推送到线上
        $(".pushline").on('click', function(){
            $.confirm({
                title: "推送菜单配置到线上",
                content: "推送本地数据库菜单到线上",
                buttons: {
                    '确定': function () {
                        $.ajax({
                            type: "post",
                            url: "/Admin/WxMenu/pushOnline",
                            datatype: "json",
                            success: function (data) {
                                showToastr(data);
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