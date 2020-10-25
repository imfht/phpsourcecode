
function delVO(obj){
   $(obj).parent().remove();
	var selector = $(obj).attr('selector');
	var imgPath = [];
	$('.'+selector+'_step_pic').each(function(){
		imgPath.push($(this).attr('v'));
	});
	$('#'+selector).val(imgPath.join(','));
}

function initTime(id,val){
	var html = [],t0,t1;
	var str = val.split(':');
	for(var i=0;i<24;i++){
		t0 = (val.indexOf(':00')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		t1 = (val.indexOf(':30')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		html.push('<option value="'+i+':00" '+t0+'>'+i+':00</option>');
		html.push('<option value="'+i+':30" '+t1+'>'+i+':30</option>');
	}
	$(id).append(html.join(''));
}
function checkProtocol(obj){
    if(obj.checked){
    	$('.msg-box').hide();
    }else{
    	$('.msg-box').show();
    }
}

function saveStep(flowId,nextflowId){
    $('#applyFrom').isValid(function(v){
        if(v){
                var params = WST.getParams('.a-ipt');
                params.flowId = flowId;
                var catFee = $('#totalCatFee').html();
                if(catFee!=undefined)params.catFee = catFee;
                $("select[class^='j-']").each(function(idx,item){
                    var fieldName = $(item).attr('data-name');
                    params[fieldName] = WST.ITGetAreaVal('j-'+fieldName);
                });
                var load = WST.load({msg:'正在提交请求，请稍后...'});
                $.post(WST.U('home/suppliers/saveStep'),params,function(data,textStatus){
                    var json = WST.toJson(data);
                    if(json.status==1){
                    	if(json.data.pkey){
							location.href = WST.U('home/suppliers/joinstepnext','id='+json.data.nextflowId+'&pkey='+json.data.pkey);
						}else{
							location.href = WST.U('home/suppliers/joinstepnext','id='+json.data.nextflowId);
						}
                    }else{
                        layer.close(load);
                        WST.msg(json.msg,{icon:5});
                    }
                });
        }
    });
}
var container,map,label,marker,mapLevel;
function initQQMap(longitude,latitude,mapLevel){
    var container = document.getElementById('container');
    mapLevel = WST.blank(mapLevel,13);
    var mapopts,center = null;
    mapopts = {zoom: parseInt(mapLevel)};
	map = new qq.maps.Map(container, mapopts);
	if(WST.blank(longitude)=='' || WST.blank(latitude)==''){
		var cityservice = new qq.maps.CityService({
		    complete: function (result) {
		        map.setCenter(result.detail.latLng);
		    }
		});
		cityservice.searchLocalCity();
	}else{
        marker = new qq.maps.Marker({
            position:new qq.maps.LatLng(latitude,longitude), 
            map:map
        });
        map.panTo(new qq.maps.LatLng(latitude,longitude));
	}
	var url3;
	qq.maps.event.addListener(map, "click", function (e) {
		if(marker)marker.setMap(null); 
		marker = new qq.maps.Marker({
            position:e.latLng, 
            map:map
        });    
	    $('#latitude').val(e.latLng.getLat().toFixed(6));
	    $('#longitude').val(e.latLng.getLng().toFixed(6));
	    url3 = encodeURI(window.conf.HTTP+'apis.map.qq.com/ws/geocoder/v1/?location=' + e.latLng.getLat() + "," + e.latLng.getLng() + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
	    $.getJSON(url3, function (result) {
	        if(result.result!=undefined){
	            document.getElementById("supplierAddress").value = result.result.address;
	        }else{
	            document.getElementById("supplierAddress").value = "";
	        }

	    })
	});
	qq.maps.event.addListener(map,'zoom_changed',function() {
        $('#mapLevel').val(map.getZoom());
    });
}
function mapCity(obj){
    var className = $(obj).attr('data-name');
    var citys = [];
    $('.j-'+className).each(function(){
        citys.push($(this).find('option:selected').text());
    })
    if(citys.length==0)return;
    var url2 = encodeURI(window.conf.HTTP+'apis.map.qq.com/ws/geocoder/v1/?region=' + citys.join('') + "&address=" + citys.join('') + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
    $.getJSON(url2, function (result) {
        if(result.result.location){
            map.setCenter(new qq.maps.LatLng(result.result.location.lat, result.result.location.lng));
        }
    });
}

function getPayUrl(){
	var params = {};
	params.payObj = "supplier_enter";
	params.pkey = $.trim($("#pkey").val());
	params.payCode = $.trim($("#payCode").val());
	params.flowId = $('#flowId').val();
	params.payStep = 2;
	if(params.payCode==""){
		WST.msg('请先选择支付方式', {icon: 5});
		return;
	}
	jQuery.post(WST.U('home/'+params.payCode+'/get'+params.payCode+"URL"),params,function(data) {
		var json = WST.toJson(data);
		if(json.status==1){
			if(params.payCode=="alipays"){
				$("#alipayform").html(json.result);
			}else{
				location.href = json.url;
			}
		}else{
			WST.msg('缴纳年费失败', {icon: 5});
		}
	});
}

function checkApply(obj,itemId){
	$("body").css({"background":"#f8f8f8"});
	$(".apply-step-nav h3").html($(obj).attr("data"));
	$(".apply-step-nav").show();
	$(".apply-nav li").removeClass("curr");
	$(obj).addClass("curr");
	$(".apply-item").hide();
	$("#apply-article-"+itemId).show();
}

function payByWallet(){
	var params = WST.getParams('.j-ipt');
	if(params.payPwd==""){
		WST.msg('请输入密码', {icon: 5});
		return;
	}
	if(window.conf.IS_CRYPT=='1'){
		var public_key=$('#token').val();
		var exponent="10001";
		var rsa = new RSAKey();
		rsa.setPublic(public_key, exponent);
		params.payPwd = rsa.encrypt(params.payPwd);
	}
	var load = WST.load({msg:'正在核对支付密码，请稍后...'});
	$.post(WST.U('home/wallets/supplierEnterPayByWallet'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg(json.msg, {icon: 1,time:1500},function(){
				window.location = WST.U('home/suppliers/joinstepnext','id='+$('#flowId').val());
			});
		}else{
			WST.msg(json.msg,{icon:2,time:1500});
		}
	});
}

function setPaypwd(){
	layerbox =	layer.open({
		title:['设置支付密码','text-align:left'],
		type: 1,
		area: ['450px', '240px'],
		content: $('.j-paypwd-box'),
		btn: ['设置支付密码，并支付年费', '关闭'],
		yes: function(index, layero){
			var newPass = $.trim($("#payPwd").val());
			var reNewPass = $.trim($("#reNewPass").val());
			if(newPass==""){
				WST.msg("请输入支付密码！");
				return false;
			}
			if(reNewPass==""){
				WST.msg("请输入确认支付密码！");
				return false;
			}
			if(newPass!=reNewPass){
				WST.msg("密码不一致！");
				return false;
			}
			if(window.conf.IS_CRYPT=='1'){
				var public_key=$('#token').val();
				var exponent="10001";
				var rsa = new RSAKey();
				rsa.setPublic(public_key, exponent);
				newPass = rsa.encrypt(newPass);
				reNewPass = rsa.encrypt(reNewPass);
			}
			var load = WST.load({msg:'正在提交支付密码，请稍后...'});
			$.post(WST.U('home/users/payPassEdit'),{newPass:newPass,reNewPass:reNewPass},function(data,textStatus){
				layer.close(load);
				var json = WST.toJson(data);
				if(json.status==1){
					WST.msg(json.msg, {icon: 1,time:1500},function(){
						layer.close(layerbox);
						payByWallet();
					});
				}else{
					WST.msg(json.msg,{icon:2,time:1500});
				}
			});

			return false;
		},
		btn2: function(index, layero){}
	});
}