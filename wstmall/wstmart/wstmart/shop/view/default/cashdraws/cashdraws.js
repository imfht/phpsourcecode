$(function () {
	$('#tab').TabPanel({tab:0,callback:function(tab){
		switch(tab){
		   case 0:pageQuery();break;
		   case 1:pageConfigQuery(0);break;
		}	
	}})
});
var isSetPayPwd = 1;
function getShopMoney(){
	$.post(WST.U('shop/shops/getShopMoney'),{},function(data,textStatus){
		var json = WST.toJson(data);
		if(json.status==1){
			var shopMoney = json.data.shopMoney;
			var rechargeMoney = json.data.rechargeMoney;
			$('#shopMoney').html('¥'+shopMoney);
			$('#lockMoney').html('¥'+json.data.lockMoney);
			rechargeMoney = parseFloat(shopMoney - rechargeMoney)
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
function pageQuery(){
    var h = WST.pageHeight();
    var cols = [
        {title:'提现单号', name:'cashNo', width: 100},
        {title:'提现银行', name:'accTargetName', width: 100},
        {title:'开户地区', name:'accAreaName', width: 150},
        {title:'银行卡号', name:'accNo', width: 100},
        {title:'持卡人', name:'accUser', width: 100},
        {title:'提现金额', name:'money', width: 50,renderer:function(val,item,rowIndex){return '￥'+val;}},
        {title:'提现状态', name:'', width: 150,renderer:function(val,item,rowIndex){
                if(item['cashSatus']==1){
                    return "<span class='statu-yes'><i class='fa fa-check-circle'></i> 提现成功</span>";
                }else if(item['cashSatus']==-1){
                    return "<span class='statu-yes'>提现失败<br/>【原因】"+item['cashRemarks']+"</span>";
                }else{
                    return "<span class='statu-yes'><i class='fa fa-clock-o'></i> 待处理</span>";
				}
            }},
    ];

    mmg = $('.mmg').mmGrid({height: h-193,indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('shop/cashdraws/pageQueryByShop'), fullWidthRows: true, autoLoad: true,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
}
function loadGrid(){
    mmg.load({page:1});
}
var w;
function toDrawMoney(){
	if(isSetPayPwd==0){
		WST.msg('您尚未设置支付密码，请先设置支付密码',{icon:2,time:1000},function(){
			location.href = WST.U('shop/users/security');
		});
		return;
	}
    var tips = WST.load({msg:'正在获取数据，请稍后...'});
	$.post(WST.U('shop/cashdraws/toEditByShop'),{},function(data,textStatus){
		layer.close(tips);
		w = WST.open({
		    type: 1,
		    title:"申请提现",
		    shade: [0.6, '#000'],
		    border: [0],
		    content: data,
		    area: ['550px', '350px'],
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
			$.post(WST.U('shop/cashdraws/drawMoneyByShop'),params,function(data,textStatus){
				layer.close(tips);
			    var json = WST.toJson(data);
			    if(json.status==1){
		            WST.msg(json.msg,{icon:1},function(){
                        loadGrid();
		            	getShopMoney();
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