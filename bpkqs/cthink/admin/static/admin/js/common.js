$(function(){
	/**
	 * 全局绑定表单继续ajax提交和验证
	 */
	var valid = $("#f").Validform({
		tiptype:3,
		label:".label",
		showAllError:true,
		ajaxPost:true,
		callback:function(data){
			$('#Validform_msg').hide();
			if(data.code == 1){
				if (data.url) {
					cthink.toast(data.msg + ', ' + data.wait+'秒后页面将自动跳转~',data.wait,'success');
					setTimeout(function(){
						location.href = data.url;
					},3000);
				}else{
					cthink.toast(data.msg,data.wait,'error');
				}
			}else{
				cthink.toast(data.msg,data.wait,'error');
			}
		}
	});
	
	/**
	 * 动作栏点击编辑、删除等操作的全局处理
	 */
	$('.ajax-post').click(function(){
		var url = $(this).attr('url');
		var target = $(this).attr('target-form');
		var checkval = '';
		$('tbody').find('.ids').each(function(i,o){
			if($(this).is(':checked')) {
				checkval += checkval?','+$(this).val():$(this).val();
			}
		});
		if(checkval || $(this).hasClass('tabledel')){
			if($(this).hasClass('confirm')){
				layer.confirm('你确认要这么处理吗？', {
				  btn: ['确定','取消']
				},
				function(index){
					layer.close(index);
					var loading = layer.load(1, {
						shade: [0.5,'#fff']
					});
					$.post(url,target+'='+checkval).success(function(data){
						if(data.code == 1){
							cthink.toast(data.msg,1,'success');
							layer.close(loading);
							setTimeout(function(){
								location.reload();
							},1000);
						}else{
							cthink.toast(data.msg,1,'error');
							layer.close(loading);
						}
					});
				});
			}else{
				var l = checkval.split(',');
				location.href = url+'?'+target+'='+l[0];
			}
		}
	});
	
	/**
	 * 全局返回按钮事件
	 */
	$('.historygo').click(function(){
		javascript:history.go(-1);
	});
	
	/**
	 * 全局checkbox效果
	 */
	$(".i-checks").iCheck({
		checkboxClass:"icheckbox_square-green",
		radioClass:"iradio_square-green"
	});
	
	/**
	 * 实现全选和反选的效果
	 */
	$(".check-all").on('ifClicked',function(event){
		$('.ids').iCheck('check');
	});
	$(".check-all").on('ifUnchecked',function(event){
		$('.ids').iCheck('uncheck');
	});
	$('.ids').on('ifUnchecked', function(event){
		var option = $(".ids");
		option.each(function(i){
			var checked = $('.ids').eq(i).attr("checked",true);
			if(checked){
				$('.ids').eq(i).iCheck('uncheck');
				return false;
			}else{
				$('.ids').eq(i).iCheck('check');
			}
		});
	});
	
	/**
	 * 实现搜索块的隐藏和显示
	 */
	$('.search-toggle').click(function(){
		var display = $('.search-area').css('display');
		if(display == 'none'){
			$('.search-area').show(100);
			$(this).html('收起');
		}else{
			$('.search-area').hide(100);
			$(this).html('搜索');
		}
	});
	
});

/**
 * cthink js函数库
 */
var cthink = {
	/**
	 * 弹出消息，成功失败返回的结果在顶部居中显示
	 */
	toast:function(msg,t,type){
		toastr.options = {
		  "closeButton": true,
		  "debug": false,
		  "progressBar": true,
		  "positionClass": "toast-top-center",
		  "onclick": null,
		  "showDuration": "400",
		  "hideDuration": "1000",
		  "timeOut": t * 1000,
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut"
		}
		toastr[type](msg);
	},
	/**
	 * 分类排序（同时可以应用到其他位置）
	 */
	editsorts:function(th){
		var sort = $(th).val();
		var id = $(th).attr('data');
		var url = $(th).attr('data-url');
		$.post(url,'id='+id+'&sort='+sort).success(function(data){
			var color = '#71b83d';
			if(data.code != 1){
				color = 'red';
			}
			$('.message_'+id).html('<span style="color:'+color+'">&nbsp;&nbsp;'+data.msg+'</span>');
			setTimeout(function(){
				$('.message_'+id).html('');
			},2000);
		});
	}
}