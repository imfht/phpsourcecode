

function queryByPage(p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.s-query');
	params.page = p;
	$.post(WST.U('home/shoproles/pageQuery'),params,function(data,textStatus){
		$('#loading').hide();
	    var json = WST.toJson(data);
	    $('#list').empty();
       	var gettpl = document.getElementById('tblist').innerHTML;
       	laytpl(gettpl).render(json.data, function(html){
       		$('#list').html(html);
       	});
       	laypage({
	        cont: 'pager', 
	        pages:json.last_page,
	        curr: json.current_page,
	        skin: '#e23e3d',
	        groups: 3,
	        jump: function(e, first){
	        	if(!first){
	        		queryByPage(e.curr);
	        	}
	        } 
		});
       
	});
}

function toEdit(id){
	location.href = WST.U('home/shoproles/edit','id='+id);
}

/**保存角色数据**/
function save(){
	$('#shoprole').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('home/shoproles/'+((params.id==0)?"toAdd":"toEdit")),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1},function(){
						location.href=WST.U('home/shoproles/index');
					});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
//删除角色
function del(id){
	var c = WST.confirm({content:'您确定要删除该角色吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('home/shoproles/del'),{id:id},function(data,textStatus){
			layer.close(load);
		    var json = WST.toJson(data);
		    if(json.status==1){
		    	WST.msg(json.msg,{icon:1});
		    	queryByPage(0);
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}