var laytpl = layui.laytpl;
$(function (){ 
	listQuery('home',1);
});
function listQuery(styleSys,p,obj){
    var styleCat = '';
    if(obj){
        styleCat = $(obj).val();
    }
	var loading = WST.msg('正在获取数据，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/shopstyles/listQueryBySys'),{styleSys:styleSys,p:p,styleCat:styleCat},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
        var pager  =  json.data.list;
		if(json.status=='1'){
			var gettpl = document.getElementById('tblist').innerHTML;
	       	laytpl(gettpl).render(json.data, function(html){
	       		$('#style_'+styleSys).html(html);
                $('.gImg').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.RESOURCE_PATH+'/'+WST.conf.MALL_LOGO});
	       	});
            if(pager.last_page>1) {
                layui.use(['laypage'], function () {
                    var laypage = layui.laypage;
                    laypage.render({
                        elem: 'pager',
                        count:pager.total,
                        curr:pager.current_page,
                        jump: function (e, first) {
                            if (!first) {
                                listQuery(styleSys,e.curr,obj);
                            }
                        }
                    })
                });
            }
	       	$('.btn').click(function(){
                changeStyle($(this),$(this).attr('dataid'));
            });
		}
	});
}

function changeStyle(obj,id){
    var isShow = $(obj).val();
    var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
    $.post(WST.U('admin/shopstyles/changeStyleShow'),{id:id,isShow:isShow},function(data,textStatus){
        layer.close(loading);
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            WST.msg(json.msg,{icon:1});
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}

function changeStyleCat(obj,id){
    var styleCat = $(obj).val();
    $.post(WST.U('admin/shopstyles/changeStyleCat'),{id:id,styleCat:styleCat},function(data,textStatus){
        var json = WST.toAdminJson(data);
        if(json.status=='1'){
            WST.msg(json.msg,{icon:1});
        }else{
            WST.msg(json.msg,{icon:2});
        }
    });
}
