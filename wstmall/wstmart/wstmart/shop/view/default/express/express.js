var mmg;
var laytpl, form, isChkAll = false;
$(function(){
	form = layui.form;
	form.on('checkbox(chooseProvince)', function (data) {
        var areaId = $(this).data("areaid");      
        $(".parent_"+areaId).each(function (index, item) {
            if(!$(this).prop("disabled")){
    			item.checked = data.elem.checked;
    		}
        	
        });
        form.render('checkbox');
    });
    form.on("checkbox(chooseCity)", function (data) {
        var parentId = $(this).data("parentid");
        var chkCnt = $('.parent_'+parentId+"[type='checkbox']:checked").length;
        if(chkCnt>0){
        	$(".province_"+parentId).each(function(){
        		if(!$(this).prop("disabled")){
        			$(this).prop("checked",true)
        		}
        	});
        }else{
        	$(".province_"+parentId).each(function(){
        		if(!$(this).prop("disabled")){
        			$(this).prop("checked",false)
        		}
        	});
        }
        form.render('checkbox');
    });
    form.on('checkbox(chooseAll)', function (data) {
    	isChkAll = data.elem.checked;
    	$(".province").each(function(){
    		if(!$(this).prop("disabled")){
    			$(this).prop("checked",isChkAll);
    		}
    	});
    	$(".city").each(function(){
    		if(!$(this).prop("disabled")){
    			$(this).prop("checked",isChkAll);
    		}
    	});
        form.render('checkbox');
    });
});
function initGrid1(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'快递名称', name:'expressName' ,width:300},
        {title:'快递代码', name:'expressCode' ,width:300},
        {title:'启用状态', name:'isEnable' ,width:200,renderer: function(val,item,rowIndex){
            if(item['isEnable'] == 1){
                return "<span class='statu-yes'><i class='fa fa-check-circle'></i> 已启用</span>";
            }else{
                return "<span class='wst-red'>未启用</span>";
            }
        }},
      
        {title:'操作', name:'' ,width:300, align:'center', renderer: function(val,item,rowIndex){
            var h = "";
            if(item['isEnable'] == 1){
                h += "<a class='btn btn-blue' href='javascript:toSet(" + item['id'] + ")'><i class='fa fa fa-cog'></i>设置运费模板</a> ";
            	h += "<a class='btn btn-red' href='javascript:disableExpress(" + item['id'] + ")'><i class='fa fa-circle-o-notch'></i>停用</a> ";
            }else{
                h += "<a class='btn btn-blue' href='javascript:enableExpress(" + item['expressId'] + ")'><i class='fa fa-check-circle'></i>启用</a>";
            }
            return h;
        }}
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/express/pageQuery'), fullWidthRows: true, autoLoad: false,remoteSort: true,
        plugins: [
            $('#pg').mmPaginator()
        ]
    });
    mmg.on('loadSuccess',function(){
   		layui.form.render();
      	layui.form.on('switch(isDefault)', function(data){
          	var id = $(this).attr("data");
          	if(this.checked){
          		toggleSet(id,1);
          	}else{
      			toggleSet(id,0);
          	}
      	});
    });
    loadGrid1(p);
}

function loadGrid1(p){
    p = (p<=1)?1:p;
    var expressName = $("#expressName").val();
    mmg.load({expressName:expressName,page:p});
}

//默认设置
function toggleSet(expressId,isDefault){
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('shop/express/toggleSet'),{expressId:expressId,isDefault:isDefault},function(data,textStatus){
        layer.close(loading);
        var json = WST.toJson(data);
        if(json.status==1){
            WST.msg(json.msg,{icon:1});
            loadGrid1();
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}

function toEdit(id){
	var shopExpressId = $("#shopExpressId").val();
	location.href = WST.U('shop/express/edit',{shopExpressId:shopExpressId,id:id});
}

function enableExpress(expressId){
	var load = WST.load({msg:'处理中，请稍后...'});
	$.post(WST.U('shop/express/enableExpress'),{expressId:expressId},function(data,textStatus){
		layer.close(load);
	    loadGrid1();
	});
}

function disableExpress(id){
	var c = WST.confirm({content:'您确定要停用该快递吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'处理中，请稍后...'});
		$.post(WST.U('shop/express/disableExpress'),{id:id},function(data,textStatus){
			layer.close(load);
		    loadGrid1();
		});
	}});

}


function loadGrid2(id,p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.s-query');
	params.shopExpressId = id;
	params.page = p;
	var load = WST.load({msg:'处理中，请稍后...'});
	$.post(WST.U('shop/express/listQuery2'),params,function(data,textStatus){
		layer.close(load);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	$('#list').empty();
	    	json = json.data;
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#list').html(html);
	       	});
	       	if(json.last_page>1){
		       	laypage({
			        cont: 'pager', 
			        pages:json.last_page, 
			        curr: json.current_page,
			        skin: '#1890ff',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		loadGrid2(id,e.curr);
			        	}
			        } 
			    });
	        }else{
	            $('#pager').empty();
	     	}
	    }
	});
}

function freightAreas(){
	var box = WST.open({
		title:'选择地区城市',
		type:1,
		resize:false,
		content:layui.$('#freightAreas'),
		area: ['700px', '500px'],
		btn:['确定','取消'],
		yes:function(){
			var provinceNames = []; 
			var provinceIds = []; 
			var cityIds = [];
			$(".province[type='checkbox']:checked").each(function(){
				provinceNames.push($(this).attr("title"));
				provinceIds.push($(this).data("areaid"));
			});
			$(".city[type='checkbox']:checked").each(function(){
				cityIds.push($(this).data("areaid"));
			});
			$("#pnames").html(provinceNames.join(","));
			$("#provinceIds").val(provinceIds.join(","));
			$("#cityIds").val(cityIds.join(","));
			layer.close(box);
		},
		cancel:function(){
			
		},
		end:function(){
			$("#freightAreas").append("<div id='chkAllBox' style='display: none;'><div class='tpox'><input type='checkbox' name='chkAll' lay-skin='primary' title='全选/全不选' "+(isChkAll?"checked":"")+" lay-filter='chooseAll' style='display: none;' /></div></div>")
			layui.form.render();
		},
		success:function(layero){
			layero.append(layui.$("#chkAllBox").show());
		}
	});
}


function toSet(id){
	location.href = WST.U('shop/express/index2',{shopExpressId:id});
}


function save(){
	$('#editForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			var mothed = params.id>0?"toEdit":"toAdd";
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('shop/express/'+mothed),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1},function(){
		    			var shopExpressId = $("#shopExpressId").val();
						location.href=WST.U('shop/express/index2',{shopExpressId:shopExpressId});
					});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}


function del(id){
	WST.confirm({content:"您确定删除运费模板吗？", yes:function(tips){
	    var ll = WST.load('数据处理中，请稍候...');
	    $.post(WST.U('shop/express/del'),{id:id},function(data){
			var json = WST.toJson(data);
			if(json.status>0){
				WST.msg(json.msg,{icon:1});
				var id = $("#shopExpressId").val();
				loadGrid2(id);
				layer.close(tips);
			    layer.close(ll);
			}else{
				WST.msg(json.msg,{icon:2});
			}
		});
	}});
}

