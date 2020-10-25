(function (win) {

    win.isEditorChange = false;

    var $btn = $("#btn-directory-action");
    var winTop = $(win.top || win), docTop = $(win.top.document);
    var $then = $("#create_directory");
    var layerIndex;
    
    $employeeMessage = $('#employeeMessage');
    $btnEmployeeAction = $('#btnUserAction');
    $trunkErrorMessage = $('#trunkErrorMessage');
    $btnTrunkAction = $('#btn-directory-action');
    
    $then.on('shown.bs.modal',function () {
        $then.find("input[name='name']").focus();

    });
    $then.on("hidden.bs.modal",function () {
        $btn.button('reset');
    });
    /**
     * 打开组织部门创建窗口
     * @param node
     */
    win.openCreateTrunkCatalogDialog = function (node) {
        var parent_id = node ? node.id : 0;
    	
        $then = $("#create_directory");
        $then.find("input[name='saveOrUpdate']").val('save');
        $then.find("input[name='id']").val('');
        $then.find("input[name='parentId']").val(parent_id);
        $then.find("input[name='name']").val('');
        $then.find("select[name='type']").val(1);
        $then.find("input[name='number']").val('');
        $then.find("textarea[name='description']").val('');
        $trunkErrorMessage.text('');
        $btnTrunkAction.button('reset');
        $then.modal({ show : true });
    };
    /**
     * 打开树叶节点(即员工信息)创建窗口
     * @param node
     */
    win.openCreateUserCatalogDialog = function (node) {
    	var parent_id = node ? node.id : 0;
    	var $then_leaf = $("#create_user_info");
    	
    	$then_leaf.find("input[name='saveOrUpdate']").val('save');
    	$then_leaf.find("input[name='id']").val('');
    	$then_leaf.find("input[name='parentId']").val(parent_id);
    	$then_leaf.find("input[name='name']").val('');
        $then_leaf.find("input[name='password']").val('');
        $then_leaf.find("input[name='password2']").val('');
        $then_leaf.find("input[name='email']").val('');
        $then_leaf.find("input[name='number']").val('');
        $then_leaf.find("input[name='jobType']").val('1');
        $employeeMessage.text('');
        $btnEmployeeAction.button('reset');
    	$then_leaf.modal({ show : true });
    };
    /*
     * jstree 删除一个节点
     */
    win.deleteNodeDialog = function (node) {
    	///organization/node/{nodeId}/delete/self
    	$isSelf = false;
    	$.ajax({  
            type : "get",  
            url : "/admin/organization/node/"+escape(node.id)+"/delete/self", 
            data : "",  
            async : false,  
            success : function(data){  
            	if(data.message == "true"){
            		layer.msg('不能删除表示自己账号的节点');
            		$isSelf = true;
            		return false;
            	}
            },
            error: function(){
          	layer.msg('删除失败',{icon: 2})
          	return false;
            }
            }); 
    	if($isSelf){
    		return false;
    	}
    	//执行删除操作
        var index = layer.confirm('你确定要删除该节点吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
        	$.ajax({  
	              type : "get",  
	              url : "/admin/tree/organization/delete/"+escape(node.id), //前缀是 /admin/ 
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
        	$editThen = $("#create_user_info");
        }else{
        	$editThen = $("#create_directory");
        }
        	
        $.getJSON("tree/organization/anode/"+escape(node.id)+"/data/get/",function(data,status){
        	// 隐藏字段，提示服务器是保存还是更新
            $editThen.find("input[name='saveOrUpdate']").val('update');
        	$editThen.find("input[name='parentId']").val(parentId);
        	$editThen.find("input[name='id']").val(data.id);
            $editThen.find("input[name='name']").val(data.name);
            $editThen.find("input[name='number']").val(data.number);
            if( $isLeaf == "1"){
                $editThen.find("select[name='type']").val(data.type);
                $editThen.find("textarea[name='description']").val(data.description);
                $trunkErrorMessage.text('');
                $btnTrunkAction.button('reset');
            }else if( $isLeaf == "2"){
                 $editThen.find("input[name='email']").val(data.email);
                 $editThen.find("input[name='number']").val(data.number);
                 $editThen.find("input[name='jobType']").val(data.jobType);
                 $employeeMessage.text('');
                 $btnEmployeeAction.button('reset');
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
    //加载指定的文档
    win.loadNodeInfo = function (selected) {
    	 //加载指定的节点信息
        win.loadNodeInfo = function (selected) {
            var index = layer.load(1, {
                shade: [0.1,'#fff'] //0.1透明度的白色背景
            });
    ///tree/material/anode/{nodeId}/data/get
            $.get("tree/organization/anode/"+escape(selected.node.id)+"/data/get/table"
            ).done(function (data) {
                layer.close(index);
                $('#nodeInfoShow').html(data);
                
            }).fail(function () {
                layer.close(index);
                layer.msg("加载信息失败");
            });
        };
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
            if(number ==""){
            	$trunkErrorMessage.text('编号不能为空');
                return false;
            }
            var id = $(jqForm).find("input[name='id']").val();
            var node = win.treeCatalog.get_node(id);
            $then = $("#create_directory");
            $btnTrunkAction.button('loading');
            return true;
        },
        success : function (res, statusText, xhr, $form) {
            if(res.errcode == 0) {
               /* var data = { "id" : res.data.id,'parent' : res.data.parent_id,"text" : res.data.name};
*/				var data = { "id" : res.data['id'],"parent" : res.data['parent_id'],
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
                $btnTrunkAction.button('reset');
            }
        }
    });
  /*
   * 员工表单信息的提交
   */
    var $directory_form = $("#userFormId");
    $directory_form.ajaxForm({
    	type : "post",
    	dataType : "json",
    	beforeSubmit : function (formData, jqForm, options) {
    		var name = $(jqForm).find("input[name='name']").val();
    		if(name == ""){
    			$employeeMessage.text('名称不能为空');
    			return false;
    		}
    		var id = $(jqForm).find("input[name='id']").val();
    		var password = $(jqForm).find("input[name='password']").val();
    		if(password == ""){
    			$employeeMessage.text('密码不能为空');
    			return false;
    		}
    		var password2 = $(jqForm).find("input[name='password2']").val();
    		if(password2 =="" || password2 != password){
    			$employeeMessage.text('两次密码不一致');
    			return false;
    		}
    		var node = win.treeCatalog.get_node(id);
    		$then = $("#create_user_info");
    		
    		
    		if(password != password2){
    			$('#password2Tips').text('两次密码不一致');
    			return false;
    		}
    		$btnEmployeeAction.button('loading');
    		return true;
    	},
    	success : function (res, statusText, xhr, $form) {
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
    			$employeeMessage.text(res.message);
    	        $btnEmployeeAction.button('reset');
    		}
    	}
    });
})(window);