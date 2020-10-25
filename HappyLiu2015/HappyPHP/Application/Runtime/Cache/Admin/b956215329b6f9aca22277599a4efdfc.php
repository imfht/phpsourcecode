<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html lang='zh-cn'>
<head>
    <meta charset="utf-8" />
    <title>后台管理系统</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--//老样式-->
    <link href="/test/Public/static/assets/css/dpl-min.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/bui-min.css" rel="stylesheet" type="text/css" />
    <!--//新样式
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/dpl.css" rel="stylesheet">
    <link href="http://g.alicdn.com/bui/bui/1.1.21/css/bs3/bui.css" rel="stylesheet">
    <link href="/test/Public/static/assets/css/dpl-new.css" rel="stylesheet" type="text/css" />
    <link href="/test/Public/static/assets/css/bui-new.css" rel="stylesheet" type="text/css" />-->
    <link href="/test/Public/Admin/css/page.css" rel="stylesheet" type="text/css" />
    
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
                    <input name="text" type="text" data-rules="{required:true}" class="input-normal control-text">
                </div>
            </div>
            <div class="control-group span8">
                <label class="control-label"><s>*</s>菜单标识</label>
                <div class="controls">
                    <input name="tag" type="text" data-rules="{required:true}" class="input-normal control-text">
                </div>
            </div>
        </div>
        <?php if($level == 1): ?><div class="row">
            <div class="control-group span8">
                <label class="control-label">默认打开菜单标识</label>
                <div class="controls">
                    <input name="homepage" type="text" data-toggle="tooltip" data-placement="bottom" title="从有URL的子菜单中选取一个" class="input-normal control-text" />
                </div>
            </div>
            <div class="control-group span8">
                <label class="control-label">菜单前图标样式</label>
                <div class="controls">
                    <input name="icon" type="text" class="input-normal control-text" value="monitor" data-toggle="tooltip" data-placement="bottom" title="（默认值）监控：monitor 权限：permission 仓库：storage 库存：inventory 用户：user 订单：order 商品：product 供应商，商家：supplier 费用：cost  聚划算：gather 铺货：distribution 货品：goods" />
                </div>
            </div>
        </div><?php endif; ?>
        <?php if($level == 3): ?><div class="row">
            <div class="control-group span8">
                <label class="control-label">菜单URL</label>
                <div class="controls">
                    <input name="href" type="text" data-rules="{required:true}" class="input-normal control-text">
                </div>
            </div>
            <div class="control-group span8">
            <label class="control-label"><s>*</s>是否可关闭</label>
            <div class="controls bui-form-group-select">
                <select name="closeable"  data-rules="{required:true}">
                    <option value="1">可关闭</option>
                    <option value="0">不可关闭</option>
                </select>
            </div>
        </div><?php endif; ?>
        </div>
    </form>
</div>

</div>
</body>
<!-- /内容区 -->
<!--<script type="text/javascript" src="/test/Public/static/assets/js/jquery-1.8.1.min.js"></script>-->
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/bui.js"></script>
<script type="text/javascript" src="/test/Public/static/assets/js/config.js"></script>
<script type="text/javascript">
    (function () {
        var ThinkPHP = window.Think = {
            "ROOT": "/test", //当前网站地址
            "APP": "/test/index.php", //当前项目地址
            "PUBLIC": "/test/Public", //项目公共目录地址
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
            var enumObj = {"1":"可关闭","0":"不能关闭"},
                    editing = new BUI.Grid.Plugins.DialogEditing({
                        contentId : 'content', //设置隐藏的Dialog内容
                        autoSave : true, //添加数据或者修改数据时，自动保存
                        triggerCls : 'btn-edit'
                    }),
                    columns = [
                        <?php if($level == 3): ?>{title:'菜单编号',dataIndex:'id',width:80},
                        <?php else: ?>
                        {title:'菜单编号',dataIndex:'id',width:80,renderer:function(v, obj){
                            return Search.createLink({
                                id : 'detail' + v,
                                title : obj.text+'[子菜单]',
                                text : v,
                                href : '/test/index.php/Admin/Menu/index/pid/' + v
                            });
                        }},<?php endif; ?>
                        {title:'菜单名称',dataIndex:'text',width:100},
                        {title:'菜单标识',dataIndex:'tag',width:100},
                        <?php if($level == 3): ?>{title:'可否关闭',dataIndex:'closeable',width:60,renderer:BUI.Grid.Format.enumRenderer(enumObj)},
                        {title:'菜单链接',dataIndex:'href',width:300},<?php endif; ?>
                        {title:'操作',dataIndex:'',width:200,renderer : function(value,obj){
                            var editStr =  Search.createLink({ //链接使用此方式
                                        id : 'edit' + obj.id,
                                        title : '编辑菜单信息',
                                        text : '打开编辑',
                                        href : '/test/index.php/Admin/Menu/edit/id/' + obj.id
                                    }),
                                //页面操作不需要使用Search.createLink
                                editStr1 = '<span class="button button-info btn-edit" title="编辑菜单信息">编辑</span>',
                                delStr = '<span class="button button-danger btn-del" title="删除菜单信息">删除</span>';
                            return editStr1 + ' ' + delStr;
                        }}
                    ],
                    store = Search.createStore('/test/index.php/Admin/Menu/menuList/pid/'+<?php echo ($pid); ?>, {
                        proxy : {
                            save : { //也可以是一个字符串，那么增删改，都会往那么路径提交数据，同时附加参数saveType
                                addUrl : '/test/index.php/Admin/menu/index/type/add',
                                updateUrl : '/test/index.php/Admin/menu/index/type/edit',
                                removeUrl : '/test/index.php/Admin/menu/index/type/remove'
                            },
                            method : 'POST'
                        },
                        pageSize : 10,
                        autoLoad : true //保存数据后，自动更新
                    }),
                    gridCfg = Search.createGridCfg(columns,{
                        tbar : {
                            items : [
                                {text : '<i class="icon-plus"></i>新建',btnCls : 'button button-small',handler:addFunction},
                                {text : '<i class="icon-remove"></i>删除',btnCls : 'button button-small',handler : delFunction}
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
                    saveData.id = data.id;
                    grid.updateItem(saveData);
                    store.update();
                    BUI.Message.Alert('添加成功！');
                }else if(type == 'update'){
                    BUI.Message.Alert('更新成功！');
                }else{
                    store.load();
                    BUI.Message.Alert('删除成功！');
                }
            });
            //保存或者读取失败
            store.on('exception',function (ev) {
                BUI.Message.Alert(ev.error);
            });

            function addFunction(){
                var newData = {isNew:true, closeable:1}; //标志是新增加的记录
                editing.add(newData, 'text'); //添加记录后，直接编辑
            }

            //删除操作
            function delFunction(){
                var selections = grid.getSelection();
                delItems(selections);
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