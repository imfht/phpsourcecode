$(document).ready(function(){
	//激活提示工具
	$(function () { $("[data-toggle='tooltip']").tooltip(); });
	//翻页
	$("input[name='tpage']").click(function(){
		var maxpage;
		switch(this.value){
			//判断页码
			case "首页":
				$('#page').val('1');
			break;
			case "上页":
				if($('#page').val()!==1){
					$('#page').val($('#page').val()-1);
				}
			break;
			case "下页":
				maxpage=$('#maxpage').val();
				if($('#page').val()!==maxpage){
					$('#page').val(parseInt($('#page').val())+1);
				}
			break;
			
			case "末页":
				maxpage=$('#maxpage').val();
				$('#page').val(maxpage);
			break;
			
			case "跳转":
				$('#page').val($("#textpage").val());
			break;
			
			//直接点击页码
			default:
				$('#page').val(this.value);
		}
	});
	//筛选时间
	$("input[name='ttime']").click(function(){
		switch(this.value){
			case '全部消息':
				$("input[name='time']").val(0);
			break;
			
			case '今天':
				$("input[name='time']").val(1);
			break;
			
			case '昨天':
				$("input[name='time']").val(2);
			break;
			
			case '前天':
				$("input[name='time']").val(3);
			break;
			
			case '更早':
				$("input[name='time']").val(4);
			break;
		}
	});
	
	//搜索
//	$("#doSearch").click(function(){
////		//判断输入是否为空
////		$("input[name='search']").val($("input[name='setsearch']").val());
////		if($("input[name='setsearch']").val()===''){
////			return confirm('搜索内容为空，确定要如此搜索？');
////		}
//	});
	
	//切换菜单选项卡
	$(".tabs").click(function(){
		$(".tabs").css('background-color','#fff');
		$(".tab").css('display','none');
		var view=$(this).attr('tab');
		$('#rtable_'+view).css("display","");
		$(this).css('background-color','#9BD3EC');
	});
	
	//切换模式——‘是否只看默认公众号’
	$("#def").click(function(){
		$("#list_form").submit();
	});
	
	//切换模式——‘是否隐藏关键词回复’
	$("#is_hide_keyword").click(function(){
		$("#list_form").submit();
	});
	
	
});