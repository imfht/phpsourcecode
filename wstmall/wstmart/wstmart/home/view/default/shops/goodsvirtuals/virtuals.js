function stockByPage(p){
	$('#list').html('<tr><td colspan="11"><img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...</td></tr>');
	var params = {};
	params.isUse = $.trim($('#isUse').val());
	params.cardNo = $.trim($('#cardNo').val());
	params.id = $.trim($('#vid').val());
	params.page = p;
	$.post(WST.U('home/goodsvirtuals/stockByPage'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    if(json.status==1 && json.data){
	    	if(params.page>json.last_page && json.last_page >0){
               stockByPage(json.last_page);
               return;
            }
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#list').html(html);
	       		$('.j-lazyGoodsImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});//商品默认图片
	       	});
	       	laypage({
		        cont: 'pager', 
		        pages:json.last_page, 
		        curr: json.current_page,
		        skin: '#e23e3d',
		        groups: 3,
		        jump: function(e, first){
		        	if(!first){
		        		stockByPage(e.curr);
		        	}
		        } 
		    });
       	}  
	});
}
function getUseStatus(v){
   switch(v){
   	  case 0:return '未使用';
   	  case 1:return '已下单';
   }
}
function closeWin(){
	layer.close(w);
}
function addCardFunc(id,isContinue){
	var params =WST.getParams('.j-ipt');
	params.id = id;
	params.goodsId = $('#vid').val();
	if(params.cardNo=='' || params.cardPwd=='' || params.lastDate==''){
		WST.msg('请输入完整卡券信息',{icon:2});
		return;
	}
	ll = WST.load({msg:'数据处理中，请稍候...'});
	$.post(WST.U('home/goodsvirtuals/'+((params.id==0)?"add":"edit")),params,function(data){
		layer.close(ll);
		var json = WST.toJson(data);
		if(json.status==1){
			stockByPage(WSTCurrPage);
			if(isContinue){
                $('#cardForm')[0].reset();
			}else{
				closeWin();
			}
			WST.msg(json.msg, {icon: 1});
		}else{
			WST.msg(json.msg, {icon: 2});
		}
	});
}
var ll,w;
function editCard(id,goodsId){
	ll = WST.load({msg:'正在加载信息，请稍候...'});
	$.post(WST.U('home/goodsvirtuals/'+((id==0)?'toAdd':'toEdit')),{id:id},function(data){
		layer.close(ll);
		w = WST.open({
			    type: 1,
			    title:"新增卡券",
			    shade: [0.6, '#000'],
			    border: [0],
			    content: data,
			    area: ['400px', '180px']
			});
	});
}

function delCard(id,v){
	if(v==1){
		id  = WST.getChks('.vchk');
		id = id.join(',');
	}
	var c = WST.confirm({content:'您确定要删除卡券吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('home/goodsvirtuals/del'),{ids:id,id:$('#vid').val()},function(data,textStatus){
			layer.close(load);
		    var json = WST.toJson(data);
		    if(json.status==1){
		    	$('#all').prop('checked',false);
		    	stockByPage(WSTCurrPage);
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}

var uploading = null;
$(function(){
	var uploader = WST.upload({
        server:WST.U('home/goodsvirtuals/importCards'),pick:'#importBtn',
    	formData: {dir:'temp',goodsId:$('#vid').val()},
    	callback:function(f,file){
    		layer.close(uploading);
    		uploader.removeFile(file);
    		var json = WST.toJson(f);
    		if(json.status==1){
    			uploader.refresh();
    		    WST.msg('导入数据成功!已导入数据'+json.importNum+"条", {icon: 1});
    		    stockByPage(0);
    		}else{
    			WST.msg('导入数据失败,出错原因：'+json.msg, {icon: 5});
    		}
	    },
	    progress:function(rate){
	    	uploading = WST.msg('正在导入数据，请稍后...');
	    }
    });
});
