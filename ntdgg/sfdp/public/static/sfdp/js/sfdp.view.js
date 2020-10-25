/*!
 * SFDP 表单设计器--view方法构建器
 * http://cojz8.com
 *
 * 
 * Released under the MIT license
 * http://cojz8.com
 *
 * Date: 2020年3月4日23:34:39
 */
	function showview(int_data){
		console.log(int_data);
		if(int_data==null){
			layer.msg('对不起，没有任何数据~');
		}else{
			$('#table').html('<table id="table_view"><tbody><tr class="table_tr"><td>'+int_data.name+'</td></tr></tbody></table>');
			 for (x in int_data.list){
				var table = table_build(int_data.list[x]['type'],int_data.list[x],'show');//恢复表单布局设计
				var $targetTbody= $("#table_view tbody");
				var $tr = $targetTbody.children("tr[class='table_tr']:last");
				$tr.after(table);
			 } 
			$(document).attr("title",int_data.name);//修改页面标题
		}
		
	}
	function showadd(int_data,Debug=false){
		if(int_data==null){
			layer.msg('对不起，没有任何数据~');
		}else{
			if(Debug==true){
				var btn ='';
				}else{
				var btn = '<a  class="btn" type="submit">&nbsp;&nbsp;保存&nbsp;&nbsp;</a><a class="btn" type="button" onclick=commonfun.layer_close()>&nbsp;&nbsp;取消&nbsp;&nbsp;</a>';
			}
			$('#table').html('<form action="" method="post" name="form" id="form"><input type="hidden" readonly name="name_db" value="'+int_data.name_db+'"><table id="table_view"><tbody><tr class="table_tr"><th  colspan="4">'+int_data.name+'</th></tr> <tr><td style="text-align: center;" colspan=4> '+btn+' </td></tr></tbody></table></form>');
			 for (x in int_data.list){
				var table = table_build(int_data.list[x]['type'],int_data.list[x],'add');//恢复表单布局设计
				var $targetTbody= $("#table_view tbody");
				var $tr = $targetTbody.children("tr[class='table_tr']:last");
				$tr.after(table);
			 } 
			$(document).attr("title",int_data.name);//修改页面标题
		}
		commonfun.setDate();//初始化日期选择器
	}
	function showlist(int_data,Debug=false){
		var td_data = int_data;
			var html ='';
			for (x in td_data){
				var type = td_data[x]['td_type'];
				 html += '   '+$.view_field_return(type,td_data[x]);
			}
			html += '   <button  class="btn btn-success" type="submit"><i class="Hui-iconfont">&#xe665;</i> 查询</button>';
		$('#search').html(html);
	}
	//表单构建
	function table_build(id,old_data='',types){
		var code = old_data['tr'];
		var td_data = old_data.data;
			var json = {1:'',2:'',3:'',4:''};
			for (x in td_data){
				var type = td_data[x]['td_type'];
				if(types=='add'){
					var html =$.view_field_return(type,td_data[x]);
				}else{
					var html =$.show_field_return(type,td_data[x]);
				}
				json[td_data[x]['td']] = html;
			}
		switch(id) {
			case 1:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-1 code_td" colspan="4">'+json[1]+'</td></tr>';
				break;
			case 2:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-2" colspan="2">'+json[1]+'</td><td id="2" colspan="2"class="fb-fz code_td" >'+json[2]+'</td></tr>';
				break;
			case 3:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4">'+json[1]+'</td><td id="2" class="fb-fz x-4">'+json[2]+'</td><td id="3" colspan="2"class="fb-fz  x-2 code_td" >'+json[3]+'</td></tr>';
				break;
			case 4:
				var html ='<tr class="table_tr" id='+code+'><td id="1" class="fb-fz x-4">'+json[1]+'</td><td id="2" class="fb-fz x-4">'+json[2]+'</td><td id="3" class="fb-fz x-4">'+json[3]+'</td><td id="4" class="fb-fz x-4 code_td">'+json[4]+'</td></tr>';
				break;
			 default:
				var html ='';
		} 
		return html;
	}

	