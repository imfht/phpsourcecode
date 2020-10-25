var grid,oldData = {},oldorderData = {};
function initGrid(){	
	grid = $('#maingrid').WSTGridTree({
		url:WST.U('admin/goodscats/pageQuery'),
		pageSize:10000,
		pageSizeOptions:[10000],
		height:'99%',
        width:'100%',
        minColToggle:6,
        delayLoad :true,
        rownumbers:true,
        columns: [
	        { display: '分类名称', width: 230,name: 'catName', id:'catId', align: 'left',isSort: false,render: function (item)
                {
                	oldData[item.catId] = item.catName;
                    return '<input type="text" size="40" value="'+item.catName+'" onblur="javascript:editName('+item.catId+',this)" style="width:200px"/>';
            }},
	        { display: '分类名缩写', width: 150,name: 'simpleName', id:'catId', align: 'left',isSort: false,render: function (item)
                {
                	oldData[item.catId] = item.simpleName;
                    return '<input type="text" size="40" maxLength="4" value="'+item.simpleName+'" onblur="javascript:editsimpleName('+item.catId+',this)" style="width:120px"/>';
            }},
            { display: '推荐楼层', width: 70, name: 'isFloor',isSort: false,
                render: function (itemf)
                {
                    return '<input type="checkbox" '+((itemf.isFloor==1)?"checked":"" )+'  class="ipt" lay-skin="switch" lay-filter="isFloor" data="'+itemf.catId+'" lay-text="是|否">';
                }
            },
            { display: '是否显示', width: 70, name: 'isShow',isSort: false,
                render: function (item)
                {
                    return '<input type="checkbox" '+((item.isShow==1)?"checked":"")+' class="ipt" lay-skin="switch" lay-filter="isShow" data="'+item.catId+'" lay-text="显示|隐藏">';
                }
            },
            { display: '排序号', name: 'catSort',width: 50,isSort: false,render: function (item)
                {
                	oldorderData[item.catId] = item.catSort;
                    return '<input type="text" style="width:50px" value="'+item.catSort+'" onblur="javascript:editOrder('+item.catId+',this)"/>';
            }},
            { display: '佣金', width: 50, name: 'commissionRate',isSort: false,
                render: function (item)
                {
                    return item["commissionRate"]+'%';
                }
            },
	        { display: '操作', name: 'op',width: 170,isSort: false,
	        	render: function (rowdata){
		            var h = "";
			        if(WST.GRANT.SPFL_01)h += "<a class='btn btn-blue' href='javascript:toEdit("+rowdata["catId"]+",0)'><i class='fa fa-plus'></i>新增子分类</a> ";
		            if(WST.GRANT.SPFL_02)h += "<a class='btn btn-blue' href='javascript:toEdit("+rowdata["parentId"]+","+rowdata["catId"]+")'><i class='fa fa-pencil'></i>修改</a> ";
		            if(WST.GRANT.SPFL_03)h += "<a class='btn btn-red' href='javascript:toDel("+rowdata["parentId"]+","+rowdata["catId"]+")'><i class='fa fa-trash-o'></i>删除</a> "; 
		            return h;
	        	}}
        ],
        callback:function(){
		    layui.form.render();
	    }
    });
    layui.form.on('switch(isShow)', function(data){
        var id = $(this).attr("data");
        if(this.checked){
            toggleIsShow(id, 1);
        }else{
            toggleIsShow(id, 0);
        }
   });
   layui.form.on('switch(isFloor)', function(data){
        var id = $(this).attr("data");
        if(this.checked){
            toggleIsFloor(id, 1);
        }else{
            toggleIsFloor(id, 0);
        }
   });
}

function toggleIsFloor(id,isFloor){
	if(!WST.GRANT.SPFL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/goodscats/editiIsFloor'),{id:id,isFloor:isFloor},function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toAdminJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
				grid.reload(id);
		  }else{
		    	WST.msg(json.msg,{icon:2});
		  }
	});
}

