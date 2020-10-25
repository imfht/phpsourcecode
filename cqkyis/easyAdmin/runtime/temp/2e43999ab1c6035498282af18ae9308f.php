<?php /*a:1:{s:61:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\good\cate.html";i:1526261549;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link href="/static/admins/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/core.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/components.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/pages.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/menu.css" rel="stylesheet" type="text/css">
    <link href="/static/admins/css/responsive.css" rel="stylesheet" type="text/css">

    <script src="/static/admins/js/modernizr.min.js"></script>
    <style>
        .icon-list i{
            font-size: 30px;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">分类列表</h4>
    </div>
    <div class="panel-body">
        <div class="inbox-widget nicescroll mx-box" tabindex="5000" style="overflow: hidden; outline: none;">

            <div id="menuTree"></div>
        </div>
    </div>
</div>
<script src="/static/admins/js/jquery.min.js" ></script>
<script src="/static/admins/js/layer/layer.js"></script>
<script src="/static/admins/js/jsTree/jstree.min.js"></script>
<link href="/static/admins/js/jsTree/style.min.css" rel="stylesheet" type="text/css">
<script>

    getMenuTreeData();
    function getMenuTreeData() {
        $.ajax({
            type : "GET",
            url : "<?php echo url('goodcate/catetree'); ?>",
            success : function(menuTree) {
                loadMenuTree(menuTree);
            }
        });
    }

    function loadMenuTree(menuTree) {

        var tree = '['+menuTree+']';
        var treeshow = eval(tree);
        $('#menuTree').jstree({
            'core' : {
                'data' :treeshow
            },
            "plugins" : [ "search" ]
        });
        $('#menuTree').jstree().open_all();
    }



    var index = parent.layer.getFrameIndex(window.name);

    $('#menuTree').on("changed.jstree", function(e, data) {
        //console.log(data.selected[0]);
      //  console.log(data.node.text);
        parent.layer.close(index);
        parent.$('#dept_id').val(data.selected[0]);
        parent.$('#dept_ids').val(data.node.text);
    });

</script>
</body>
</html>