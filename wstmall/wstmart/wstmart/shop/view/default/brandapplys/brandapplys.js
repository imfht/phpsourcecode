var mmg;
var mmg2;
var mmg3;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'品牌图标', name:'img', width: 30, renderer: function(val,item,rowIndex){
                return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['brandImg']
            	+"'><span class='imged' style='left:45px;' ><img  style='height:200px; width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['brandImg']+"'></span></span>";
            }},
            {title:'品牌名称', name:'brandName', width: 60},
            {title:'品牌介绍', name:'brandDesc', width: 350,renderer: function(val,item,rowIndex){
                return "<span  ><p class='wst-nowrap'>"+item['brandDesc']+"</p></span>";
            }},
			{title:'审核状态', name:'brandDesc', width: 100,renderer: function(val,item,rowIndex){
				if(item['applyStatus']==0){
					return "<span class='statu-wait'><i class='fa fa-clock-o'></i> "+item['applyStatusName']+"</span>";
				}else if(item['applyStatus']==1){
					return "<span class='statu-yes'><i class='fa fa-check-circle'></i> "+item['applyStatusName']+"</span>";
				}else{
					return "<span class='statu-no'><i class='fa fa-ban'></i> "+item['applyStatusName']+"</span>";
				}
			}},
            {title:'操作', name:'' ,width:70, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
				if(item['applyStatus'] == 0){
					h += "<a class='btn btn-blue' href='javascript:toEdit("+item["applyId"]+")'><i class='fa fa-pencil'></i>修改</a> ";
				}else{
					h += "<a class='btn btn-blue' href='javascript:toEdit("+item["applyId"]+")'><i class='fa fa-search'></i>查看</a> ";
				}
				h += "<a class='btn btn-red' href='javascript:toDel(" + item["applyId"] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
				h += "<a class='btn btn-blue' href='javascript:toView("+item["brandId"]+")'><i class='fa fa-search'></i>查看商家</a> ";
		        return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: h-89,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
        url: WST.U('shop/brandapplys/pageQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}

function loadGrid(p){
    p=(p<=1)?1:p;
	mmg.load({page:p,isNew:1,key:$('#key').val()});
}

function initGrid2(p){
	var h = WST.pageHeight();
	var cols = [
		{title:'品牌图标', name:'img', width: 30, renderer: function(val,item,rowIndex){
				return "<span class='weixin'><img id='img' onmouseout='toolTip()' onmouseover='toolTip()' style='height:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['brandImg']
					+"'><span class='imged' style='left:45px;' ><img  style='height:200px; width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['brandImg']+"'></span></span>";
			}},
		{title:'品牌名称', name:'brandName', width: 60},
		{title:'品牌介绍', name:'brandDesc', width: 350,renderer: function(val,item,rowIndex){
				return "<span  ><p class='wst-nowrap'>"+item['brandDesc']+"</p></span>";
			}},
		{title:'审核状态', name:'brandDesc', width: 100,renderer: function(val,item,rowIndex){
				if(item['applyStatus']==0){
					return "<span class='statu-wait'><i class='fa fa-clock-o'></i> "+item['applyStatusName']+"</span>";
				}else if(item['applyStatus']==1){
					return "<span class='statu-yes'><i class='fa fa-check-circle'></i> "+item['applyStatusName']+"</span>";
				}else{
					return "<span class='statu-no'><i class='fa fa-ban'></i> "+item['applyStatusName']+"</span>";
				}
			}},
		{title:'操作', name:'' ,width:70, align:'center', renderer: function(val,item,rowIndex){
				var h = "";
				if(item['applyStatus'] == 0){
					h += "<a class='btn btn-blue' href='javascript:toEdit("+item["applyId"]+")'><i class='fa fa-pencil'></i>修改</a> ";
				}else{
					h += "<a class='btn btn-blue' href='javascript:toEdit("+item["applyId"]+")'><i class='fa fa-search'></i>查看</a> ";
				}
				h += "<a class='btn btn-red' href='javascript:toDel(" + item["applyId"] + ")'><i class='fa fa-trash-o'></i>删除</a> ";
				return h;
			}}
	];

	mmg2 = $('.mmg2').mmGrid({height: h-89,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
		url: WST.U('shop/brandapplys/pageQuery'), fullWidthRows: true, autoLoad: false,
		plugins: [
			$('#pg2').mmPaginator({})
		]
	});
	loadGrid2(p);
}

function loadGrid2(p){
	p=(p<=1)?1:p;
	mmg2.load({page:p,isNew:0,key:$('#key2').val()});
}

function initGrid3(p){
	var h = WST.pageHeight();
	var cols = [
		{title:'店铺名称', name:'shopName', width: 100},
		{title:'申请时间', name:'createTime', width: 100}
	];

	mmg3 = $('.mmg3').mmGrid({height: h-140,indexCol: true,indexColWidth:50, cols: cols,method:'POST',
		url: WST.U('shop/brandapplys/shopPageQuery'), fullWidthRows: true, autoLoad: false,
		plugins: [
			$('#pg3').mmPaginator({})
		]
	});
	loadGrid3(p);
}

function loadGrid3(p){
	p=(p<=1)?1:p;
	var brandId = $("#brandId").val();
	mmg3.load({page:p,brandId:brandId,key:$('#key3').val()});
}

function toEdit(id){
	location.href=WST.U('shop/brandapplys/toEdit','id='+id+'&p='+WST_CURR_PAGE);
}

function toView(id){
	location.href=WST.U('shop/brandapplys/toView','id='+id+'&p='+WST_CURR_PAGE);
}

function toEdits(id,p){
    var params = WST.getParams('.ipt');
    params.id = id;
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('shop/brandapplys/'+((id>0)?"edit":"add")),params,function(data,textStatus){
		  layer.close(loading);
		  var json = WST.toJson(data);
		  if(json.status=='1'){
		    	WST.msg(json.msg,{icon:1});
			    location.href=WST.U('shop/brandapplys/index',"p="+p);
		  }else{
		        WST.msg(json.msg,{icon:2});
		  }
	});
}

function toDel(id){
	var box = WST.confirm({content:"您确定要删除该品牌吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           	$.post(WST.U('shop/brandapplys/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg(json.msg,{icon:1});
	           			    	layer.close(box);
	           		            loadGrid(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}

function getBrandByKey(){
	if($.trim($('#keyName').val())=='')return;
	$('#brandNameBox').html('');
	cleanBrandInfo();
	var loading = WST.msg('正在查询品牌信息...', {icon: 16,time:60000});
	$.post(WST.U('shop/brandapplys/getBrandByKey'),{key:$('#keyName').val()},function(data,textStatus){
		layer.close(loading);
		var json = WST.toJson(data);
		if(json.status=='1'){
			var brands = json.data.data;
			for(var i=0;i<brands.length;i++){
				var brand = brands[i];
				var brandHtml = "";
				brandHtml += "<div class='wst-flex-row wst-ac brand-item'>";
				brandHtml += "<input type='radio' name='select-brand' class='select-brand' onclick='selectBrand(this)' value='"+brand.brandId+"'>";
				brandHtml += "<img src='"+WST.conf.RESOURCE_PATH+'/'+brand.brandImg+"' class='ipt select-brand-img' height='30'>";
				brandHtml += "<p>"+brand.brandName+"</p>";
				brandHtml += "</div>";
				$('#brandNameBox').append(brandHtml);
			}
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}

function selectBrand(obj){
	var loading = WST.msg('正在加载品牌信息...', {icon: 16,time:60000});
	var brandId = $(obj).val();
	$.post(WST.U('shop/brandapplys/getBrandInfo'),{brandId:brandId},function(data,textStatus){
		layer.close(loading);
		var json = WST.toJson(data);
		if(json.status=='1'){
			var brand = json.data.data;
			$('#isNew').val(0);
			$('#brandId').val(brand.brandId);
			$('#brandName').val(brand.brandName).attr('readonly','readonly');
			$('#brandImg').val(brand.brandImg);
			$('#preview').html('');
			$('#preview').append("<img src='"+WST.conf.RESOURCE_PATH+'/'+brand.brandImg+"' class='ipt' height='30'>");
			$("#filePicker").hide();
			$('#accreditImg').val('');
			$('#accreditPreview').html('');
			$("#brandDesc").val(brand.brandDesc);
			editor1.html(brand.brandDesc);
			editor1.readonly(true);
			for(var i=0;i<brand.catIds;i++){
				$(".goods-cat").each(function(idx, item) {
					$(item).attr("disabled","disabled");
					if($(item).val()==brand.catIds[i]){
						$(item).prop("checked",true);
					}
				});
			}
		}else{
			WST.msg(json.msg,{icon:2});
			cleanBrandInfo();
		}
	});
}

function cleanBrandInfo(){
	$('#isNew').val(1);
	$('#brandName').val('').attr('readonly',false);
    $('#brandId').val('');
	$('#brandImg').val('');
	$('#preview').html('');
    $("#brandDesc").val('');
	editor1.html('');
	editor1.readonly(false);
	$(".goods-cat").each(function(idx, item) {
		$(item).attr("disabled",false);
		$(item).prop("checked",false);
	});
	$("#filePicker").show();
}

function initBrandInfo(){
    var isNew = $('#isNew').val();
    if(isNew==0){
        $('#brandName').attr('readonly',true);
        editor1.readonly(true);
        $(".goods-cat").each(function(idx, item) {
            $(item).attr("disabled",true);
        });
        $("#filePicker").hide();
    }
}

function toolTip(){
	$('body').mousemove(function(e){
		var windowH = $(window).height();
		if(e.pageY >= windowH*0.8){
			var top = windowH*0.233;
			$('.imged').css('margin-top',-top);
		}else{
			var top = windowH*0.06;
			$('.imged').css('margin-top',-top);
		}
	});
}

function delVO(obj){
	$(obj).parent().remove();
	var imgPath = [];
	$('.step_pic').each(function(){
		imgPath.push($(this).attr('v'));
	});
	$('#accreditImg').val(imgPath.join(','));
}