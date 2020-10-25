var formError = $('#error-message');
	/*
	 * 申请购买 物资表单信息的提交
	 */
	  var $directory_form = $("#materialFormId");
	  $directory_form.ajaxForm({
	  	type : "post",
	  	dataType : "json",
	  	beforeSubmit : function (formData, jqForm, options) {
	  		$btn = $("#btn-material-action");
	  		var name = $(jqForm).find("input[name='name']").val();
	  		$then = $("#apply_purchase_material_info");
	  		if(name == ""){
	  			formError.text('名称不能为空');
	  			return false;
	  		}
	  		$btn.button('loading');
	  		return true;
	  	},
	  	success : function (res, statusText, xhr, $form) {
	  		$btn.button('reset');
	  		if(res.errcode == 0) {
	  			$then.modal('hide');
	  		}else{
	  			formError.text(res.message);
	  		}
	  	},
	  	 error: function(){
	  		$btn.button('reset');
		  	 }
	  });

	/**申请购买物资
	 * 打开树叶节点创建窗口
	 * @param node
	 */
	function openCreateMaterialInfoCatalogDialog() {
		var $then_leaf = $("#apply_purchase_material_info");
		
		$then_leaf.find("input[name='saveOrUpdate']").val('save');
		$then_leaf.find("input[name='id']").val('');
		$then_leaf.find("input[name='name']").val('');
	    $then_leaf.find("input[name='type']").val('');
	    $then_leaf.find("input[name='description']").val('');
	    $then_leaf.find("input[name='price']").val('');
		$then_leaf.find("select[name='mainType']").val(2);
		$then_leaf.find("input[name='quantity']").val('');
		$then_leaf.find("input[name='approver']").val('');
		formError.text('');
		$btn = $("#btn-material-action");
  		$btn.button('reset');
		$then_leaf.modal({ show : true });
	};
	// 响应点击事件
	$("#applyPurchaseId").click(function(){
		  openCreateMaterialInfoCatalogDialog();
	  });
