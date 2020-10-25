<?php /*a:1:{s:60:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\role\add.html";i:1525915910;}*/ ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('role/add'); ?>" target="frame">
        <div class="modal-body">


            <div class="row">
                <div class="col-md-12">

                        <div class="form-group">
                            <label for="role_name" class="control-label">角色名称</label>
                            <input type="text" class="form-control" id="role_name" name="role_name"  placeholder="菜单名称">
                            <input id="all_rules" name="all_rules" type="hidden" class="form-control">
                            <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  for="all_rules" class="control-label">角色权限</label>
                        <div id="menuTree"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">备注</label>

                           <textarea class="form-control"></textarea>

                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">排序</label>


                            <input type="text"  name="orderby" class="form-control" placeholder="排序">


                    </div>
                </div>


            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">关闭</button>
            <button type="button" id="menuAdd" class="btn btn-info waves-effect waves-light">保存</button>
        </div>
        </form>
    </div>
</div>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/layer/layer.js"></script>
<script src="/static/admins/js/jsTree/jstree.min.js"></script>
<link href="/static/admins/js/jsTree/style.min.css" rel="stylesheet" type="text/css">
<script>
//    $().ready(function() {
//
//        $("#Add").validate({
//            rules: {
//                role_name: "required",
//                all_rules: "required"
//            },
//            messages: {
//                role_name: "角色名称不能为空",
//                all_rules: "角色权限不能为空"
//            },
//            showErrors: function(errorMap, errorList) {
//
//                $.each(errorList, function (i, v) {
//
//                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
//                    return false;
//                });
//                onfocusout: false
//            }
//        });
//
//    });

//    $.validator.setDefaults({
//        submitHandler: function () {
//            dd();
//
//            //getAllSelectNodes();
//
//            //getAllSelectNodes();
//            //$('#all_rules').val("dsfds");
//           // console.log(all_rules);
//            //$('#Add').submit();
//
//        }
//    });

//    function dd() {
//        getAllSelectNodes();
//        $('#Add').submit();
//    }

    $('#menuAdd').click(function () {


        getAllSelectNodes();
        $('#all_rules').val(all_rules);

       $('#Add').submit();


    });

    getMenuTreeData();
    function getMenuTreeData() {
        $.ajax({
            type : "GET",
            url : "<?php echo url('menu/tree'); ?>",
            success : function(menuTree) {
                loadMenuTree(menuTree);
            }
        });
    }

    function loadMenuTree(menuTree) {

        var tree = '['+menuTree+']';
        var treeshow = eval(tree);
        console.log(eval( tree ));
        $('#menuTree').jstree({
            'core' : {
                'data' :treeshow
            },
            "checkbox" : {
                "three_state" : true,
            },
            "plugins" : [ "wholerow", "checkbox" ]
        });


    }
    function getAllSelectNodes() {
        var ref = $('#menuTree').jstree(true); // 获得整个树

        all_rules = ref.get_selected(); // 获得所有选中节点的，返回值为数组

        $("#menuTree").find(".jstree-undetermined").each(function(i, element) {
            all_rules.push($(element).closest('.jstree-node').attr("id"));
        });
        $('#all_rules').val(all_rules);

    }

</script>