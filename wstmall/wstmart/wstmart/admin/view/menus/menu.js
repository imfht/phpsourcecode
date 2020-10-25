var zTree,mmg,rMenu;
function layout(){
	var h = WST.pageHeight();
	var w = WST.pageWidth();
	$('.j-layout').width(w-8);
	$('.j-layout-left').width(200).height(h-125);
	$('.j-layout-center').width(w-220).height(h-125);
	$('#headTip').WSTTips({width:90,height:35,callback:function(v){
		 var diff = v?125:53;
		 $('.j-layout-left').height(h-diff);
		 $('#menuTree').height(h-diff-40);
		 $('.j-layout-center').height(h-diff);
    }});
}
function initGrid(){
	var cols = [
            {title:'权限名称', name:'privilegeName', width: 130},
            {title:'权限代码', name:'privilegeCode' ,width:60},
            {title:'是否菜单权限', name:'isMenuPrivilege' ,width:50,renderer: function(val){
                return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 是&nbsp;</span>":"<span class='statu-no'><i class='fa fa-ban'></i> 否&nbsp;</span>";
            }},
            {title:'权限资源', name:'privilegeUrl' ,width:150},
            {title:'关联资源', name:'otherPrivilegeUrl' },
            {title:'操作', name:'' ,width:110, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
			    if(WST.GRANT.QXGL_02)h += "<button  class='btn btn-blue' onclick='javascript:getForEdit(" + item['privilegeId'] + ")'><i class='fa fa-pencil'></i>编辑</button> ";
			    if(WST.GRANT.QXGL_03)h += "<button  class='btn btn-red' onclick='javascript:toDel(" + item['privilegeId'] + ")'><i class='fa fa-trash-o'></i>删除</button> ";
                return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: 'auto',indexCol: true, cols: cols,method:'POST',nowrap: true,
        url: WST.U("admin/privileges/listQuery"), fullWidthRows: true, autoLoad: false
    });  
    $('#m_add').click(function(){
		treeNode = zTree.getSelectedNodes()[0];
        editMenu({menuId:0,menuName:'',menuIcon:'',parentId:treeNode.id,pnode:treeNode,menuSort:0});
	});
	$('#m_edit').click(function(){
		treeNode = zTree.getSelectedNodes()[0];
        getForEditMenu(treeNode.id);
        return false;
	});
	$('#m_del').click(function(){
		treeNode = zTree.getSelectedNodes()[0];
                  	layer.confirm('您确定要删除该菜单['+treeNode.name+']吗？', {btn: ['确定','取消']}, function(){
                  	    var loading = WST.msg('正在提交请求，请稍后...', {icon: 16,time:60000});
                  	    $.post(WST.U('admin/menus/del'),{id:treeNode.id},function(data,textStatus){
                  		     layer.close(loading);
                  		     var json = WST.toAdminJson(data);
                  		     if(json.status=='1'){
                  		          WST.msg("操作成功",{icon:1});
                  		          zTree.reAsyncChildNodes(treeNode.getParentNode(), "refresh",true);
                  		     }else{
                  		          WST.msg(json.msg,{icon:2});
                  		     }
                  		 });
                      });
        return false;
	})
}
$(window).resize(function(){layout();});
$(function(){
	layout();
	$('#menuTree').height(WST.pageHeight()-153);
	var setting = {
	      view: {
	           selectedMulti: false,
	           dblClickExpand:false
	      },
	      async: {
	           enable: true,
	           url:WST.U('admin/menus/listQuery'),
	           autoParam:["id", "name=n", "level=lv"]
	      },
	      callback:{
	           onRightClick: onRightClick,
	           onClick: onClick,
	           onAsyncSuccess: onAsyncSuccess
	      }
	};
	$.fn.zTree.init($("#menuTree"), setting);
	zTree = $.fn.zTree.getZTreeObj("menuTree");
	rMenu = $("#rMenu");
	initGrid();
})
function loadPrivileges(id){
    mmg.load({page:1,id:id});       
}
function onAsyncSuccess(event, treeId, treeNode, msg){
	var json = WST.toAdminJson(msg);
	if(json && json.id==0){
		var treeNode = zTree.getNodeByTId('menuTree_1');
		zTree.reAsyncChildNodes(treeNode, "refresh",true);
		zTree.expandAll(treeNode,true);
	}
}
function onClick(e,treeId, treeNode){
	if(treeNode.id>0){
	      $('.wst-toolbar').show();
	      $('#maingrid').show();
	}else{
	    $('.wst-toolbar').hide();
	    $('#maingrid').hide();
	}
	loadPrivileges(treeNode.id);
}
function onRightClick(event, treeId, treeNode) {
	if(!treeNode)return;
	if(!WST.GRANT.CDGL_01 && !WST.GRANT.CDGL_02 && !WST.GRANT.CDGL_03)return;
	if(!treeNode && event.target.tagName.toLowerCase() != "button" && $(event.target).parents("a").length == 0) {
		zTree.cancelSelectedNode();
		showRMenu("root", event.clientX, event.clientY);
	}else if(treeNode && !treeNode.noR) {
		zTree.selectNode(treeNode);
		showRMenu("node", event.clientX, event.clientY);
	}
}
function showRMenu(type, x, y) {
	$("#rMenu ul").show();
    y += document.body.scrollTop;
    x += document.body.scrollLeft;
    rMenu.css({"top":y+"px", "left":x+"px", "visibility":"visible"});
	$("body").bind("mousedown", onBodyMouseDown);
}
function hideRMenu() {
	if (rMenu) rMenu.css({"visibility": "hidden"});
	$("body").unbind("mousedown", onBodyMouseDown);
}
function onBodyMouseDown(event){
	if (!(event.target.id == "rMenu" || $(event.target).parents("#rMenu").length>0)) {
		rMenu.css({"visibility" : "hidden"});
	}
}
function getForEditMenu(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/menus/get'),{id:id},function(data,textStatus){
          layer.close(loading);
          var json = WST.toAdminJson(data);
          if(json.menuId){
          	  editMenu(json);
          }else{
          	  WST.msg(json.msg,{icon:2});
          }
   });
}	                    		
function editMenu(obj){
	WST.setValues(obj);
	var box = WST.open({ title:(obj.menuId==0)?'新增菜单':"编辑菜单",type: 1,area: ['660px', '300px'],
	                content:$('#menuBox'),
	                btn:['确定','取消'],
	                end:function(){$('#menuBox').hide()},
	                yes: function(index, layero){
	                	if(!$('#menuName').isValid())return;
		                var params = WST.getParams('.ipt2');
		                params.menuId = obj.menuId;
		                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		                $.post(WST.U('admin/menus/'+((params.menuId)?"edit":"add")),params,function(data,textStatus){
		                	layer.close(loading);
		                	var json = WST.toAdminJson(data);
		                	if(json.status=='1'){
		                	    WST.msg("操作成功",{icon:1});
		                		layer.close(box);
		                	    $('#menuForm')[0].reset();
		                		treeNode = zTree.getSelectedNodes()[0];
		                		if(params.menuId){
			                		zTree.reAsyncChildNodes(treeNode.getParentNode(), "refresh",true);
		                	    }else{
		                			zTree.reAsyncChildNodes(treeNode, "refresh",true);
		                		}
		                	}else{
		                			WST.msg(json.msg,{icon:2});
		                	}
		                });
	            }});
}

