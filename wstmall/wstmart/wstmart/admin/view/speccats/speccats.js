var mmg;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'规格类型', name:'catName', width: 10},
            {title:'所属商品分类', name:'goodsCatNames', width: 300,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['goodsCatNames']+"</p></span>";
            }},
            {title:'是否允许上传图片', name:'isAllowImg', width: 10,renderer: function(val,item,rowIndex){
            	return (val==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 允许</span>":'';
            }},
            {title:'是否显示', name:'attrVal', width: 10,renderer: function(val,item,rowIndex){
            	return '<input type="checkbox" '+((item['isShow']==1)?"checked":"")+' id="isShow1" name="isShow1" value="1" class="ipt" lay-skin="switch" lay-filter="isShow1" data="'+item['catId']+'" lay-text="显示|隐藏">'
            }},
            {title:'操作', name:'op' ,width:50, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
		        if(item.specId>0){
		        	if(WST.GRANT.SPGG_02)h += "<a class='btn btn-blue' href='javascript:toEdit("+ item['catId']+"," + item['id'] + ")'><i class='fa fa-pencil'></i>修改</a> ";
		        	if(WST.GRANT.SPGG_03)h += "<a class='btn btn-red' href='javascript:toDel(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> "; 
		            return h;
		        }else{
		        	if(WST.GRANT.SPGG_02)h += "<a class='btn btn-blue' href='javascript:toEditCat(" + item['id'] + ")' ><i class='fa fa-pencil'></i>修改</a> ";
		        	if(WST.GRANT.SPGG_03)h += "<a class='btn btn-red' href='javascript:toDelCat(" + item['id'] + ")'><i class='fa fa-trash-o'></i>删除</a> "; 
		        }
		        return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true, cols: cols,method:'POST',
        url: WST.U('admin/speccats/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    mmg.on('loadSuccess',function(data){
    	layui.form.render();
    	layui.form.on('switch(isShow1)', function(data){
           var id = $(this).attr("data");
		   if(this.checked){
			   toggleIsShow(id, 1);
		   }else{
			   toggleIsShow(id, 0);
		   }
		});
    })
	loadGrid(p);
}
//------------------规格类型---------------//
function toEditCat(catId){
	$("select[id^='bcat_0_']").remove();
	$('#specCatsForm').get(0).reset();
	$.post(WST.U('admin/speccats/get'),{catId:catId},function(data,textStatus){
        var json = WST.toAdminJson(data);
        WST.setValues(json);
        layui.form.render();
        if(json.goodsCatId>0){
        	var goodsCatPath = json.goodsCatPath.split("_");
        	$('#bcat_0').val(goodsCatPath[0]);
        	var opts = {id:'bcat_0',val:goodsCatPath[0],childIds:goodsCatPath,className:'goodsCats'}
        	WST.ITSetGoodsCats(opts);
        }
		var title =(catId==0)?"新增":"编辑";
		var box = WST.open({title:title,type:1,content:$('#specCatsBox'),area: ['750px', '360px'],btn:['确定','取消'],
			end:function(){$('#specCatsBox').hide();},yes:function(){
			$('#specCatsForm').submit();
		}});
		$('#specCatsForm').validator({
			fields: {
			 	'catName': {rule:"required remote;",msg:{required:'请输入规格名称'}},
			},
			valid: function(form){
			    var params = WST.getParams('.ipt');
			    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
			    params.goodsCatId = WST.ITGetGoodsCatVal('goodsCats');
			 	$.post(WST.U('admin/speccats/'+((params.catId==0)?"add":"edit")),params,function(data,textStatus){
			 		layer.close(loading);
			    	var json = WST.toAdminJson(data);
					if(json.status=='1'){
						WST.msg("操作成功",{icon:1});
						layer.close(box);
						$('#specCatsBox').hide();
						loadGrid(WST_CURR_PAGE);
						layer.close(box);
				  	}else{
				    	WST.msg(json.msg,{icon:2});
					}
			 	});
			}
		});

	});
}

function loadGrid(p){
	p=(p<=1)?1:p;
	var keyName = $("#keyName").val();
	var goodsCatPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats');
	mmg.load({"page":p,"keyName":keyName,"goodsCatPath":goodsCatPath.join('_')});
}

function toDelCat(catId){
	var box = WST.confirm({content:"您确定要删除该类型吗?",yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/speccats/del'),{catId:catId},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				WST.msg("操作成功",{icon:1});
				layer.close(box);
				loadGrid(WST_CURR_PAGE);
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}

function toggleIsShow( catId, isShow){
	$.post(WST.U('admin/speccats/setToggle'), {'catId':catId, 'isShow':isShow}, function(data, textStatus){
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg("操作成功",{icon:1});
			loadGrid(WST_CURR_PAGE);
		}else{
			WST.msg(json.msg,{icon:2});
		}
	})
}

//------------------规格---------------//
function toDel(specId){
	var box = WST.confirm({content:"您确定要删除该规格吗?",yes:function(){
		var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/specs/del'),{specId:specId},function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				WST.msg("操作成功",{icon:1});
				layer.close(box);
				loadGrid(WST_CURR_PAGE);
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}


function toEdit(catId,specId){
	    $.post(WST.U('admin/specs/get'),{specId:specId},function(data,textStatus){
	    	var json = WST.toAdminJson(data);
	    	$('#specForm').get(0).reset();
	      	WST.setValues(json);

			var title =(specId==0)?"新增":"编辑";
			var box = WST.open({title:title,type:1,content:$('#specBox'),area: ['450px', '160px'],btn:['确定','取消'],yes:function(){
				$('#specForm').submit();
			}});
			$('#specForm').validator({
				rules: {
			        remote: function(el){
			        	return $.post(WST.U('admin/specs/checkSpecName'),{"specName":el.value,"catId":catId},function(data,textStatus){});
			        }
			    },
		        fields: {
		        	'specName': {rule:"required; remote;",msg:{required:'请输入规格名称'}},
		        },
		        valid: function(form){
		    	   var params = WST.getParams('.ipt');
		    	   params.catId = catId;
		    	   var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    	   $.post(WST.U('admin/specs/'+((specId==0)?"add":"edit")),params,function(data,textStatus){
		    		   layer.close(loading);
		    		   var json = WST.toAdminJson(data);
		    		   if(json.status=='1'){
		    	          WST.msg("操作成功",{icon:1});
		    	          layer.close(box);
		    	          loadGrid(WST_CURR_PAGE);
		    	          $('#specForm')[0].reset();
		    		   }else{
		    			   WST.msg(json.msg,{icon:2});
		    	      }
		    	    });
		
		    	}
		
			});
	});
}
