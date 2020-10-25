
function toStock(id,src){
    location.href=WST.U('shop/goodsvirtuals/stock','id='+id+"&src="+src);
}
function stockByPage(p){
    var h = WST.pageHeight();
    var cols = [
        {title:'商品图片', name:'goodsName', width: 40, renderer: function(val,item,rowIndex){
                return "<span class='weixin'><img class='img' style='height:50px;width:50px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'><img class='imged' style='height:200px;width:200px;' src='"+WST.conf.RESOURCE_PATH+"/"+item['goodsImg']+"'></span>";
            }},
        {title:'商品名称', name:'goodsName', width: 200},

        {title:'货号', name:'productNo', width: 100},
        {title:'规格', name:'', width: 150,renderer:function(val,item,rowIndex){
                if(item['isSpec']==1){
                	var spec="";
                    for(var s = 0; s < item.spec.length; s++){
                        spec +=item.spec[s]['catName']+"："+item.spec[s]['itemName'];
                        }
                    return spec;
                }else{
                    return "<span class='statu-wait'>无</span>";
                }
            }},
        {title:'库存', name:'goodsStock', width: 30,renderer:function(val,item,rowIndex){
                var goodsStock="";
                if(item['isSpec']==1){
                    goodsStock+="<span ondblclick='javascript:toEditGoodsStock(" + item['id'] + ",1)'>" +
                        "<input style='width: 60%;display: none;' id='ipt_1_"+item['id']+"' onkeyup='javascript:WST.isChinese(this,1)' onkeypress='return WST.isNumberKey(event)' onblur='javascript:editGoodsStock("+item['id']+",1,"+item['goodsId']+")' class='stockin' maxlength='6'/>\n" +
                        "        <span id='span_1_"+item['id']+"' style='display: inline;cursor:pointer;color:#f30505;'>"+item['goodsStock']+"</span>" +
                        "</span>";
                }else{
                    if(item['goodsType']==0){
                        goodsStock+="<span ondblclick='javascript:toEditGoodsStock(" + item['goodsId'] + ",3)'>" +
                            "<input style='width: 60%;display: none;' id='ipt_3_"+item['goodsId']+"' onkeyup='javascript:WST.isChinese(this,1)' onkeypress='return WST.isNumberKey(event)' onblur='javascript:editGoodsStock("+item['goodsId']+",3)' class='stockin' maxlength='6'/>\n" +
                            "        <span id='span_3_"+item['goodsId']+"' style='display: inline;cursor:pointer;color:#f30505;'>"+item['goodsStock']+"</span>" +
                            "</span>";
                    }else{
                        goodsStock=item['goodsStock'];
                    }
                }

                return goodsStock;
            }},
        {title:'预警', name:'warnStock', width: 30,renderer:function(val,item,rowIndex){
                var goodsStock="";
                if(item['isSpec']==1){
                    goodsStock+="<span ondblclick='javascript:toEditGoodsStock(" + item['id'] + ",2)'>" +
                        "<input style='width: 60%;display: none;' id='ipt_2_"+item['id']+"' onkeyup='javascript:WST.isChinese(this,1)' onkeypress='return WST.isNumberKey(event)' onblur='javascript:editGoodsStock("+item['id']+",2,"+item['goodsId']+")' class='stockin' maxlength='6'/>\n" +
                        "        <span id='span_2_"+item['id']+"' style='display: inline;cursor:pointer;color:#f30505;'>"+item['warnStock']+"</span>" +
                        "</span>";
                }else{
                    if(item['goodsType']==0){
                        goodsStock+="<span ondblclick='javascript:toEditGoodsStock(" + item['goodsId'] + ",4)'>" +
                            "<input style='width: 60%;display: none;' id='ipt_4_"+item['goodsId']+"' onkeyup='javascript:WST.isChinese(this,1)' onkeypress='return WST.isNumberKey(event)' onblur='javascript:editGoodsStock("+item['goodsId']+",4)' class='stockin' maxlength='6'/>\n" +
                            "        <span id='span_4_"+item['goodsId']+"' style='display: inline;cursor:pointer;color:#f30505;'>"+item['warnStock']+"</span>" +
                            "</span>";
                    }else{
                        goodsStock=item['warnStock'];
                    }
                }

                return goodsStock;
            }},
        {title:'操作', name:'' ,width:200, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
                if(item['goodsType']==1)h += "<a class='btn btn-blue' href='javascript:toStock(" + item['goodsId'] + ",\"stockWarnByPage\")'><i class='fa fa-pencil'></i>卡券编辑</a>";
                h += " <a class='btn btn-blue' href='javascript:toEdit(" + item['goodsId'] + ",\"stockwarnbypage\")'><i class='fa fa-pencil'></i>商品编辑</a> ";
                return h;
            }}
    ];

    mmg = $('.mmg').mmGrid({height: h-100,indexCol: true, cols: cols,method:'POST',
        url: WST.U('shop/goods/stockByPage'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function loadGrid(p){
    p = (p<=1)?1:p;
    mmg.load({cat1:$('#cat1').val(),cat2:$('#cat2').val(),page:p});
}
function toEdit(id,src){
	location.href = WST.U('shop/goods/edit','id='+id+'&src='+src+'&p='+WST_CURR_PAGE);
}
//双击修改
function toEditGoodsStock(id,type){
	$("#ipt_"+type+"_"+id).show();
	$("#span_"+type+"_"+id).hide();
	$("#ipt_"+type+"_"+id).focus();
	$("#ipt_"+type+"_"+id).val($("#span_"+type+"_"+id).html());
}
function endEditGoodsStock(type,id){
	$('#span_'+type+'_'+id).html($('#ipt_'+type+'_'+id).val());
	$('#span_'+type+'_'+id).show();
    $('#ipt_'+type+'_'+id).hide();
}
function editGoodsStock(id,type,goodsId){
	var number = $('#ipt_'+type+'_'+id).val();
	if($.trim(number)==''){
		WST.msg('库存不能为空', {icon: 5});
        return;
	}
	var params = {};
	params.id = id;
	params.type = type;
	params.goodsId = goodsId;
	params.number = number;
	$.post(WST.U('shop/Goods/editwarnStock'),params,function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status>0){
			$('#img_'+type+'_'+id).fadeTo("fast",100);
			endEditGoodsStock(type,id);
			$('#img_'+type+'_'+id).fadeTo("slow",0);
		}else{
			WST.msg(json.msg, {icon: 5}); 
		}
	});
}

function getCat(val){
  if(val==''){
  	$('#cat2').html("<option value='' >-请选择-</option>");
  	return;
  }
  $.post(WST.U('shop/shopcats/listQuery'),{parentId:val},function(data,textStatus){
       var json = WST.toJson(data);
       var html = [],cat;
       html.push("<option value='' >-请选择-</option>");
       if(json.status==1 && json.list){
         json = json.list;
       for(var i=0;i<json.length;i++){
           cat = json[i];
           html.push("<option value='"+cat.catId+"'>"+cat.catName+"</option>");
        }
       }
       $('#cat2').html(html.join(''));
  });
}