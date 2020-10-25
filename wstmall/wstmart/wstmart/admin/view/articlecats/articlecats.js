var grid,oldData = {};
function initGrid(){
	grid = $('#maingrid').WSTGridTree({
		url:WST.U('admin/articlecats/pageQuery'),
		rownumbers:true,
        columns: [
	        { display: '分类名称', name: 'catName', id:'catId', align: 'left',isSort: false,render: function (item)
                {
                	oldData[item.catId] = item.catName;
                    return '<input type="text" size="40" value="'+item.catName+'" onblur="javascript:editName('+item.catId+',this)"/>';
            }},
            { display: '分类类型', width: 100, name: 'catType',isSort: false,
                render: function (item)
                {
                    if (parseInt(item.catType) == 1) return '<span>系统菜单</span>';
                    return '<span>普通类型</span>';
                }
            },
            { display: '是否显示', width: 80, name: 'isShow',isSort: false,
                render: function (item)
                {
                    return '<input type="checkbox" '+((item.isShow==1)?"checked":"")+' class="ipt" lay-skin="switch" lay-filter="isShow" data="'+item.catId+'" lay-text="显示|隐藏">';
                }
            },
	        { display: '排序号', name: 'catSort',width: 60,isSort: false},
	        { display: '操作', name: 'op',width: 250,isSort: false,
	        	render: function (rowdata,e){
		            var h = "";
			        if(WST.GRANT.WZFL_01)h += "<a class='btn btn-blue' href='javascript:toEdit("+rowdata["catId"]+",0)'><i class='fa fa-plus'></i>新增子分类</a> ";
		            if(WST.GRANT.WZFL_02)h += "<a class='btn btn-blue' href='javascript:toEdit("+rowdata["parentId"]+","+rowdata["catId"]+")'><i class='fa fa-pencil'></i>修改</a> ";
		            if(WST.GRANT.WZFL_03 && rowdata["catType"]==0)h += "<a class='btn btn-red' href='javascript:toDel("+rowdata["parentId"]+","+rowdata["catId"]+","+rowdata["catType"]+")'><i class='fa fa-trash-o'></i>删除</a> "; 
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
   $('#headTip').WSTTips({width:90,height:35,callback:function(v){}});
   $('body').css('overflow-y','auto');
}
function toggleIsShow(id,isShow){
	if(!WST.GRANT.WZFL_02)return;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/articlecats/editiIsShow'),{id:id,isShow:isShow},function(data,textStatus){
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

function toEdit(pid,id){
	$('#articlecatForm')[0].reset();
	if(id>0){
		$.post(WST.U('admin/articlecats/get'),{id:id},function(data,textStatus){
			var json = WST.toAdminJson(data);
			if(json){
				WST.setValues(json);
				layui.form.render();
				editsBox(id);
			}
		});
	}else{
		WST.setValues({parentId:pid,catName:'',isShow:1,catSort:0});
		layui.form.render();
		editsBox(id);
	}
}

function editsBox(id){
	var title =(id>0)?"修改文章分类":"新增文章分类";
	var box = WST.open({title:title,type:1,content:$('#articlecatBox'),area: ['465px', '300px'],btn:['确定','取消'],
		end:function(){$('#articlecatBox').hide();},yes:function(){
		          $('#articlecatForm').submit();
	          }});
	$('#articlecatForm').validator({
	    fields: {
	    	catName: {
	    		tip: "请输入分类名称",
	    		rule: '分类名称:required;length[~10];'
	    	},
	    	catSort: {
            	tip: "请输入排序号",
            	rule: '排序号:required;length[~8];'
            }
	    },
	    valid: function(form){
	        var params = WST.getParams('.ipt');
	        params.id = id;
	        var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    		$.post(WST.U('admin/articlecats/'+((id>0)?"edit":"add")),params,function(data,textStatus){
    			  layer.close(loading);
    			  var json = WST.toAdminJson(data);
    			  if(json.status=='1'){
    			    	WST.msg(json.msg,{icon:1});
    			    	$('#articlecatBox').hide();
    			    	layer.close(box);
    		            grid.reload(params.parentId);
    			  }else{
    			        WST.msg(json.msg,{icon:2});
    			  }
    		});
	    }
	});
}

function toDel(pid,id,type){
	var box = WST.confirm({content:"您确定要删除该分类以及其下的文章吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('admin/articlecats/del'),{id:id,type:type},function(data,textStatus){
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
function editName(id,obj){
	if($.trim(obj.value)=='' || $.trim(obj.value)==oldData[id]){
		obj.value = oldData[id];
		return;
	}
	$.post(WST.U('admin/articlecats/editName'),{id:id,catName:obj.value},function(data,textStatus){
	    var json = WST.toAdminJson(data);
	    if(json.status=='1'){
	    	oldData[id] = $.trim(obj.value);
	        WST.msg(json.msg,{icon:1});
	    }else{
	        WST.msg(json.msg,{icon:2});
	    }
	});
}