function queryByPage(p){
	$('#list').html('<img src="'+WST.conf.ROOT+'/wstmart/home/view/default/img/loading.gif">正在加载数据...');
	var params = {};
	params = WST.getParams('.s-query');
	params.page = p;
	$.post(WST.U('home/goodsconsult/pageQuery'),params,function(data,textStatus){
	    var json = WST.toJson(data);
	    $('#list').empty();
	    if(json.status==1){
	    	json = json.data;
	       	var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#list').html(html);
	       		$('.gImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+window.conf.GOODS_LOGO});
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
       	}  
	});
}

function reply(t,id){
 var params = {};
 if($('#reply-'+id).val()==''){
    WST.msg('回复内容不能为空',{icon:2});
    return false;
 }
 params.reply = $('#reply-'+id).val();
 params.id=id;
 $.post(WST.U('home/goodsconsult/reply'),params,function(data){
    var json = WST.toJson(data);
    if(json.status==1){
      var today = new Date();
      var Myd = today.toLocaleDateString();
      var His = today.toLocaleTimeString();
      var html = '<p class="reply-content">'+params.reply+'【'+Myd+'  '+His+'】</p>'
      $(t).parent().html(html);
    }
 });
}

function editConsult(isShow,id){
	if(id < 0){
	   var ids = WST.getChks('.chk');
	}else{
	   var ids = [id];
	}
	if(ids==''){
		WST.msg('请先选择!', {icon: 5});
		return;
	}
	var params = {};
	params.id = ids;
	params.isShow = parseInt(isShow);
	$.post(WST.U('home/goodsConsult/edit'),params,function(data){
          var json = WST.toJson(data);
          if(json.status==1){
           WST.msg('设置成功!', {icon: 1});
           queryByPage(0);
          }
	})
}