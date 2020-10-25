

function queryByPage(p){
	$('#loading').show();
	var params = {};
	params = WST.getParams('.s-query');
	params.page = p;
	$.post(WST.U('home/shopusers/pageQuery'),params,function(data,textStatus){
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
	location.href = WST.U('home/shopusers/edit','id='+id);
}

/**保存角色数据**/
function add(){
	$('#editForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			if(WST.conf.IS_CRYPT=='1'){
	            var public_key=$('#token').val();
	            var exponent="10001";
	       	    var rsa = new RSAKey();
	            rsa.setPublic(public_key, exponent);
	            params.loginPwd = rsa.encrypt(params.loginPwd);
	            params.reUserPwd = rsa.encrypt(params.reUserPwd);
	        }
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('home/shopusers/toAdd'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1},function(){
						location.href=WST.U('home/shopusers/index');
					});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}

function edit(){
	$('#editForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			if(WST.conf.IS_CRYPT=='1' && params.newPass!=""){
	            var public_key=$('#token').val();
	            var exponent="10001";
	       	    var rsa = new RSAKey();
	            rsa.setPublic(public_key, exponent);
	            params.oldPass = rsa.encrypt(params.oldPass);
	            params.newPass = rsa.encrypt(params.newPass);
	            params.reNewPass = rsa.encrypt(params.reNewPass);
	        }
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('home/shopusers/toEdit'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1},function(){
						location.href=WST.U('home/shopusers/index');
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
	var c = WST.confirm({content:'删除店铺管理帐号，只是删除该帐号与店铺的关系，您确定要删除吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('home/shopusers/del'),{id:id},function(data,textStatus){
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