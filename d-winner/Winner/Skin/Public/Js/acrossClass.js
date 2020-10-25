// JavaScript Document
function acrossClass(){
	this.act = '';						//与服务器互交地址
	
	//判断属性
	var getOpt = function(val,opts){
		var defopt = {
			id:'',						//盒子ID
			cls:'mail-height225',		//多选框样式
			mail:1,						//是否显示邮箱地址 1为显示，0为不显示
			mode:3,						//多选框层数
			used:''						//默认值
		};
		if(opts==undefined){
			return defopt[val];
		}else{
			if(opts[val]==undefined){
				return defopt[val];
			}else{
				return opts[val];
			}
		}
	};
	
	//控制方法
	this.show = function(option){
		var _act = this.act;
		var pid = new Array();
		var uid = new Array();
		var ouid = new Array();
		var boxid = getOpt('id',option);
		var idd = "#"+getOpt('id',option);
		var mod = getOpt('mode',option);
		var cinfo,pinfo,uinfo;
		//alert(getOpt('used',option));
		$(document).ready(function(){
			$.ajaxSetup({  
				async : false  
			});
			
			$.get(_act+'/defInfo/act/comy', function(data){
				cinfo = data;
			});
			$.get(_act+'/defInfo/act/part', function(data){
				pinfo = data;
			});
			$.get(_act+'/defInfo/act/user', function(data){
				uinfo = data;
			});
			$.ajaxSetup({  
				async : true  
			});
			//alert(uinfo);
			//alert(getOpt('mail',option));
			
			$(idd).html('<table class="infobox" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse;border-width:0px; margin-bottom:-1px; border-style:none;"></table>');
			switch(mod){
				case 2:
					$(idd+" table").append('<tr class="bar">'
							+'<td><strong>待选</strong></td>'
							+'<td align="center"><input type="text" class="easyui-validatebox" id="userBox'+boxid+'" style="width:94%; text-transform:uppercase" size="4" /></td>'
							+'<td width="3%">&nbsp;</td>'
							+'<td width="78%"><strong>已选</strong></td>'
						+'</tr>'
						+'<tr id="see'+boxid+'">'
						+'<td width="8%"><select  class="'+getOpt('cls',option)+'" name="part_id" size="20" multiple="multiple" id="partId'+boxid+'" style="width:130px;" onchange=\'return preventSelectDisabled(this)\'>'
						+' <option value="0" id="0">所有部门</option>'
						+ pinfo
						+'</select></td>'
						+'<td width="11%">'
						+'<select class="'+getOpt('cls',option)+'" name="user_id" size="20" multiple="multiple" id="userId'+boxid+'" onchange=\'return preventSelectDisabled(this)\'>'
						+ uinfo
						+'</select>'
						+'</td>'
						+'<td width="3%">'
						+' <input id="go'+boxid+'" class="to-but" type="button" value="&gt;" style="margin-bottom:10px" />'
						 +' <br />'
						 +' <input id="back'+boxid+'"  class="to-but" type="button" value="&lt;"  style="margin-bottom:18px"/>'
						 +' <br />'
						 +' <input id="goa'+boxid+'" class="to-but" type="button" value="&gt;&gt;" style="margin-bottom:10px" />'
						 +' <br />'
						 +' <input id="backa'+boxid+'"  class="to-but" type="button" value="&lt;&lt;" />'
						 +' <br /></td>'
						+'<td width="78%"><select class="'+getOpt('cls',option)+'" style="width:99%;" name="touser[]" size="20" multiple="multiple" id="toUser'+boxid+'" onchange=\'return preventSelectDisabled(this)\'>'
						+ getOpt('used',option)
						+'</select></td>'
					+' </tr>');
				
					$("#partId"+boxid).val(0);
					var opt = $("#partId"+boxid).find('option:selected');
					var val = opt.val();
					var now_id = "#partId"+boxid;
					var now_mo = "part";
				break;
				
				case 3:
					$(idd+" table").append('<tr class="bar">'
							+'<td><strong>待选</strong></td>'
							+'<td>&nbsp;</td>'
							+'<td align="center"><input type="text" class="easyui-validatebox" id="userBox'+boxid+'" style="width:94%; text-transform:uppercase" size="4" /></td>'
							+'<td width="3%">&nbsp;</td>'
							+'<td width="70%"><strong>已选</strong></td>'
						+'</tr>'
						+'<tr id="see'+boxid+'">'
						+'<td width="8%" height="200"><select class="'+getOpt('cls',option)+'" size="20" multiple="multiple" name="company_id" id="companyId'+boxid+'" onchange=\'return preventSelectDisabled(this)\'>'
						 +' <option value="0" id="0">所有公司</option>'
						 + cinfo
						+'</select></td>'
						+'<td width="8%"><select  class="'+getOpt('cls',option)+'" name="part_id" size="20" multiple="multiple" id="partId'+boxid+'" style="width:130px;" onchange=\'return preventSelectDisabled(this)\'>'
						+'</select></td>'
						+'<td width="11%">'
						+'<select class="'+getOpt('cls',option)+'" name="user_id" size="20" multiple="multiple" id="userId'+boxid+'" onchange=\'return preventSelectDisabled(this)\'>'
						+ uinfo
						+'</select>'
						+'</td>'
						+'<td width="3%">'
						+' <input id="go'+boxid+'" class="to-but" type="button" value="&gt;" style="margin-bottom:10px" />'
						 +' <br />'
						 +' <input id="back'+boxid+'"  class="to-but" type="button" value="&lt;"  style="margin-bottom:18px"/>'
						 +' <br />'
						 +' <input id="goa'+boxid+'" class="to-but" type="button" value="&gt;&gt;" style="margin-bottom:10px" />'
						 +' <br />'
						 +' <input id="backa'+boxid+'"  class="to-but" type="button" value="&lt;&lt;" />'
						 +' <br /></td>'
						+'<td width="70%"><select class="'+getOpt('cls',option)+'" style="width:99%;" name="touser[]" size="20" multiple="multiple" id="toUser'+boxid+'" onchange=\'return preventSelectDisabled(this)\'>'
						+ getOpt('used',option)
						+'</select></td>'
					+' </tr>');
					
					$("#companyId"+boxid).val(0);
					var opt = $("#companyId"+boxid).find('option:selected');
					var val = opt.val();
					var now_id = "#companyId"+boxid;
					var now_mo = "comy";
				break;
			}
			
			if(val==0){
				var allopt = $(now_id).find('option:gt(0)');
				allopt.each(function(){
					//alert(mod);
					var tval = $(this).val();
					if(mod>=2){
						$.post(_act+'/change/act/'+now_mo, {id:tval},function(data){
							pid[tval] = data;
						});
					}
					$.post(_act+'/change/act/'+now_mo+'/mode/1', {id:tval},function(data){
						uid[tval] = data;
						ouid[tval] = data;
					});
				});
				
				if(!isset(uid[val])){
					$.post(_act+'/change/act/'+now_mo+'/mode/1', {id:val},function(data){
						uid[val] = data;
						ouid[val] = data;
					});
				}
			}else{
				if(mod>=3){
					if(!isset(pid[val])){
						$.post(_act+'/change/act/'+now_mo, {id:val},function(data){
							$("#partId"+boxid).html(data);
							pid[val] = data;
						});
					}
				}
				
				if(!isset(uid[val])){
					$.post(_act+'/change/act/'+now_mo+'/mode/1', {id:val},function(data){
						$("#userId"+boxid).html(data);
						uid[val] = data;
						ouid[val] = data;
					});
				}
			}
			 
			var uuh = ouid;
			$("#userId"+boxid).dblclick(function(){
				$.ajaxSetup({  
					async : false  
				});
				var opt = $(this).find('option:selected');
				var uopt =  $(this).find('option');
				var topt = $("#toUser"+boxid).find('option');
				var val = opt.val();
				if(val){
					var txt = opt.text();
					opt.attr("disabled","disabled");
					opt.addClass('disabled');
					$(this).val("");
					var idd = opt.attr('id');
					var mail = opt.attr('mail');
					if(mod>=2){
						var popt = $("#partId"+boxid+" option[value="+idd+"]");
					}
					var cid = opt.attr('cid');
					var re = new RegExp('value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
					if(!isset(uid[idd])){
						$.post(_act+'/change/act/part', {id:idd},function(datas){
							uid[idd] = datas.replace(re,'value="'+val+'"$1 disabled class=disabled');
						});
					}else{
						uid[idd] = uid[idd].replace(re,'value="'+val+'"$1 disabled class=disabled');
					}
					uuh[0] = uuh[0].replace(re,'value="'+val+'"$1 disabled class=disabled');
					uid[0] = uuh[0];
					if(mod>=3){
						uuh[cid] = uuh[cid].replace(re,'value="'+val+'"$1 disabled class=disabled');
						uid[cid] = uuh[cid];
					}
					
					var ishas = 0;
					topt.each(function(){
						var nowval = $(this).val();
						if(val==nowval){
							ishas = 1;
						}
					});
					if(ishas==0){
						if(getOpt('mail',option)){
							$("#toUser"+boxid).append('<OPTION id='+idd+' value='+val+' cid="'+cid+'">'+txt+' ： '+mail+'</OPTION>');
						}else{
							$("#toUser"+boxid).append('<OPTION id='+idd+' value='+val+' cid="'+cid+'">'+txt+'</OPTION>');
						}
					}
					
					if(mod>=2){
						var num = 0;
						uopt.each(function(){
							var da = $(this).attr("disabled");
							//alert(isset(da));
							if(!isset(da)){
								num++;
							}
						});
						
						if(num==0){
							if(popt.length>0){
								popt.attr("disabled","disabled");
								popt.addClass('disabled');
								$("#partId"+boxid).val("");
								pid[cid] = $("#partId"+boxid).html();
							}else{
								//alert(pid[cid]);
								var re = new RegExp('value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
								pid[cid] = pid[cid].replace(re,'value="'+idd+'" disabled class=disabled$1');
							}
						}
					}
				}
				
				$.ajaxSetup({  
					async : true  
				}); 
			});
			
			if(mod>=2){
				var puh = ouid;
				$("#partId"+boxid).dblclick(function(){
					var opt = $(this).find('option:selected');
					var popt = $(this).find('option');
					var uopt = $("#userId"+boxid+" option");
					var topt = $("#toUser"+boxid).find('option');
					var uo = $("#userId"+boxid);
					var val = opt.val();
					if(val){
						opt.attr("disabled","disabled");
						opt.addClass('disabled');
						$(this).val("");
						var idd = opt.attr('id');
						if(mod>=3){
							var copt = $("#companyId"+boxid+" option[value="+idd+"]");
						}
						var ph = $("#partId"+boxid).html();
						pid[idd] = ph;
						uopt.each(function(){
							var da = $(this).attr("disabled");
							if(!isset(da)){
								$(this).attr("disabled","disabled");
								$(this).addClass('disabled');
								var sid = $(this).val();
								var stxt = $(this).text();
								var mail = $(this).attr('mail');
								
								var ishas = 0;
								topt.each(function(){
									var nowval = $(this).val();
									if(sid==nowval){
										ishas = 1;
									}
								});
								if(ishas==0){
									if(getOpt('mail',option)){
										$("#toUser"+boxid).append('<OPTION id='+val+' value='+sid+' cid="'+idd+'">'+stxt+' ： '+mail+'</OPTION>');
									}else{
										$("#toUser"+boxid).append('<OPTION id='+val+' value='+sid+' cid="'+idd+'">'+stxt+'</OPTION>');
									}
								}
							}
						});
						var re =new RegExp('id=\\\"?'+val+'\\\"?(\\\s|>)',"ig");
						puh[idd] = puh[idd].replace(re,'id="'+val+'"$1 disabled class=disabled');
						uid[idd] = puh[idd];
						puh[0] = puh[0].replace(re,'id="'+val+'"$1 disabled class=disabled');
						uid[0] = puh[0];
						//alert(puh[0])
						uid[val] =  $("#userId"+boxid).html();;
						//alert(uid[val])
						if(mod>=3){
							var num = 0;
							popt.each(function(){
								var da = $(this).attr("disabled");
								//alert(isset(da));
								if(!isset(da)){
									num++;
								}
							});
							if(num==0){
								copt.attr("disabled","disabled");
								copt.addClass('disabled');
								$("#companyId"+boxid).val("");
							}
						}
					}
				});
			}
			
			var puh = ouid;
			$("#goa"+boxid).click(function(){
				var opts = $("#partId"+boxid);
				var opt = opts.find('option:selected:eq(0)');
				var popt = opts.find('option');
				var uopt = $("#userId"+boxid+" option");
				var topt = $("#toUser"+boxid).find('option');
				var uo = $("#userId"+boxid);
				var val = opt.val();
				if(val){
					opt.attr("disabled","disabled");
					opt.addClass('disabled');
					opts.val("");
					var idd = opt.attr('id');
					if(mod>=3){
						var copt = $("#companyId"+boxid+" option[value="+idd+"]");
					}
					var ph = $("#partId"+boxid).html();
					pid[idd] = ph;
					uopt.each(function(){
						var da = $(this).attr("disabled");
						if(!isset(da)){
							$(this).attr("disabled","disabled");
							$(this).addClass('disabled');
							var sid = $(this).val();
							var stxt = $(this).text();
							var mail = $(this).attr('mail');
							
							var ishas = 0;
							topt.each(function(){
								var nowval = $(this).val();
								if(sid==nowval){
									ishas = 1;
								}
							});
							if(ishas==0){
								if(getOpt('mail',option)){
									$("#toUser"+boxid).append('<OPTION id='+val+' value='+sid+' cid="'+idd+'">'+stxt+' ： '+mail+'</OPTION>');
								}else{
									$("#toUser"+boxid).append('<OPTION id='+val+' value='+sid+' cid="'+idd+'">'+stxt+'</OPTION>');
								}
							}
						}
					});
					var re =new RegExp('id=\\\"?'+val+'\\\"?(\\\s|>)',"ig");
					puh[idd] = puh[idd].replace(re,'id="'+val+'"$1 disabled class=disabled');
					uid[idd] = puh[idd];
					puh[0] = puh[0].replace(re,'id="'+val+'"$1 disabled class=disabled');
					uid[0] = puh[0];
					//alert(puh[idd])
					uid[val] =  $("#userId"+boxid).html();
					if(mod>=3){
						var num = 0;
						popt.each(function(){
							var da = $(this).attr("disabled");
							//alert(isset(da));
							if(!isset(da)){
								num++;
							}
						});
						if(num==0){
							copt.attr("disabled","disabled");
							copt.addClass('disabled');
							$("#companyId"+boxid).val("");
						}
					}
				}
			});
			
			if(mod>=3){
				$("#companyId"+boxid).dblclick(function(){
					var opt = $(this).find('option:selected');
					var uopt = $("#userId"+boxid+" option");
					var popt = $("#partId"+boxid+" option");
					var topt = $("#toUser"+boxid).find('option');
					var val = opt.val();
					if(val && val!=0){
						opt.attr("disabled","disabled");
						opt.addClass('disabled');
						popt.attr("disabled","disabled");
						popt.addClass('disabled');
						$(this).val("");
						var idd = opt.attr('id');
						var ph = $("#partId"+boxid).html();
						pid[idd] = ph;
						uopt.each(function(){
							var da = $(this).attr("disabled");
							if(!isset(da)){
								$(this).attr("disabled","disabled");
								$(this).addClass('disabled');
								var sid = $(this).val();
								var sidd = $(this).attr('id');
								var mail = $(this).attr('mail');
								var stxt = $(this).text();
								var ishas = 0;
								topt.each(function(){
									var nowval = $(this).val();
									if(sid==nowval){
										ishas = 1;
									}
								});
								if(ishas==0){
									if(getOpt('mail',option)){
										$("#toUser"+boxid).append('<OPTION id='+sidd+' value='+sid+' cid='+val+'>'+stxt+' ： '+mail+'</OPTION>');
									}else{
										$("#toUser"+boxid).append('<OPTION id='+sidd+' value='+sid+' cid='+val+'>'+stxt+'</OPTION>');
									}
								}
							}
						});
						var uh = $("#userId"+boxid).html();
						uid[idd] = uh;
						var re =new RegExp('cid\=\\\"'+val+'\\\"(\\\s|>)',"ig");
						uid[0] = uid[0].replace(re,'cid="'+val+'"$1 disabled class=disabled');
						uid[0] = uid[0];
						//alert(uid[0]);
					}
				});
			
				$("#companyId"+boxid).click(function(){
					var opt = $(this).find('option:selected');
					var val = opt.val();
					if(val){
						var da = opt.attr("disabled");
						if(!isset(pid[val])){
							$.post(_act+'/change/act/comy', {id:val},function(data){
								$("#partId"+boxid).html(data);
								pid[val] = data;
							});
						}else{
							$("#partId"+boxid).html(pid[val]);
						}
						if(!isset(uid[val])){
							$.post(_act+'/change/act/comy/mode/1', {id:val},function(data){
								$("#userId"+boxid).html(data);
								uid[val] = data;
								ouid[val] = data;
							});
						}else{
							$("#userId"+boxid).html(uid[val]);
						}
					}
				});
			}
			
			if(mod>=2){
				$("#partId"+boxid).change(function(){
					var opt = $(this).find('option:selected');
					var val = opt.val();
					if(val){
						if(!isset(uid[val])){
							$.post(_act+'/change/act/part', {id:val},function(data){
								$("#userId"+boxid).html(data);
								uid[val] = data;
								ouid[val] = data;
							});
						}else{
							$("#userId"+boxid).html(uid[val]);
						}
					}	
				});
			}
			
			$("#toUser"+boxid).dblclick(function(){
				$.ajaxSetup({  
					async : false  
				});
				var opt = $(this).find('option:selected');
				if(mod>=3){
					var copt = $("#companyId"+boxid);
				}
				if(mod>=2){
					var popt = $("#partId"+boxid);
				}
				var uopt = $("#userId"+boxid);
				var val = opt.val();
				var idd = opt.attr('id');
				var cid = opt.attr('cid');
				uopt.find("option[value="+val+"]").attr("disabled",false);
				uopt.find("option[value="+val+"]").removeClass('disabled');
				opt.remove();
				if(idd!=0){
					if(!isset(uid[idd])){
						$.post(_act+'/change/act/part', {id:idd},function(datas){
							uid[idd] = datas.replace(/\<option/g,'<option disabled class=disabled');
						});
					}
					
					var re =new RegExp('value=\\\"?'+val+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
					uid[idd] = uid[idd].replace(re,'value="'+val+'" $1');
					if(mod>=3){
						uid[cid] = uid[cid].replace(re,'value="'+val+'" $1');
					}
					uid[0] = uid[0].replace(re,'value="'+val+'" $1');
					re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
					uid[idd] = uid[idd].replace(re,'$2value="'+val+'"$3');
					if(mod>=3){
						uid[cid] = uid[cid].replace(re,'$2value="'+val+'"$3');
					}
					uid[0] = uid[0].replace(re,'$2value="'+val+'"$3');
					re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
					uid[idd] = uid[idd].replace(re,'$1 value="'+val+'"$3');
					if(mod>=3){
						uid[cid] = uid[cid].replace(re,'$1 value="'+val+'"$3');
					}
					uid[0] = uid[0].replace(re,'$1 value="'+val+'"$3');
					//alert(uid[idd]);
					ouid[idd] = uid[idd];
					if(mod>=3){
						ouid[cid] = uid[cid];
					}
					ouid[0] = uid[0];
					//alert(ouid[0]);
					if(mod>=2){
						var da = popt.find("option[value="+idd+"]").attr("disabled");
						if(isset(da)){
							popt.find("option[value="+idd+"]").attr("disabled",false);
							popt.find("option[value="+idd+"]").removeClass('disabled');
						}
						
						if(mod>=3){
							re =new RegExp('value=\\\"?'+idd+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
							pid[cid] = pid[cid].replace(re,'value="'+idd+'" $1');
							re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
							pid[cid] = pid[cid].replace(re,'$2value="'+idd+'"$3');
							re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
							pid[cid] = pid[cid].replace(re,'$1 value="'+idd+'"$3');
						}
						//alert(pid[cid]);
					}
					
					if(mod>=3){
						var dac = copt.find("option[value="+cid+"]").attr("disabled");
						if(isset(dac)){
							copt.find("option[value="+cid+"]").attr("disabled",false);
							copt.find("option[value="+cid+"]").removeClass('disabled');
						}
					}
				}
				
				$.ajaxSetup({  
					async : true  
				}); 	
			});
			
			$("#go"+boxid).click(function(){
				$.ajaxSetup({  
					async : false  
				});
				if(mod>=3){
					var copt = $("#companyId"+boxid);
				}
				if(mod>=2){
					var popt = $("#partId"+boxid);
				}
				var uopt = $("#userId"+boxid);
				var opts = uopt.find('option:selected');
				var topt = $("#toUser"+boxid).find('option');
				var uopt =  uopt.find('option');
				opts.each(function(){
					var opt = $(this);
					var val = opt.val();
					if(val){
						var txt = opt.text();
						opt.attr("disabled","disabled");
						opt.addClass('disabled');
						$(this).attr("selected",false);
						
						var idd = opt.attr('id');
						var mail = opt.attr('mail');
						var popt = $("#partId"+boxid+" option[value="+idd+"]");
						var cid = opt.attr('cid');
						var re = new RegExp('value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
						if(!isset(uid[idd])){
							$.post(_act+'/change/act/part', {id:idd},function(datas){
								uid[idd] = datas.replace(re,'value="'+val+'"$1 disabled class=disabled');
							});
						}else{
							uid[idd] = uid[idd].replace(re,'value="'+val+'"$1 disabled class=disabled');
						}
						uuh[0] = uuh[0].replace(re,'value="'+val+'"$1 disabled class=disabled');
						uid[0] = uuh[0];
						if(mod>=3){
							uuh[cid] = uuh[cid].replace(re,'value="'+val+'"$1 disabled class=disabled');
							uid[cid] = uuh[cid];
						}
						
						var ishas = 0;
						topt.each(function(){
							var nowval = $(this).val();
							if(val==nowval){
								ishas = 1;
							}
						});
						if(ishas==0){
							if(getOpt('mail',option)){
								$("#toUser"+boxid).append('<OPTION id='+idd+' value='+val+' cid="'+cid+'">'+txt+' ： '+mail+'</OPTION>');
							}else{
								$("#toUser"+boxid).append('<OPTION id='+idd+' value='+val+' cid="'+cid+'">'+txt+'</OPTION>');
							}
						}
						if(mod>=2){
							var num = 0;
							uopt.each(function(){
								var da = $(this).attr("disabled");
								//alert(isset(da));
								if(!isset(da)){
									num++;
								}
							});
							
							if(num==0){
								popt.attr("disabled","disabled");
								popt.addClass('disabled');
							}
						}
					}
				});
				$.ajaxSetup({  
					async : true  
				});
			});
			
			$("#back"+boxid).click(function(){
				$.ajaxSetup({  
					async : false  
				});
				if(mod>=3){
					var copt = $("#companyId"+boxid);
				}
				if(mod>=2){
					var popt = $("#partId"+boxid);
				}
				var uopt = $("#userId"+boxid);
				var topt = $("#toUser"+boxid);
				var opts = topt.find('option:selected');
				opts.each(function(){
					var opt = $(this);
					var val = opt.val();
					//alert(val);
					var idd = opt.attr('id');
					if(mod>=3){
						var cid = opt.attr('cid');
					}
					uopt.find("option[value="+val+"]").attr("disabled",false);
					uopt.find("option[value="+val+"]").removeClass('disabled');
					opt.remove();
					if(idd!=0){
						if(!isset(uid[idd])){
							$.post(_act+'/change/act/part', {id:idd},function(datas){
								uid[idd] = datas.replace(/\<option/g,'<option disabled class=disabled');
							});
						}
						
						var re =new RegExp('value=\\\"?'+val+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
						uid[idd] = uid[idd].replace(re,'value="'+val+'" $1');
						if(mod>=3){
							uid[cid] = uid[cid].replace(re,'value="'+val+'" $1');
						}
						uid[0] = uid[0].replace(re,'value="'+val+'" $1');
						re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
						uid[idd] = uid[idd].replace(re,'$2value="'+val+'"$3');
						if(mod>=3){	
							uid[cid] = uid[cid].replace(re,'$2value="'+val+'"$3');
						}
						uid[0] = uid[0].replace(re,'$2value="'+val+'"$3');
						re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
						uid[idd] = uid[idd].replace(re,'$1 value="'+val+'"$3');
						
						if(mod>=3){
							uid[cid] = uid[cid].replace(re,'$1 value="'+val+'"$3');
						}
						uid[0] = uid[0].replace(re,'$1 value="'+val+'"$3');
						//alert(uid[idd]);
						ouid[idd] = uid[idd];
						if(mod>=3){
							ouid[cid] = uid[cid];
						}
						ouid[0] = uid[0];
						//alert(ouid[0]);
						if(mod>=2){
							var da = popt.find("option[value="+idd+"]").attr("disabled");
							if(isset(da)){
								popt.find("option[value="+idd+"]").attr("disabled",false);
								popt.find("option[value="+idd+"]").removeClass('disabled');
							}
							
							if(mod>=3){
								re =new RegExp('value=\\\"?'+idd+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
							
								pid[cid] = pid[cid].replace(re,'value="'+idd+'" $1');
								re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
								pid[cid] = pid[cid].replace(re,'$2value="'+idd+'"$3');
								re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
								pid[cid] = pid[cid].replace(re,'$1 value="'+idd+'"$3');
							}
							//alert(pid[cid]);
						}
						
						if(mod>=3){
							var dac = copt.find("option[value="+cid+"]").attr("disabled");
							if(isset(dac)){
								copt.find("option[value="+cid+"]").attr("disabled",false);
								copt.find("option[value="+cid+"]").removeClass('disabled');
							}
						}
					}
				});
				$.ajaxSetup({  
					async : true  
				});
			});
			
			$("#backa"+boxid).click(function(){
				$.ajaxSetup({  
					async : false  
				});
				if(mod>=3){
					var copt = $("#companyId"+boxid);
				}
				if(mod>=2){
					var popt = $("#partId"+boxid);

				}
				var uopt = $("#userId"+boxid);
				var topt = $("#toUser"+boxid);
				var opts = topt.find('option');
				opts.each(function(){
					var opt = $(this);
					var val = opt.val();
					//alert(val);
					var idd = opt.attr('id');
					var cid = opt.attr('cid');
					uopt.find("option[value="+val+"]").attr("disabled",false);
					uopt.find("option[value="+val+"]").removeClass('disabled');
					opt.remove();
					if(idd!=0){
						if(!isset(uid[idd])){
							$.post(_act+'/change/act/part', {id:idd},function(datas){
								uid[idd] = datas.replace(/\<option/g,'<option disabled class=disabled');
							});
						}
						
						var re =new RegExp('value=\\\"?'+val+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
						uid[idd] = uid[idd].replace(re,'value="'+val+'" $1');
						if(mod>=3){
							uid[cid] = uid[cid].replace(re,'value="'+val+'" $1');
						}
						uid[0] = uid[0].replace(re,'value="'+val+'" $1');
						re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
						uid[idd] = uid[idd].replace(re,'$2value="'+val+'"$3');
						if(mod>=3){
							uid[cid] = uid[cid].replace(re,'$2value="'+val+'"$3');
						}
						uid[0] = uid[0].replace(re,'$2value="'+val+'"$3');
						re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+val+'\\\"?(\\\s|>)',"i");
						uid[idd] = uid[idd].replace(re,'$1 value="'+val+'"$3');
						if(mod>=3){
							uid[cid] = uid[cid].replace(re,'$1 value="'+val+'"$3');
						}
						uid[0] = uid[0].replace(re,'$1 value="'+val+'"$3');
						//alert(uid[idd]);
						ouid[idd] = uid[idd];
						if(mod>=3){
							ouid[cid] = uid[cid];
						}
						ouid[0] = uid[0];
						//alert(ouid[0]);
						if(mod>=2){
							var da = popt.find("option[value="+idd+"]").attr("disabled");
							if(isset(da)){
								popt.find("option[value="+idd+"]").attr("disabled",false);
								popt.find("option[value="+idd+"]").removeClass('disabled');
							}
							
							if(mod>=3){
								re =new RegExp('value=\\\"?'+idd+'\\\"?\\\s+([^<]*)?(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)',"i");
								pid[cid] = pid[cid].replace(re,'value="'+idd+'" $1');
								re =new RegExp('(disabled\\\s+class=\\\"?disabled\\\"?|disabled=\\\"?disabled\\\"?\\\s+class=\\\"?disabled\\\"?|class=\\\"?disabled\\\"?\\\s+disabled[^=]|class=\\\"?disabled\\\"?\\\s+disabled=\\\"?disabled\\\"?)([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
								pid[cid] = pid[cid].replace(re,'$2value="'+idd+'"$3');
								re =new RegExp('disabled\\\s+([^<]+)?\\\s+class=\\\"?disabled\\\"?([^<]*)?value=\\\"?'+idd+'\\\"?(\\\s|>)',"i");
								pid[cid] = pid[cid].replace(re,'$1 value="'+idd+'"$3');
							}
							//alert(pid[cid]);
						}
						
						if(mod>=3){
							var dac = copt.find("option[value="+cid+"]").attr("disabled");
							if(isset(dac)){
								copt.find("option[value="+cid+"]").attr("disabled",false);
								copt.find("option[value="+cid+"]").removeClass('disabled');
							}
						}
					}
				});
				$.ajaxSetup({  
					async : true  
				});
			});
			
			/*
			$("#see"+boxid).keydown(function(event){
				var w = event.which;
				//alert(w);
				if(w>=65 && w<=90){
					var far = $("#userId"+boxid);
					var opt = far.find('option');
					var q = keyValue(w,1300);
					$("#userBox"+boxid).val(q);
					far.val("");
					opt.each(function(){
						var v = $(this).text();
						var vp = ','+String(getPinYin(v));
						var qp = ','+q;
						if(vp.indexOf(qp)>=0 || v.indexOf(q) == 0){
							var fp = $(this).index();
							$(this).attr("selected",true);
							var fh = far.height();
							user.scrollTop(fp*fh);
							//alert(user.scrollTop()
							return false;
						}
					});
				}
			});
			*/
			
			$("#userBox"+boxid).keyup(function(){
				var far = $(this);
				var user = $("#userId"+boxid);
				var opt = user.find('option');
				var q = $("#userBox"+boxid).val();
				q = q.toUpperCase();
				user.val("");
				opt.each(function(){
					var v = $(this).text();
					var vp = ','+String(getPinYin(v));
					var qp = ','+q;
					if(vp.indexOf(qp)>=0 || v.indexOf(q) == 0){
						var fp = $(this).index();
						$(this).attr("selected",true);
						var fh = far.height();
						user.scrollTop(fp*fh);
						//alert(user.scrollTop()
						return false;
					}
				});
			});
		});	
	}
}

function preventSelectDisabled(oSelect){
	var isOptionDisabled = oSelect.options[oSelect.selectedIndex].disabled;    
	if(isOptionDisabled){       
		oSelect.selectedIndex = oSelect.defaultSelectedIndex;        
		return false;    
	}else{
		oSelect.defaultSelectedIndex = oSelect.selectedIndex;
		return true;
	}
}