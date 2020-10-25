<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html lang='zh-cn'>
<head>
    <meta charset="utf-8" />
    <title>后台管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--//老样式-->
    <link href="/Public/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="/Public/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <!--//新样式
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/dpl.css" rel="stylesheet">
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/bui.css" rel="stylesheet">
    <link href="/Public/static/assets/css/dpl-new.css" rel="stylesheet" type="text/css" />
    <link href="/Public/static/assets/css/bui-new.css" rel="stylesheet" type="text/css" />-->
    <link href="/Public/Admin/css/page.css" rel="stylesheet" type="text/css" />
    
</head>
<body>
<div class="container">

<div class="search-grid-container">
    <div id="grid"></div>
</div>
<div id="content" class="hide">
    <form id="J_Form" class="form-horizontal" action="#">
        <input type="hidden" name="id" value="0" />
        <div class="row">
            <div class="control-group span8">
                <label class="control-label"><s>*</s>菜单名称</label>
                <div class="controls">
                    <input name="name" type="text" data-rules="{required:true}" class="input-normal control-text">
                </div>
            </div>
            <div class="control-group span8">
                <label class="control-label"><s>*</s>菜单类型</label>
                <div class="controls bui-form-group-select">
                    <select name="type" data-rules="{required:true}">
                        <option value="">请选择</option>
                        <option value="view">外部链接</option>
                        <option value="click">点击类型</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="control-group span8">
                <label class="control-label"><s>*</s>父菜单</label>
                <div class="controls bui-form-group-select">
                    <select name="pid">
                        <option value="">一级菜单</option>
                        <?php if(is_array($topMenu)): $i = 0; $__LIST__ = $topMenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>
            </div>
            <div class="control-group span8">
                <label class="control-label">菜单URL</label>
                <div class="controls">
                    <input name="url" type="text"  class="input-normal control-text" data-toggle="tooltip" data-placement="bottom" title="站点内部URL，默认使用前台模块并使用' /控制器/方法名 '的方式(如：/Index/index)添加；外部URL必须以http开头" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="control-group span8">
                <label class="control-label">菜单KEY值</label>
                <div class="controls">
                    <input name="key" type="text" class="input-normal control-text" value="monitor" data-toggle="tooltip" data-placement="bottom" title="click等点击类型必须" />
                </div>
            </div>
        </div>
    </form>
</div>

</div>
</body>
<!-- /内容区 -->
<!--<script type="text/javascript" src="/Public/static/assets/js/jquery-1.8.1.min.js"></script>-->
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/Public/static/assets/js/bui.js"></script>
<script type="text/javascript" src="/Public/static/assets/js/config.js"></script>
<script type="text/javascript">
    (function () {
        var ThinkPHP = window.Think = {
            "ROOT": "", //当前网站地址
            "APP": "/index.php", //当前项目地址
            "PUBLIC": "/Public", //项目公共目录地址
            "DEEP": "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL": ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR": ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
</script>

    <script type="text/javascript">
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
        BUI.use('common/page');
        BUI.use('common/search',function (Search) {
                var editing = new BUI.Grid.Plugins.DialogEditing({
                        contentId : 'content', //设置隐藏的Dialog内容
                        autoSave : true, //添加数据或者修改数据时，自动保存
                        triggerCls : 'btn-edit'
                    }),
                    columns = [
                        {title:'菜单编号',dataIndex:'id',width:80},
                        {title:'层级',dataIndex:'pre',width:150},
                        {title:'菜单名称',dataIndex:'name',width:100},
                        {title:'事件KEY',dataIndex:'key',width:100},
                        {title:'菜单链接',dataIndex:'url',width:300},
                        {title:'操作',dataIndex:'',width:200,renderer : function(value,obj){
                            var editStr =  Search.createLink({ //链接使用此方式
                                        id : 'edit' + obj.id,
                                        title : '编辑菜单信息',
                                        text : '打开编辑',
                                        href : '/index.php/Admin/Weixin/edit/id/' + obj.id
                                    }),
                                //页面操作不需要使用Search.createLink
                                editStr1 = '<span class="button button-info btn-edit" title="编辑菜单信息">编辑</span>',
                                    delStr = '<span class="button button-danger btn-del" title="删除菜单信息">删除</span>';
                            return editStr1 + ' ' + delStr;
                        }}
                    ],
                    store = Search.createStore('/index.php/Admin/Weixin/menuList/', {
                        proxy : {
                            save : { //也可以是一个字符串，那么增删改，都会往那么路径提交数据，同时附加参数saveType
                                addUrl : '/index.php/Admin/Weixin/setMenu/type/add',
                                updateUrl : '/index.php/Admin/Weixin/setMenu/type/edit',
                                removeUrl : '/index.php/Admin/Weixin/setMenu/type/remove',
                                savemUrl : '/index.php/Admin/Weixin/setMenu/type/savem'
                            },
                            method : 'POST'
                        },
                        pageSize : 20,
                        autoLoad : true //保存数据后，自动更新
                    }),
                    gridCfg = Search.createGridCfg(columns,{
                        tbar : {
                            items : [
                                {text : '<i class="icon-plus"></i>新建',btnCls : 'button button-small',handler:addFunction},
                                {text : '<i class="icon-remove"></i>删除',btnCls : 'button button-small',handler : delFunction},
                                {text : '<i class="icon-ok-sign"></i>保存到微信',btnCls : 'button button-small',handler : savemFunction}
                            ]
                        },
                        plugins : [editing,BUI.Grid.Plugins.CheckSelection,BUI.Grid.Plugins.AutoFit] // 插件形式引入多选表格
                    });

            var  search = new Search({
                        store : store,
                        gridCfg : gridCfg
                    }),
                 grid = search.get('grid');

            //保存成功时的回调函数,其实更好的方式是直接在保存成功后调用store.load()方法，更新所有数据
            store.on('saved',function (ev) {
                var type = ev.type,         //保存类型，add,remove,update
                    saveData = ev.saveData, //保存的数据
                    data = ev.data;         //返回的数据

                //TO DO
                if(type == 'add'){ //新增记录时后台返回id
                    store.load();
                    BUI.Message.Alert('添加成功！');
                }else if(type == 'update'){
                    BUI.Message.Alert('更新成功！');
                }else if(type == 'savem'){
                    BUI.Message.Alert('同步成功！');
                } else {
                    store.load();
                    BUI.Message.Alert('删除成功！');
                }
            });
            //保存或者读取失败
            store.on('exception',function (ev) {
                store.load();
                BUI.Message.Alert(ev.error);
            });

            function addFunction(){
                var newData = {isNew:true}; //标志是新增加的记录
                editing.add(newData, 'name'); //添加记录后，直接编辑
            }

            //删除操作
            function delFunction(){
                var selections = grid.getSelection();
                delItems(selections);
            }
            // 保存微信菜单，提交到微信服务器
            function savemFunction() {
                BUI.Message.Confirm('确认要提交菜单到微信么？提交之前请确认已编辑好？',function(){
                    store.save('savem');
                },'question');
            }

            function delItems(items){
                var ids = [];
                BUI.each(items,function(item){
                    ids.push(item.id);
                });

                if(ids.length){
                    BUI.Message.Confirm('确认要删除选中的记录么？',function(){
                        store.save('remove',{ids : ids});
                    },'question');
                }
            }

            //监听事件，删除一条记录
            grid.on('cellclick',function(ev){
                var sender = $(ev.domTarget); //点击的Dom
                if(sender.hasClass('btn-del')){
                    var record = ev.record;
                    delItems([record]);
                }
            });
        });
    </script>

</html>