function getForEdit(id){
	 var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
     $.post(WST.U('admin/privileges/get'),{id:id},function(data,textStatus){
           layer.close(loading);
           var json = WST.toAdminJson(data);
           if(json.privilegeId){
           		WST.setValues(json);
           		toEdit(json.privilegeId);
           }else{
           		WST.msg(json.msg,{icon:2});
           }
    });
}

function toEdit(id){
	var title = "新增权限";
	if(id>0){
		title = "编辑权限";
	}else{
		$('#privilegeForm')[0].reset();
	}
	$("#privilegeId").val(id);
	var box = WST.open({title:title,type:1,content:$('#privilegeBox'),area: ['640px', '450px'],btn:['确定','取消'],
              end:function(){$('#privilegeBox').hide()},
		      yes:function(){
		            if(!$('#privilegeName').isValid())return;
		            if(!$('#privilegeCode').isValid())return;
	                var params = WST.getParams('.ipt');
	                params.menuId = zTree.getSelectedNodes()[0].id;
	                params.id = id;
	                var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           		$.post(WST.U('admin/privileges/'+((id==0)?"add":"edit")),params,function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	$('#privilegeForm')[0].reset();
	           			    	layer.close(box);
	           		            loadPrivileges(params.menuId);
	           			  }else{
	           			        WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	          }});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该权限吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/privileges/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		            loadPrivileges(zTree.getSelectedNodes()[0].id);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function checkPrivilegeCode(obj){
	var privilegeId = $("#privilegeId").val();
	if($.trim(obj.value)=='')return;
	var loading = WST.msg('正在检测代码是否存在，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/privileges/checkPrivilegeCode'),{privilegeId:privilegeId,code:obj.value},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status!='1'){
			WST.msg(json.msg,{icon:2});
			$('#privilegeCode').val('');
		}
	});
}