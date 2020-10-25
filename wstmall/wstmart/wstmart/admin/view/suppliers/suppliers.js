var mmg,mmg2;
function initGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'供货商编号', name:'supplierSn', width: 30,sortable: true},
            {title:'供货商账号', name:'loginName',width: 60,sortable: true},
            {title:'供货商名称', name:'supplierName',width: 100,sortable: true},
            {title:'所属行业', name:'tradeName',width: 80,sortable: true},
            {title:'店主姓名', name:'supplierkeeper',width: 40,hidden: true,sortable: true},
            {title:'店主联系电话', name:'telephone',width: 30,hidden: true,sortable: true},
            {title:'供货商地址', name:'supplierAddress',width:180 },
            {title:'所属公司', name:'supplierCompany',width: 60,hidden: true},
            {title:'营业状态', name:'supplierAtive' ,width: 20,sortable: true,renderer: function (val,item,rowIndex){
	        	return (item['supplierAtive']==1)?"<span class='statu-yes'><i class='fa fa-check-circle'></i> 营业中</span>":"<span class='statu-wait'><i class='fa fa-coffee'></i> 休息中</span>";
	        }},
            {title:'到期日期', name:'expireDate' ,width: 20,sortable: true,renderer: function (val,item,rowIndex){
                return (item['isExpire']==true)?"<span class='expire-yes'>"+item['expireDate']+"</span>":"<span>"+item['expireDate']+"</span>";
            }},
            {title:'操作', name:'' ,width:180, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            if(WST.GRANT.GDPGL_02)h += "<a class='btn btn-blue' href=\"javascript:toEdit(" + item['supplierId'] + ",\'index\')\"><i class='fa fa-pencil'></i>修改</a> ";
	            if(WST.GRANT.GDPGL_03)h += "<a class='btn btn-red' href='javascript:toDel(" + item['supplierId'] + ",1)'><i class='fa fa-trash-o'></i>删除</a> ";
	            h += "<a class='btn btn-blue' href='"+WST.U('admin/logmoneys/tologmoneys','id='+item['supplierId']+'&src=suppliers&p='+WST_CURR_PAGE)+"&type=3'><i class='fa fa-search'></i>供货商资金</a>";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/suppliers/pageQuery'), fullWidthRows: true, autoLoad: false,
        remoteSort:true ,
        sortName: 'supplierSn',
        sortStatus: 'desc',
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadGrid(p);
}
function loadGrid(p){
	p=(p<=1)?1:p;
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.page = p;
	mmg.load(params);
}
function initApplyGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'申请人账号', name:'loginName', width: 30},
            {title:'供货商名称', name:'supplierName',width:100 },
            {title:'所属行业', name:'tradeName',width: 80,sortable: true},
            {title:'所属公司', name:'supplierCompany',width:100 },
            {title:'申请联系人', name:'applyLinkMan',width:30 },
            {title:'申请联系人电话', name:'applyLinkTel',width:60 },
            {title:'对接商城招商人员', name:'applyLinkTel' ,width:60,renderer: function (val,item,rowIndex){
	        	return (item['isInvestment']==1)?item['investmentStaff']:'-';
	        }},
            {title:'申请日期', name:'applyTime' },
            {title:'申请状态', name:'applyStatus' ,width:30,renderer: function (val,item,rowIndex){
	        	if(item['applyStatus']==1){
                    return "<span class='statu-wait'><i class='fa fa-clock-o'></i> 待处理</span>";
	        	}else if(item['applyStatus']==0){
                    return "<span class='statu-wait'><i class='fa fa-clock-o'></i> 填写中</span>";
	        	}else{
                    return "<span class='statu-no'><i class='fa fa-ban'></i> 申请失败</span>";
	        	}
	        }},
            {title:'操作', name:'' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            if(WST.GRANT.GDPSQ_04)h += "<a class='btn btn-blue' href='javascript:toHandle(" + item['supplierId'] + ")'><i class='fa fa-pencil'></i>操作</a> ";
	            if(WST.GRANT.GDPSQ_03)h += "<a class='btn btn-red' href='javascript:toDelApply(" + item['supplierId'] + ")'><i class='fa fa-trash-o'></i>删除</a> "; 
	            return h;
            }}
            ];
 
    mmg = $('#mmg').mmGrid({height: (h-100),indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/suppliers/pageQueryByApply'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadApplyGrid(p);
}
function loadApplyGrid(p){
	p=(p<=1)?1:p;
	var params = WST.getParams('.j-ipt');
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.page = p;
	mmg.load(params);
}
function toHandle(id){
	location.href = WST.U('admin/suppliers/toHandleApply','id='+id+'&p='+WST_CURR_PAGE);
}
function toDelApply(id){
	var box = WST.confirm({content:"您确定要彻底删除该供货商申请信息吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/suppliers/delApply'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           		            loadApplyGrid(WST_CURR_PAGE);
	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function initStopGrid(p){
    var h = WST.pageHeight();
    var cols = [
            {title:'供货商编号', name:'supplierSn', width: 30},
            {title:'供货商账号', name:'loginName', width: 60},
            {title:'供货商名称', name:'supplierName',width: 120},
            {title:'所属行业', name:'tradeName',width: 80,sortable: true},
            {title:'店主姓名', name:'supplierkeeper',width: 40,hidden: true},
            {title:'店主联系电话', name:'telephone',hidden: true},
            {title:'供货商地址', name:'supplierAddress',width:260 },
            {title:'所属公司', name:'supplierCompany',hidden: true },
            {title:'操作', name:'' ,width:80, align:'center', renderer: function(val,item,rowIndex){
                var h = "";
	            h += "<a class='btn btn-blue' href=\"javascript:toEdit(" + item['supplierId'] + ",\'stopIndex\')\"><i class='fa fa-pencil'></i>修改</a> ";
	            h += "<a class='btn btn-red' href='javascript:toDel(" + item['supplierId'] + ",2)'><i class='fa fa-trash-o'></i>删除</a> ";
	            return h;
            }}
            ];
 
    mmg = $('.mmg').mmGrid({height: (h-90),indexCol: true, cols: cols,method:'POST',nowrap:true,
        url: WST.U('admin/suppliers/pageStopQuery'), fullWidthRows: true, autoLoad: false,
        plugins: [
            $('#pg').mmPaginator({})
        ]
    });
    loadStopGrid(p);
}
function loadStopGrid(p){
	var params = WST.getParams('.j-ipt');
	p=(p<=1)?1:p;
	params.areaIdPath = WST.ITGetAllAreaVals('areaId1','j-areas').join('_');
	params.page = p;
	mmg.load(params);
}
var initTab2 = false,initTab3 = false;
function initUpload(isEdit){
	if(!isEdit){
        legalCertificateImgUpload();
		businessLicenceImgUpload();
		bankAccountPermitImgUpload();
		organizationCodeUpload();
		taxRegistrationCertificateUpload();
		taxpayerQualificationUpload();
	}else{
		var element = layui.element;
		element.on('tab(msgTab)', function(data){
		   if(data.index==1){
		   	   if(initTab2)return;
		       initTab2 = true;
               legalCertificateImgUpload();
			   businessLicenceImgUpload();
			   bankAccountPermitImgUpload();
			   organizationCodeUpload();
		   }else if(data.index==2){
		   	   if(initTab3)return;
		       initTab3 = true;
               taxRegistrationCertificateUpload();
			   taxpayerQualificationUpload();
		   }
	    });
	}
}
function legalCertificateImgUpload (){
	WST.upload({
			pick:'#legalCertificateImgPicker',
			formData: {dir:'suppliers'},
			accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
			callback:function(f){
				var json = WST.toAdminJson(f);
				if(json.status==1){
				  	$('#legalCertificateImgMsg').empty().hide();
				    $('#legalCertificateImgPreview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb).show();
				    $('#legalCertificateImgPreview_a').attr('href',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name);
				    $('#legalCertificateImg').val(json.savePath+json.name);
				    $('#msg_legalCertificateImg').hide();
				}
			},
			progress:function(rate){
				$('#legalCertificateImgMsg').show().html('已上传'+rate+"%");
			}
		});
}
function businessLicenceImgUpload(){
	WST.upload({
			pick:'#businessLicenceImgPicker',
			formData: {dir:'suppliers'},
			accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
			callback:function(f){
				var json = WST.toAdminJson(f);
				if(json.status==1){
					$('#businessLicenceImgMsg').empty().hide();
					$('#businessLicenceImgPreview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb).show();
					$('#businessLicenceImgPreview_a').attr('href',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name);
					$('#businessLicenceImg').val(json.savePath+json.name);
					$('#msg_businessLicenceImg').hide();
				}
			},
			progress:function(rate){
				$('#businessLicenceImgMsg').show().html('已上传'+rate+"%");
			}
		});
}
function bankAccountPermitImgUpload(){
	WST.upload({
			pick:'#bankAccountPermitImgPicker',
			formData: {dir:'suppliers'},
			accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
			callback:function(f){
				var json = WST.toAdminJson(f);
				if(json.status==1){
					$('#bankAccountPermitImgMsg').empty().hide();
					$('#bankAccountPermitImgPreview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb).show();
					$('#bankAccountPermitImgPreview_a').attr('href',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name);
					$('#bankAccountPermitImg').val(json.savePath+json.name);
					$('#msg_bankAccountPermitImg').hide();
				}
			},
			progress:function(rate){
				$('#bankAccountPermitImgMsg').show().html('已上传'+rate+"%");
			}
		});
}
function organizationCodeUpload(){
	WST.upload({
			pick:'#organizationCodeImgPicker',
			formData: {dir:'suppliers'},
			accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
			callback:function(f){
				var json = WST.toAdminJson(f);
				if(json.status==1){
					$('#organizationCodeImgMsg').empty().hide();
					$('#organizationCodeImgPreview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb).show();
					$('#organizationCodeImgPreview_a').attr('href',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name);
					$('#organizationCodeImg').val(json.savePath+json.name);
					$('#msg_organizationCodeImg').hide();
				}
			},
			progress:function(rate){
				$('#organizationCodeImgMsg').show().html('已上传'+rate+"%");
			}
		});
}
function taxRegistrationCertificateUpload(){
	var uploader = WST.upload({
				pick:'#taxRegistrationCertificateImgPicker',
			    formData: {dir:'suppliers'},
				accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
				fileNumLimit:3,
				callback:function(f,file){
					var json = WST.toAdminJson(f);
					if(json.status==1){
					  	$('#taxRegistrationCertificateImgMsg').empty().hide();
					  	var tdiv = $("<div style='height:30px;float:left;margin:0px 5px;position:relative'><a target='_blank' href='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name+"'>"+
			                       "<img class='step_pic"+"' height='30' src='"+WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb+"' v='"+json.savePath+json.name+"'></a></div>");
						var btn = $('<div style="position: absolute;top: -5px;right: 0px;cursor: pointer;background: rgba(0,0,0,0.5);width: 18px;height: 18px;text-align: center;border-radius: 50%;" ><img src="'+WST.conf.ROOT+'/wstmart/home/View/default/img/seller_icon_error.png"></div>');
						tdiv.append(btn);
						$('#taxRegistrationCertificateImgBox').append(tdiv);
						$('#msg_taxRegistrationCertificateImg').hide();
						var imgPath = [];
						$('.step_pic').each(function(){
			                imgPath.push($(this).attr('v'));
						});
			            $('#taxRegistrationCertificateImg').val(imgPath.join(','));
						btn.on('click','img',function(){
						    uploader.removeFile(file);
						    $(this).parent().parent().remove();
						    uploader.refresh();
						    if($('#taxRegistrationCertificateImgBox').children().size()<=0){
						         $('#msg_taxRegistrationCertificateImg').show();
						    }
						});
					}else{
					  		 WST.msg(json.msg,{icon:2});
					}
				},
				progress:function(rate){
					$('#taxRegistrationCertificateImgMsg').show().html('已上传'+rate+"%");
				}
			});
}
function taxpayerQualificationUpload(){
	WST.upload({
			pick:'#taxpayerQualificationImgPicker',
			formData: {dir:'suppliers'},
			accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
			callback:function(f){
				var json = WST.toAdminJson(f);
				if(json.status==1){
					$('#taxpayerQualificationImgMsg').empty().hide();
					$('#taxpayerQualificationImgPreview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb).show();
					$('#taxpayerQualificationImgPreview_a').attr('href',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.name);
					$('#taxpayerQualificationImg').val(json.savePath+json.name);
					$('#msg_taxpayerQualificationImg').hide();
				}
			},
			progress:function(rate){
				$('#taxpayerQualificationImgMsg').show().html('已上传'+rate+"%");
			}
	});
}

function delVO(obj){
	$(obj).parent().remove();
	var selector = $(obj).attr('selector');
	var imgPath = [];
	$('.'+selector+'_step_pic').each(function(){
		imgPath.push($(this).attr('v'));
	});
	$('#'+selector).val(imgPath.join(','));
}
function toEdit(id,src){
	location.href=WST.U('admin/suppliers/toEdit','id='+id+'&p='+WST_CURR_PAGE+'&src='+src);
}
function toDel(id,type){
	var box = WST.confirm({content:"您确定要删除该供货商吗?",yes:function(){
	           var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
	           $.post(WST.U('admin/suppliers/del'),{id:id},function(data,textStatus){
	           			  layer.close(loading);
	           			  var json = WST.toAdminJson(data);
	           			  if(json.status=='1'){
	           			    	WST.msg("操作成功",{icon:1});
	           			    	layer.close(box);
	           			    	if(type==1){
                                    loadGrid(WST_CURR_PAGE);
								}else{
                                    loadStopGrid(WST_CURR_PAGE)
								}

	           			  }else{
	           			    	WST.msg(json.msg,{icon:2});
	           			  }
	           		});
	            }});
}
function checkLoginKey(obj){
	if($.trim(obj.value)=='')return;
	var params = {key:obj.value,userId:0};
	var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/users/checkLoginKey'),params,function(data,textStatus){
    	layer.close(loading);
    	var json = WST.toAdminJson(data);
    	if(json.status!='1'){
    		WST.msg(json.msg,{icon:2});
    		obj.value = '';
    	}
    });
}
function save(p,src){
	$('#editFrom').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			var params = WST.getParams('.a-ipt');
            $("select[class^='j-']").each(function(idx,item){
                var fieldName = $(item).attr('data-name');
                params[fieldName] = WST.ITGetAreaVal('j-'+fieldName);
            });
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('admin/suppliers/edit'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toAdminJson(data);
		    	if(json.status=='1'){
		    		WST.msg("操作成功",{icon:1,time:1000},function(){
		    			if(params.supplierStatus==1){
			    			location.href=WST.U('admin/suppliers/index','p='+p);
			    		}else{
                            location.href=WST.U('admin/suppliers/stopIndex','p='+p);
			    		}
		    		});
		    		
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
function getUserByKey(){
	if($.trim($('#keyName').val())=='')return;
	$('#keyNameBox').html('');
	$('#supplierUserId').val(0);
	var loading = WST.msg('正在查询用户信息...', {icon: 16,time:60000});
    $.post(WST.U('admin/users/getUserByKey'),{key:$('#keyName').val()},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
		    $('#keyNameBox').html('用户：'+json.data.loginName);
		    $('#supplierUserId').val(json.data.userId);
		}else{
		    WST.msg(json.msg,{icon:2});
		}
    });
}
function add(p,src){
	$('#editFrom').isValid(function(v){
		if(v){
			var params = WST.getParams('.a-ipt');
            $("select[class^='j-']").each(function(idx,item){
                var fieldName = $(item).attr('data-name');
                params[fieldName] = WST.ITGetAreaVal('j-'+fieldName);
            });
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('admin/suppliers/add'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toAdminJson(data);
		    	if(json.status=='1'){
		    		WST.msg("操作成功",{icon:1,time:1000},function(){
			    		location.href=WST.U('admin/suppliers/'+src,'p='+p);
		    		});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}

function apply(p){
	$('#editFrom').isValid(function(v){
		if(v){
			var params = WST.getParams('.a-ipt');
            $("select[class^='j-']").each(function(idx,item){
                var fieldName = $(item).attr('data-name');
                params[fieldName] = WST.ITGetAreaVal('j-'+fieldName);
            });
			if(params.applyStatus==-1 && params.applyDesc==''){
				 WST.msg('请输入审核不通过原因!',{icon:2});
				 return;
			}
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('admin/suppliers/handleApply'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toAdminJson(data);
		    	if(json.status=='1'){
		    		WST.msg("操作成功",{icon:1,time:1000},function(){
			    		location.href=WST.U('admin/suppliers/apply',"p="+p);
		    		});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
function initTime($id,val){
	var html = [],t0,t1;
	var str = val.split(':');
	for(var i=0;i<24;i++){
		t0 = (val.indexOf(':00')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		t1 = (val.indexOf(':30')>-1 && (parseInt(str[0],10)==i))?'selected':'';
		html.push('<option value="'+i+':00" '+t0+'>'+i+':00</option>');
		html.push('<option value="'+i+':30" '+t1+'>'+i+':30</option>');
	}
	$($id).append(html.join(''));
}
var container,map,label,marker,mapLevel = 15;
function initQQMap(longitude,latitude,mapLevel){
    var container = document.getElementById("container");
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
	    url3 = encodeURI(window.conf.__HTTP__+'apis.map.qq.com/ws/geocoder/v1/?location=' + e.latLng.getLat() + "," + e.latLng.getLng() + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
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
    var citys = [];
    $('.j-'+$(obj).attr('data-name')).each(function(){
        citys.push($(this).find('option:selected').text());
    })
    if(citys.length==0)return;
    var url2 = encodeURI(window.conf.__HTTP__+'apis.map.qq.com/ws/geocoder/v1/?region=' + citys.join('') + "&address=" + citys.join('') + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
    $.getJSON(url2, function (result) {
        if(result.result.location){
            map.setCenter(new qq.maps.LatLng(result.result.location.lat, result.result.location.lng));
            map.setZoom(mapLevel);
        }
    });
}
