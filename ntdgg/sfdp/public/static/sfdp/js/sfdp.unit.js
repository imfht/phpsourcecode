/*!
 * SFDP 表单设计器--组件构建器
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
$(function(){
    $.extend({
		tpfd_tableui:function(code,type){
			switch(type) {
			case 1:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-1 code_td" colspan="4"><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 2:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-2" colspan="2"></td><td id="2" colspan="2"class="fb-fz code_td" ><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 3:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4"></td><td id="2" class="fb-fz x-4"></td><td id="3" colspan="2"class="fb-fz  x-2 code_td" ><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			case 4:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4"></td><td id="2" class="fb-fz x-4"></td><td id="3" class="fb-fz x-4"></td><td id="4" class="fb-fz x-4 code_td"><span class="code">'+code+'</span><span class="code2">x</span></td></tr>';
				break;
			 default:
				var html ='';
			} 
			return html;
		},
		tpfd_change:function(labid,type){
			switch(type) {
			case 'text':
				var html ='<label '+labid+'>文本控件：</label><input  type="text"  placeholder="请输入信息~" disabled>';
				break;
			case 'upload':    
				var html ='<label '+labid+'>上传控件：</label>上传';
				break;
			case 'checkboxes':
				var html ='<label '+labid+')>多选控件：</label>选项1<input type="checkbox"  placeholder="" disabled> 选项2<input type="checkbox"  placeholder="" disabled>';
				break;
			case 'radio':
				var html ='<label '+labid+')>单选控件：</label>选项1<input type="radio"  placeholder="" disabled> 选项2<input type="radio"  placeholder="" disabled>';
				break;
			case 'date':
				var html ='<label '+labid+')>时间日期：</label><input type="text"  placeholder="" disabled >';
				break;
			case 'dropdown':
				var html ='<label '+labid+'>下拉选择：</label><select disabled><option value ="请选择">请选择</option></select>';
				break;
			case 'textarea':
				var html ='<label '+labid+'>多行控件：</label><textarea  disabled ></textarea>';
				break;
			case 'html':
				var html ='<label '+labid+'>HTML控件：</label><b style="color: blue;">Look this is a HTML</b>';
				break;
			case 'wenzi':
				var html ='<label '+labid+'>文字控件：</label>默认现实的文本';
				break;
			 default:
				var html ='';
			}
			return html;
			
		},
		tpfd_return:function(type,data){
			switch(type) {
				case 'text':
					var html = $.tpfd_common(data)+$.tpfd_moren(data)+$.tpfd_gaoji(data);
					break;
				case 'checkboxes':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data)+$.tpfd_gaoji(data);
					break;
				case 'radio':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data,'radio')+$.tpfd_gaoji(data);
					break;
				case 'date':
					var html = $.tpfd_common(data)+$.tpfd_date(data)+$.tpfd_gaoji(data);
					break;
				case 'dropdown':
					var html = $.tpfd_common(data)+$.tpfd_checkboxes(data,'radio')+$.tpfd_gaoji(data);
					break;
				case 'textarea':
					var html = $.tpfd_common(data)+$.tpfd_moren(data)+$.tpfd_gaoji(data);
					break;
				case 'html':
					var html = $.tpfd_common(data)+$.tpfd_xianshi(data);
					break;
				case 'wenzi':
					var html = $.tpfd_common(data)+$.tpfd_xianshi(data);
					break;
				case 'upload':    
					data.tpfd_list = 'no';
					data.tpfd_chaxun = 'no';
					data.tpfd_show = 'no';
					var html =$.tpfd_common(data)+$.tpfd_upload(data)+$.tpfd_gaoji(data);
					break;
				 default:
					var html ='';
			}
			return html;
        },
		tpfd_upload:function(data){
			if(data.tpfd_upload_type=='undefined'){
				var tpfd_upload_type = 0;
				var tpfd_upload_xz = 0;
			}else{
				var tpfd_upload_type = data.tpfd_upload_type;
				var tpfd_upload_xz = data.tpfd_upload_xz;
			}
			var default_data =[{cid:0,clab:'单文件上传'},{cid:1,clab:'多文件上传'}];
			var word_type =[{cid:0,clab:'不限制'},{cid:1,clab:'*.jpg/*.png/*.gif'},{cid:1,clab:'*.doc/*.txt/*.xlx/*.xlxs/*.docx'}];
			
			return '<div style="font-size: 16px;font-weight: 800;">上传配置</div><div>上传属性：'+$.tpfd_select(default_data,'tpfd_upload_type',tpfd_upload_type)+'</div>'+
				   '<div>文件类型：'+$.tpfd_select(word_type,'tpfd_upload_xz',tpfd_upload_xz)+'</div>'; 
			
		},
		tpfd_common:function(data){
			var default_field = [{cid:'int',clab:'int',checked:''},{cid:'varchar',clab:'varchar',checked:'checked'},{cid:'datetime',clab:'datetime',checked:''},{cid:'longtext',clab:'longtext',checked:''}];
			return '<div><input name="tpfd_id" type="hidden" value="'+data.tpfd_id +'"><input name="tr_id" type="hidden" value="'+data.tr_id +'"><div>数据表段：<input style="width:60px" name="tpfd_db" type="text" value="'+data.tpfd_db +'">长度<input style="width:40px" name="tpfd_dbcd" type="text" value="'+data.tpfd_dbcd +'">类型:'+$.tpfd_select(default_field,'tpfd_dblx','varchar')+'  字段标题：<input style="width:120px" name="tpfd_name" type="text"  value="'+data.tpfd_name +'"></div>'+$.tpfd_list(data);
        },
        tpfd_xianshi:function(data){
			return '<div>显示类型：<textarea name="tpfd_moren">'+data.tpfd_moren +'</textarea>';
        },
        tpfd_date:function(data){
			var default_data =[{cid:0,clab:'yyyy'},{cid:1,clab:'MM-dd'},{cid:2,clab:'yyyy-MM-dd'},{cid:3,clab:'yyyyMMdd'},{cid:4,clab:'yyyy-MM'}];
			return '<div>显示类型：'+$.tpfd_select(default_data,'xx_type','2');
        },
		tpfd_checkboxes:function(data,type='checkbox'){
			if(data.tpfd_data==undefined){
				var default_data =[{cid:0,clab:'选项1',checked:''},{cid:1,clab:'选项2',checked:'checked'}];
			}else{
				var datas = [];
				for (x in data.tpfd_data){
					if(data.tpfd_check != undefined && isInArray(data.tpfd_check,x)){
						var check='checked';
					}else{
						var check='';
					}
					datas[x] = { cid:x,clab:data.tpfd_data[x],checked:check};
				}
				var default_data =JSON.parse(JSON.stringify(datas));
			}
            return '<div><input '+((data.xx_type) == '0' ? 'checked' : '') +'  name="xx_type" value=0 type="radio">静态数据：</div>'+$.tpfd_checkboxes_clss(default_data,type)+
			'<div><input '+((data.xx_type) == '1' ? 'checked' : '') +' name="xx_type" value=1 type="radio">动态数据：<br/><input name="checkboxes_func" type="text" value="'+((data.checkboxes_func) == '' ? '' : data.checkboxes_func)+'"></div>';
        },
		tpfd_gaoji:function(data){
			var default_data =[{cid:0,clab:'是'},{cid:1,clab:'否'}];
			if(data.tpfd_read=='undefined'){
				var tpfd_read = 0;
				var tpfd_must = 0;
			}else{
				var tpfd_read = data.tpfd_read;
				var tpfd_must = data.tpfd_must;
			}
			return '<div style="font-size: 16px;font-weight: 800;">高级设置</div><div>只读：'+$.tpfd_select(default_data,'tpfd_read','1')+'必填：'+$.tpfd_select(default_data,'tpfd_must',tpfd_must)+'</div>'; 
		},
		tpfd_moren:function(data){
			return '<div>占位内容：<input type="text" name="tpfd_zanwei" value="'+data.tpfd_zanwei +'">  设置默认：<input name="tpfd_moren" type="text" value="'+data.tpfd_moren+'"></div>';
        },
		tpfd_list:function(data){
			console.log(data);
			return '<div>列表设置：'+$.tpfd_select('','tpfd_list',data.tpfd_list)+'  查询设置：'+$.tpfd_select('','tpfd_chaxun',data.tpfd_chaxun)+'</div>';
        },
		tpfd_select:function(data,field,value){
			if(data==''){
				return '<select name="'+field+'" style="width: 80px"><option value="yes" '+((value) == 'yes' ? 'selected' : '') +'>是</option><option value="no" '+((value) == 'no' ? 'selected' : '') +'>否</option></select>';
			}else{
				var html ='<select name="'+field+'" style="width: 80px">';
				for (x in data){
					
					html += '<option value="'+data[x]['cid']+'" '+((data[x]['cid']) == value ? 'selected' : '') +'>'+data[x]['clab']+'</option>';
				}
				return html+'</select>';
			}
        },
		tpfd_checkboxes_clss:function(data,type='checkbox'){
			var html ='';
			for (x in data){
				if(x == data.length-1){
					var btn ='<span onclick=commonfun.addoption('+x+',"'+type+'")>Add</span>';
				}else{
					var btn ='<span onclick=commonfun.editoption('+x+')>Del</span>';
				}
				html += '<div id="checkboxes'+x+'"><input '+data[x]['checked']+' name="tpfd_check" value='+x+' type="'+type+'"><input name="tpfd_data" type="text" value="'+data[x]['clab']+'">'+btn+'</div>';
			}
			return html;
		}
		
    })
	function isInArray(arr,value){
		for(var i = 0; i < arr.length; i++){
		if(value === arr[i]){
		return true;
		}
		}
		return false;
	}
})