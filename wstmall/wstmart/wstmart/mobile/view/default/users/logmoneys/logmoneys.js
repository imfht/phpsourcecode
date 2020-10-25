jQuery.noConflict();
$(document).ready(function(){
  WST.initFooter('user');
  // 弹出层
  $("#frame").css('top',0);
  $("#frame").css('right','-100%');
});
//资金流水列表
function getRecordList(){
	  $('#Load').show();
	    loading = true;
	    var param = {};
	    param.type = $('#type').val() || -1;
	    param.pagesize = 10;
	    param.page = Number( $('#currPage').val() ) + 1;
	    $.post(WST.U('mobile/logMoneys/pageQuery'), param, function(data){
	        var json = WST.toJson(data.data);
	        var html = '';
	        if(json && json.data && json.data.length>0){
	          var gettpl = document.getElementById('scoreList').innerHTML;
	          laytpl(gettpl).render(json.data, function(html){
	            $('#score-list').append(html);
	          });

	          $('#currPage').val(json.current_page);
	          $('#totalPage').val(json.last_page);
	        }else{
	           html += '<div class="wst-prompt-icon"><img src="'+ window.conf.MOBILE +'/img/no_data.png"></div>';
	  	       html += '<div class="wst-prompt-info">';
	  	       html += '<p>暂无数据</p>';
	  	       html += '</div>';
	          $('#score-list').html(html);
	        }
	        loading = false;
	        $('#Load').hide();
	        echo.init();//图片懒加载
	    });
	}
// 验证支付密码资金
function check(){
  var isSetPayPwd = $('#isSet').val();
  if(isSetPayPwd==0){
  		$('#wst-event2').html('去设置');
  		WST.dialog('您未设置支付密码','location.href="'+WST.U('mobile/users/editPayPass')+'"');
		return;
	}else{
		showPayBox();
	}
  	
}
// 支付密码对话框
function showPayBox(){
    $("#wst-event3").attr("onclick","javascript:checkSecret()");
    $("#payPwdBox").dialog("show");
}
function checkSecret(){
	var payPwd = $.trim($('#payPwd').val());
	if(payPwd==''){
		WST.msg('请输入支付密码','info');
		return;
	}
    if(window.conf.IS_CRYPTPWD==1){
        var public_key=$('#key').val();
        var exponent="10001";
   	    var rsa = new RSAKey();
        rsa.setPublic(public_key, exponent);
        var payPwd = rsa.encrypt(payPwd);
    }
	$.post(WST.U('mobile/logmoneys/checkPayPwd'),{payPwd:payPwd},function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			$("#payPwdBox").dialog("hide");
			location.href=WST.U('mobile/cashconfigs/index');
		}else{
			WST.msg(json.msg);
		}
	})
}
//资金流水
function toRecord(){
	location.href = WST.U('mobile/logmoneys/record');
}
/********************  提现层 *************************/
function getCash(){
	$('#money').val('');
	$('#cashPayPwd').val('');
	dataShow();
}

function cashCard() {
	$.post(WST.U('mobile/cashconfigs/pageQuery'),{},function(data){
		var json = WST.toJson(data);
		var html = ''
		if(json.status==1){
			$(json.data.data).each(function(k,v){
				var bg = 'bg2';
				if (k%2 == 0) {
					bg = 'bg1';
				}

				html += "<div style='margin-bottom: -0.6rem' id='"+v.id+"card' name='"+v.accNo+"' onclick='selectCard("+ v.id +")'>";
				html += '<ul class="ui-row" style="border-radius: 0.15rem">';
				html += '<li class="ui-col ui-col-100 ' + bg + '">';
				html += '<div class="wst-flex-column bank-content"><div class="bank-box">';
				html += '<div class="bank-img"><i class="bank-img-logo" style="background: url("__RESOURCE_PATH__/'+ v.bankImg + '");background-size: 0.27rem 0.27rem;"></i></div>';
				html += '<div class="bank-name"><label>'+ v.bankName +'</label><br><label class="bank-n">'+ v.accUser +'</label></div></div>';
				html += '<div class="bank-no">' + v.accNo + '</div></li></ul></div>';

				// '<option value='+v.id+'>'+v.accUser+'|'+v.accNo+'</option>';
			});
			$('#accInfo').html(html);
			// 判断是否禁用按钮
			// if($('#userMoney').attr('money')<$('#userMoney').attr('cash'))$('#submit').attr('disabled','disabled');
		}else{
			WST.msg(json.msg,'info');
		}
	})
}

//点击账号
function selectCard(accId) {
	var accNo = $('#'+accId+'card').attr('name')
	$('#accNo').html(accNo);
	$('#accId').val(accId);
	dataHide(4)
}





// 申请提现
function drawMoney(){
	var accId = $('#accId').val();
	var money = $('#money').val();
	var payPwd = $('#cashPayPwd').val();

	if(accId==''){
		WST.msg('请选择提现账号','info');
		return;
	}
	if(money==''||money==0){
		WST.msg('请输入提现金额','info');
		return
	}
	if(payPwd==''){
		WST.msg('请输入支付密码','info');
		return
	}
    if(window.conf.IS_CRYPTPWD==1){
        var public_key=$('#key').val();
        var exponent="10001";
   	    var rsa = new RSAKey();
        rsa.setPublic(public_key, exponent);
        var payPwd = rsa.encrypt(payPwd);
    }
	var param = {};
	param.accId = accId;
	param.money = money;
	param.payPwd = payPwd;
	$.post(WST.U('mobile/cashdraws/drawMoney'),param,function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg('提现申请已提交','success');
			setTimeout(function(){
				location.reload();
			},1000);
		}else{
			WST.msg(json.msg,'info');
		}
	})
}

//弹框
function dataShow(n = 1){
	if (n == 2) {
		jQuery('#frame2').animate({"bottom": 0}, 500);
		cashCard();
	} else{
		jQuery('#frame').animate({"right": 0}, 500);
	}

}

function dataHide(n = 1){
	if (n == 2) {
		jQuery('#frame2').animate({'bottom': '-100%'}, 500);
	} else if(n == 3){
		jQuery('#frame').animate({'right': '-100%'}, 500);
	} else if (n == 4) {
		jQuery('#frame2').animate({'bottom': '-100%'}, 500);
	} else {
		jQuery('#frame').animate({'right': '-100%'}, 500);
	}
}

function cleverMoney() {
	$('#money').val('');
	$('#actualMoney').html('0');
	$("#chargeService").html('0');
}

function allMoney(num) {
	$('#money').val(num);
	changeDrawMoney($("#money"));
}

function changeDrawMoney(obj){
	WST.isChinese(this,1);
	var commission = $('#commission').html();
	var totalMoney = $(obj).val()?$(obj).val():0;
	totalMoney = parseFloat(totalMoney);
	if(!totalMoney){
		$("#chargeService").html("0");
		$("#actualMoney").html("0");
		return;
	}
	var money = 0;
	if(commission!=null){
		money = (parseFloat(totalMoney)*parseFloat(commission)*0.01).toFixed(2);
	}
	$("#chargeService").html(money);
	$("#actualMoney").html((parseFloat(totalMoney-money)).toFixed(2));
}