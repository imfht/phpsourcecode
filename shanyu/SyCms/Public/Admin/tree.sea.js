define(function (require,exports,module){
    //var $ = require('jquery');
    require('tabletree');

    var tree={};

    exports.tree = function (name){
        if(name == undefined) name=".TreeTable";
        tree = $(name).treeTable({
            expandLevel : 2,
            column : 1,
        });
    };

    exports.checkbox = function (name){
        if(name == undefined) name=".TreeTable";
        $(name + " input:checkbox").click(function(){
            var node_id = $(this).parents('tr').attr('id');
            var node_checked = true;
            if(!this.checked) node_checked=false;

            var node = tree.getChilds(node_id);
            if(node == undefined){ return true;}

            $.each(node,function (i,v){
                $(name + " tr[id='"+v+"']").find("input:checkbox").prop("checked", node_checked);
            });
        });
    };

    exports.bacth = function (name){
        if(name == undefined) name=".AjaxBatch";
        var dialog = require('dialog');

        $(name).submit(function(e) {
            var url=$(this).data('action');
            var data=$(this).serializeArray();

            //选中
            var obj_id = $('#AjaxListBox input:checkbox:checked');
            if(obj_id.length) $.merge(data,obj_id.serializeArray());

            //排序
            var obj_sort = $('#AjaxListBox input:text');
            if(obj_sort.length){
                var obj_sort_num = 0;
                $.each(obj_sort,function(i,n) {
                    if(obj_sort.eq(i).val() != obj_sort.eq(i).attr('value')){
                        $.merge(data,obj_sort.eq(i).serializeArray());
                    }
                });
            }

            //有效数据为空返回
            if(data.length < 2) return false;

            dialog({id:'LoadingBox'}).show();
            $.ajax({
                url:url,
                data:data,
                type:'POST',
                success:function(result){
                    dialog({
                        id: 'AjaxBatchBox',
                        content: result.info,
                        quickClose: true,
                        onshow: function (){dialog.get('LoadingBox').close().remove();},
                    }).show();
                    if(result.status){
                        setTimeout(function(){window.location.reload();}, 1000);
                    }
                },
                error:function(){},
                beforeSend:function (){}
            });
            return false;
        });
    };

});