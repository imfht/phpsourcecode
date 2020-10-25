$(function () {
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
		   case 0:pageQuery(0);break;
		   case 1:pageConfigQuery(0);break;
		}	
	}})
});
var isSetPayPwd = 1;
function getUserMoney(){
	$.post(WST.U('home/users/getUserMoney'),{},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			var userMoney = json.data.userMoney;
			var rechargeMoney = json.data.rechargeMoney;
			$('#userMoney').html('¥'+userMoney);
			$('#lockMoney').html('¥'+json.data.lockMoney);
			rechargeMoney = parseFloat(userMoney - rechargeMoney)
			$('#userCashMoney').html('¥'+rechargeMoney.toFixed(2));
			if(json.data.isDraw==1){
               $('#drawBtn').show();
			}else{
               $('#drawBtn').hide();
			}
			isSetPayPwd = json.data.isSetPayPwd;
		}
	});
}
function pageQuery(p){
	var tips = WST.load({msg:'正在获取数据，请稍后...'});
	var params = {};
	params.page = p;
	$.post(WST.U('home/cashdraws/pageQuery'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	json = json.data;
		    var gettpl = document.getElementById('draw-list').innerHTML;
		    laytpl(gettpl).render(json.data, function(html){
		       	$('#draw-page-list').html(html);
		    });
		    if(json.last_page>1){
		       	laypage({
			        cont: 'draw-pager', 
			        pages:json.last_page, 
			        curr: json.current_page,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		pageQuery(e.curr);
			        	}
			        } 
			    });
		     }else{
		       	 $('#draw-pager').empty();
		     }
	    }
	});
}
var w;
function toDrawMoney(){
	if(isSetPayPwd==0){
		WST.msg('您尚未设置支付密码，请先设置支付密码',{icon:2},function(){
			location.href = WST.U('home/users/security');
		});
		return;
	}
    var tips = WST.load({msg:'正在获取数据，请稍后...'});
	$.post(WST.U('home/cashdraws/toEdit'),{},function(data,textStatus){
		layer.close(tips);
		w = WST.open({
		    type: 1,
		    title:"申请提现",
		    shade: [0.6, '#000'],
		    border: [0],
		    content: data,
		    area: ['550px', '300px'],
		    offset: '100px'
		});
	});
}
function drawMoney(){
	$('#drawForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
		    if(window.conf.IS_CRYPT=='1'){
		        var public_key=$('#token').val();
		        var exponent="10001";
		   	    var rsa = new RSAKey();
		        rsa.setPublic(public_key, exponent);
		        params.payPwd = rsa.encrypt(params.payPwd);
		    }
			var tips = WST.load({msg:'正在提交数据，请稍后...'});
			$.post(WST.U('home/cashdraws/drawMoney'),params,function(data,textStatus){
				layer.close(tips);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
		            	pageQuery(0);
		            	getUserMoney();
		            	layer.close(w);
		            });
			    }else{
			    	WST.msg(json.msg,{icon:2});
			    }
			});
		}
	});
}
function layerclose(){
  layer.close(w);
}

function pageConfigQuery(p){
	var tips = WST.load({msg:'正在获取数据，请稍后...'});
	var params = {};
	params.page = p;
	$.post(WST.U('home/cashconfigs/pageQuery'),params,function(data,textStatus){
		layer.close(tips);
	    var json = WST.toJson(data);
	    if(json.status==1){
	    	json = json.data;
		    var gettpl = document.getElementById('config-list').innerHTML;
		    laytpl(gettpl).render(json.data, function(html){
		       	$('#config-page-list').html(html);
		    });
		    if(json.last_page>1){
		       	laypage({
			        cont: 'config-pager', 
			        pages:json.last_page, 
			        curr: json.current_page,
			        skin: '#e23e3d',
			        groups: 3,
			        jump: function(e, first){
			        	if(!first){
			        		pageConfigQuery(e.curr);
			        	}
			        } 
			    });
		     }else{
		       	 $('#config-pager').empty();
		     }
	    }
	});
}

function toEditConfig(id){
	var tips = WST.load({msg:'正在获取数据，请稍后...'});
	$.post(WST.U('home/cashconfigs/toEdit','id='+id),{},function(data,textStatus){
		layer.close(tips);
		w = WST.open({
		    type: 1,
		    title:((id>0)?"编辑":"新增")+"提现账号",
		    shade: [0.6, '#000'],
		    border: [0],
		    content: data,
		    area: ['600px', '300px'],
		    offset: '100px'
		});
	});
} 
function editConfig(){
	$('#configForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			params.accAreaId = WST.ITGetAreaVal('j-areas');
			var tips = WST.load({msg:'正在提交数据，请稍后...'});
			$.post(WST.U('home/cashconfigs/'+((params.id>0)?'edit':'add')),params,function(data,textStatus){
				layer.close(tips);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
		            	pageConfigQuery(0);
		            	layer.closeAll();
		            });
			    }else{
			    	WST.msg(json.msg,{icon:2});
			    }
			});
		}
	});
}
function delConfig(id){
    WST.confirm({content:'您确定要删除该提现账号吗？',yes:function(){
   	    var tips = WST.load({msg:'正在提交数据，请稍后...'});
	    $.post(WST.U('home/cashconfigs/del'),{id:id},function(data,textStatus){
		    layer.close(tips);
			var json = WST.toJson(data);
			if(json.status==1){
		        WST.msg(json.msg,{icon:1},function(){
		            pageConfigQuery(0);
		        });
			}else{
			    WST.msg(json.msg,{icon:2});
			}
	  });
   }})
}

function changeDrawMoney(obj){
	WST.isChinese(this,1);
	var commission = $('#commission').val();
	var totalMoney = $(obj).val()?$(obj).val():0;
	totalMoney = parseFloat(totalMoney);
	if(!totalMoney){
		$("#chargeService").html("0");
		$("#actualMoney").html("0");
		return;
	}
	var money = 0;
	if(commission!=undefined){
		money = (parseFloat(totalMoney)*parseFloat(commission)*0.01).toFixed(2);
	}
	$("#chargeService").html(money);
	$("#actualMoney").html((parseFloat(totalMoney-money)).toFixed(2));
}