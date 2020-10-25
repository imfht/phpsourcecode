
function openCreateTrunkCatalogDialog(node){
	layer.open({
		  type: 2,
		  area: ['700px', '530px'],
		  fixed: false, //不固定
		  maxmin: true,
		  content: 'treeTrunk/create/'+node.id//当前url 所在目录下的 ， treeTrunk目录下的 create.
		});
}

function openCreateMaterialInfoCatalogDialog(node){
	var index = layer.open({
		  type: 2,
		  area: ['700px', '530px'],
		  fixed: false, //不固定
		  maxmin: true,
		  content: 'material/create/'+node.id//当前url 所在目录下的 ， treeTrunk目录下的 create.前缀是 /aaa/admin/tree/
		});
	
}

function editCurrentInfoDialog(node){
	
}

function deleteDocumentDialog(node){
	
	var isLeaf = node.id.charAt(node.id.length-1);
	var confirmInfo = isLeaf == 2 ? '确认删除: \"'+node.text+'\"  的信息吗 ?' : '确认删除: \“'+node.text+'\” 以及该目录下的子节点吗?';
	layer.confirm(confirmInfo, {
		  btn: ['删除','取消'] //按钮
		}, function(){
			$.ajax({  
	              type : "get",  
	              url : "tree/delete/"+escape(node.id), //前缀是 /aaa/admin/ 
	              data : "",  
	              async : false,  
	              success : function(data){  
	                layer.msg('删除成功', {icon: 1});
	              }  
	              }); 
		  
		}, function(){//点击取消按钮时的操作
		  
		});
}