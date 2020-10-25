(function (win) {

    win.isEditorChange = false;

    var $btn = $("#btn-directory-action");
    var winTop = $(win.top || win), docTop = $(win.top.document);
    var $then = $("#create_directory");
    var formError = $then.find('#error-message');
    var layerIndex;
    $trunkErrorMessage = $('#trunkErrorMessage');
    $btnDirectoryAction = $('#btn-directory-action');
    $errorMessage = $('#error-message');
    $btnMaterialAction = $("#btn-material-action");
    
    $then.on('shown.bs.modal',function () {
        $then.find("input[name='name']").focus();

    });
    $then.on("hidden.bs.modal",function () {
        $btn.button('reset');
    });
   
    /**
     * 打开目录创建窗口
     * @param node
     */
    win.openCreateTrunkCatalogDialog = function (node) {
        var parent_id = node ? node.id : 0;
        $then = $("#create_directory");
        $then.find("input[name='saveOrUpdate']").val('save');
        $then.find("input[name='id']").val('');
        $then.find("input[name='parentId']").val(parent_id);
        $then.find("input[name='name']").val('');
        $then.find("select[name='type']").val(2);
        $then.find("input[name='number']").val('');
        $then.find("textarea[name='description']").val('');
        $trunkErrorMessage.text('');
        $btnDirectoryAction.button('reset');
        $then.modal({ show : true });
    };
    /**
     * 打开树叶节点创建窗口
     * @param node
     */
    win.openCreateMaterialInfoCatalogDialog = function (node) {
    	var parent_id = node ? node.id : 0;
    	var $then_leaf = $("#create_material_info");
    	
    	$then_leaf.find("input[name='saveOrUpdate']").val('save');
    	$then_leaf.find("input[name='id']").val('');
    	$then_leaf.find("input[name='parentId']").val(parent_id);
    	$then_leaf.find("input[name='name']").val('');
        $then_leaf.find("input[name='type']").val('');
        $then_leaf.find("input[name='number']").val('');
        $then_leaf.find("input[name='picture']").val('');
        $then_leaf.find("input[name='pictureUrl']").val('');
        $then_leaf.find("img[id='imageShow']").attr('src','');
        $then_leaf.find("textarea[name='description']").val('');
        $then_leaf.find("input[name='price']").val('');
    	$then_leaf.find("select[name='mainType']").val('2');
    	$errorMessage.text('');
    	$btnMaterialAction.button('reset');
    	$then_leaf.modal({ show : true });
    };

    /*
     * jstree 删除一个节点
     */
    win.deleteNodeDialog = function (node) {
        var index = layer.confirm('你确定要删除该节点吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
        	$.ajax({  
	              type : "get",  
	              url : "tree/delete/"+escape(node.id), //前缀是 /admin/ 
	              data : "",  
	              async : false,  
	              success : function(data){  
	                layer.msg('删除成功', {icon: 1});
	                win.treeCatalog.delete_node(node);
	              },
	              error: function(){
	            	layer.msg('删除失败',{icon: 2})
	              }
	              }); 

        });
    };

    /*
     * 编辑当前节点信息
     */
    win.editCurrentInfoDialog = function (node) {
        var node_id = node ? node.id : 0;
        var text = node ? node.text : '';
        var parentId = node ? node.parent : '#';
        var $isLeaf = node.id.charAt(node.id.length-1);// 判断是目录还是叶子节点
        var $editThen ="";
        if($isLeaf == "2"){
        	$editThen = $("#create_material_info");
        }else{
        	$editThen = $("#create_directory");
        }
        	
        $.getJSON("/admin/tree/material/anode/"+escape(node.id)+"/data/get",function(data,status){
        	// 隐藏字段，提示服务器是保存还是更新
            $editThen.find("input[name='saveOrUpdate']").val('update');
        	$editThen.find("input[name='parentId']").val(parentId);
        	$editThen.find("input[name='id']").val(data.id);
            $editThen.find("input[name='name']").val(data.name);
            $editThen.find("input[name='number']").val(data.number);
            $editThen.find("textarea[name='description']").val(data.description);
            if($isLeaf == "2"){
            	$editThen.find("input[name='price']").val(data.price);
            	$editThen.find("select[name='mainType']").val(data.mainType);
            	$editThen.find("input[name='type']").val(data.type);
            	$editThen.find("input[name='pictureUrl']").val(data.pictureUrl);
            	$editThen.find("img[id='imageShow']").attr('src','/picture/download/'+data.pictureUrl);
            	$errorMessage.text('');
            	$btnMaterialAction.button('reset');
            }else{
                $editThen.find("select[name='type']").val(data.type);
                $trunkErrorMessage.text('');
            }
        });
        $editThen.modal({ show : true });
    };

    win.getSiblingSort = function (node) {
        var data = [];
        for(key in node.children){
            var index = data.length;
            data[index] = {
                "id" : node.children[key],
                "sort" : key,
                "parent" : node.id
            };
        }
        return data;
    };
    //加载指定的节点信息
    win.loadNodeInfo = function (selected) {
        var index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
///tree/material/anode/{nodeId}/data/get
        $.get("tree/material/anode/"+escape(selected.node.id)+"/data/get/table"
        ).done(function (data) {
            layer.close(index);
            $('#nodeInfoShow').html(data);
            
        }).fail(function () {
            layer.close(index);
            layer.msg("加载信息失败");
        });
    };
    /**
     * 目录表提交
     */
    var $directory_form = $("#directoryFormId");
    $directory_form.ajaxForm({
        type : "post",
        dataType : "json",
        beforeSubmit : function (formData, jqForm, options) {
            var name = $(jqForm).find("input[name='name']").val();
            if(name == ""){
            	$trunkErrorMessage.text('目录名称不能为空');
                return false;
            }
            var number = $(jqForm).find("input[name='number']").val();
            if(number== ""){
            	$trunkErrorMessage.text('编号不能为空');
                return false;
            }
            var id = $(jqForm).find("input[name='id']").val();
            var node = win.treeCatalog.get_node(id);
            $then = $("#create_directory");
            $btnDirectoryAction.button('loading');
            return true;
        },
        success : function (res, statusText, xhr, $form) {
        	$btnDirectoryAction.button('reset')
            if(res.errcode == 0) {
                var data = { "id" : res.data['id'],"parent" : res.data['parent_id'],
                		"type" : res.data['type'],"text" : res.data['name']};
                var node = win.treeCatalog.get_node(data.id);
                if(node){
                    win.treeCatalog.rename_node({"id":data.id},data.text);
                }else {
                	
                var parent_node = win.treeCatalog.get_node(data.parent);
   		    	 if(!win.treeCatalog.is_closed( parent_node ) || data.parent == '#'){
   	    			var result = win.treeCatalog.create_node(res.data.parent_id, data, 'last');
   		    	 }else{
   		    		win.treeCatalog.open_node( parent_node );
   		    	 }
                    win.treeCatalog.deselect_all();
                    win.treeCatalog.select_node(data);
                }
                $then.modal('hide');
            }else{
            	//$directory_form.find('#trunkErrorMessage').text(res.message);
            	$trunkErrorMessage.text(res.message);
            }
        }
    });
  /*
   * 物资表单信息的提交
   */
    var $directory_form = $("#materialFormId");
    $directory_form.ajaxForm({
    	type : "post",
    	dataType : "json",
    	beforeSubmit : function (formData, jqForm, options) {
    		var name = $(jqForm).find("input[name='name']").val();
    		var number = $(jqForm).find("input[name='number']").val();
    		var id = $(jqForm).find("input[name='id']").val();
    		var node = win.treeCatalog.get_node(id);
    		$then = $("#create_material_info");
    		if(name == ""){
    			$errorMessage.text('名称不能为空');
    			return false;
    		}
    		$btnMaterialAction.button('loading');
    		// 判断编号是否重复
    		
    		return true;
    	},
    	success : function (res, statusText, xhr, $form) {
    		$btnMaterialAction.button('reset')
    		if(res.errcode == 0) {
    			var data = { "id" : res.data['id'],"parent" : res.data['parent_id'],
                		"type" : res.data['type'],"text" : res.data['name']};
    			var node = win.treeCatalog.get_node(data.id);
    			if(node){
    				win.treeCatalog.rename_node({"id":data.id},data.text);
    			}else {
                    var parent_node = win.treeCatalog.get_node(data.parent);
                 // 判断节点是否展开， 根节点一直是关闭的
      		    	 if(!win.treeCatalog.is_closed( parent_node ) || data.parent == '#'){
      	    			var result = win.treeCatalog.create_node(res.data.parent_id, data, 'last');
      		    	 }else{
      		    		win.treeCatalog.open_node( parent_node );
      		    	 }
      		    	 
    				win.treeCatalog.deselect_all();
    				win.treeCatalog.select_node(data);
    			}
    			$then.modal('hide');
    		}else{
    			$errorMessage.text(res.message);
    		}
    	}
    });
  
})(window);