function toggleIsShow(id,isShow){
	if(!WST.GRANT.SPFL_02)return;
	if(isShow==0){
		var box = WST.confirm({content:"您确定要隐藏该商品分类并下架该分类下的所有商品吗?",yes:function(){
			  layer.close(box);
              var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			  $.post(WST.U('admin/goodscats/editiIsShow'),{id:id,isShow:isShow},function(data,textStatus){
					layer.close(loading);
					var json = WST.toAdminJson(data);
					if(json.status=='1'){
						 WST.msg(json.msg,{icon:1});
						 grid.reload(id);
					}else{
						 WST.msg(json.msg,{icon:2});
					}
			  });
		}});	
	}else{
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	    $.post(WST.U('admin/goodscats/editiIsShow'),{id:id,isShow:isShow},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				 WST.msg(json.msg,{icon:1});
				 grid.reload(id);
			}else{
				 WST.msg(json.msg,{icon:2});
			}
		});
	}
}
var editBox;
function toEdit(pid,id){
	var w = WST.pageWidth();
	var h = WST.pageHeight();
    editBox = WST.open({type:2,title:false,content:WST.U('admin/goodscats/toEdit','id='+id+'&pid='+pid),closeBtn:0,area: [w+'px', h+'px'],offset:['0px','0px']})
}
function closeEditBox(){
    layer.close(editBox);
}
function loadGrid(id){
	grid.reload(id);
}
function toEdits(){
    var id = $('#catId').val();
    var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/goodscats/'+((id>0)?"edit":"add")),params,function(data,textStatus){
        layer.close(loading);
        parent.loadGrid((params.parentId>0)?params.parentId:id);
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            WST.msg(json.msg,{icon:1},function(){
                parent.closeEditBox();
            });
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}
var isInitUpload = false;

function toDel(pid,id){
	var box = WST.confirm({content:"您确定要删除该商品分类并下架该分类下的所有商品吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/goodscats/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			       WST.msg(json.msg,{icon:1});
	           			       layer.close(box);
	           		           grid.reload(pid);
	           			  }else{
	           			       WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

function initUpload(){
	isInitUpload = true;
	//文件上传
	WST.upload({
	    pick:'#catFilePicker',
	    formData: {dir:'goodscats'},
	    accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	    callback:function(f){
	      var json = WST.toAdminJson(f);
	      if(json.status==1){
	        $('#uploadMsg').empty().hide();
	        //将上传的图片路径赋给全局变量
		    $('#catImg').val(json.savePath+json.thumb);
		    $('#preview').html('<img src="'+WST.conf.RESOURCE_PATH+'/'+json.savePath+json.thumb+'" height="30" />');
	      }else{
	      	WST.msg(json.msg,{icon:2});
	      }
	  },
	  progress:function(rate){
	      $('#uploadMsg').show().html('已上传'+rate+"%");
	  }
	});

}

function editName(id,obj){
	if($.trim(obj.value)=='' || $.trim(obj.value)==oldData[id]){
		obj.value = oldData[id];
		return;
	}
	$.post(WST.U('admin/goodscats/editName'),{id:id,catName:obj.value},function(data,textStatus){
	    var json = WST.toAdminJson(data);
	    if(json.status=='1'){
	    	oldData[id] = $.trim(obj.value);
	        WST.msg(json.msg,{icon:1});
	    }else{
	        WST.msg(json.msg,{icon:2});
	    }
	});
}
function editsimpleName(id,obj){
	if($.trim(obj.value)=='' || $.trim(obj.value)==oldData[id]){
		obj.value = oldData[id];
		return;
	}
	if(obj.value.length>4){
		return WST.msg('商品分类名缩写不能超过4个字',{icon:2});
	}
	$.post(WST.U('admin/goodscats/editsimpleName'),{id:id,simpleName:obj.value},function(data,textStatus){
	    var json = WST.toAdminJson(data);
	    if(json.status=='1'){
	    	oldData[id] = $.trim(obj.value);
	        WST.msg(json.msg,{icon:1});
	    }else{
	        WST.msg(json.msg,{icon:2});
	    }
	});
}
function editOrder(id,obj){
	if($.trim(obj.value)=='' || $.trim(obj.value)==editOrder[id]){
		obj.value = editOrder[id];
		return;
	}
	$.post(WST.U('admin/goodscats/editOrder'),{id:id,catSort:obj.value},function(data,textStatus){
	    var json = WST.toAdminJson(data);
	    if(json.status=='1'){
	    	editOrder[id] = $.trim(obj.value);
	        WST.msg(json.msg,{icon:1});
	    }else{
	        WST.msg(json.msg,{icon:2});
	    }
	});
}