//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.qqface = {

	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var str = '';
		var url = typeof(api)=='object'?web_url:'';
		for(var i=1;i<23;i++){
			str += `<em data-id="${i}"><img src="${url}/public/static/images/qqface/${i}.gif"></em>`;
		}
		if(in_pc==true){
			pc_show(str);
		}else{
			wap_show(str);
		}

		function wap_show(str){
			router.$("#choose_qqface").on("click",function () {
				router.$(".chat_mod_btn").hide();
				if(router.$(".face_wrap").is(":hidden")){
					router.$(".face_wrap").show();
				}else{
					router.$(".face_wrap").hide();
				}			
			});
			str += `<style type="text/css">
					.face_wrap{
						background:#fff;
						padding:5px;
					}
					.face_wrap em{						
						padding:.2rem;
					}
					.face_wrap em img{
						opacity:0.7;
						width:.5rem;
					}
					.face_wrap .ck img{
						opacity:1;
						border:1px solid red;
					}
					</style>`;
			$(".face_wrap").html(str);
			$(".face_wrap em").click(function(){
				router.$(".face_wrap em").removeClass('ck');
				$(this).addClass('ck');
				router.$(".chatInput").val( router.$(".chatInput").val() + '[face' + $(this).data('id') + ']' );
				router.$("#btnSend").removeClass("disabled");
				router.$("#btnSend").addClass("primary");
			});
		}

		function pc_show(str){
			$("#hack_wrap").html(str);
			$('#btn_qqface').click(function(){
				if( $("#hack_wrap").is(':hidden') ){
					$("#hack_wrap").show(100);
					$("#hack_wrap em").off("click");
					$("#hack_wrap em").click(function(){
						$("#hack_wrap em").removeClass('ck');
						$(this).addClass('ck');
						$(".msgcontent").val( $(".msgcontent").val() + '[face' + $(this).data('id') + ']' )
					});			
				}else{
					 $("#hack_wrap").hide(500);
				}
			});
		}
	},

	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}


//类接口,加载到聊天会话数据时执行的  刷新数据的时候也会有到.不仅仅是初次加载
format_content.qqface = function(res,type){
	//rs.content = (rs.content).replace(/ src=('|")\/public\/static\//g," src=$1"+web_url+"/public/static/");
	/*
	if( typeof(api)=='object' ){
		$("#chat_win").find("img").each(function(){
			var url = $(this).attr('src');
			console.log(url);
			if(url.indexOf('/public/static/')==0){
				$(this).attr('src',url.substring(1));
			}
		});
	}
	*/
}