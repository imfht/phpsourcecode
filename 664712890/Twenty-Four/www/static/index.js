$(function() {
	window.ws = new WebSocket(CONF.server);
	window.boxs = {
		number: "#number-box .number",
		result: "#result-box .number",
		tags:  "#result-box .tag"
	};
	var ping = 0;
	var freeTime = 0;

	function send(data) {
		freeTime = 0;
		
		ws.send(JSON.stringify(data));
	}
	
	ws.onopen = function() {
		// 准备游戏
		$("#ready-box .ready").click(function() {
			if(Game.isReady) return;
			
			send({path:'game/ready'});
		});
		
		// 开始游戏（房主）
		$("#start-box .start").click(function() {
			var d = {
				path:'game/start',
				questionNumber: $("select[name=questionNumber]").val(),
				isPoints: $("select[name=isPoints]").val()
			};
			
			send(d);
		});
		
		// 创建房间
		$("#room-box .createRoom").click(function() {
			send({path:'room/create'});
		});
		
		// 加入房间
		$("#room-box .joinRoom").click(function() {
			var val = $.trim( $("#room-box input[name=roomid]").val() );
			
			if(!val) return Game.tip($(this), '请输入房间号!');
			
			send({path:'room/join', id:CONF.roomPrefix+val});
		});
		
		// 设置昵称
		$("#nickname-box .editNickname").click(function() {
			var val = $.trim($("#nickname-box input[name=nickname]").val());
			
			if(!val) return Game.tip($(this), '请输入昵称!');
			if(val.length > 8) return Game.tip($(this), '昵称最多8个字符!');
			
			send({path:'user/nickname', value:val});
		});
		
		// 无答案
		$("#no-answer").click(function() {
			if(!Game.onStart) return;
			
			send({path:'game/answer', value:-1});
		});
		
		// 退出
		$(".leave").click(function() {
			send({path:'room/leave'});
		});
		
		// 提交
		$("#post").click(function() {
			if(!Game.onStart) return;
			
			var res = [];
			$(boxs.result).each(function() {
				var i = $(this).index();
				res[i] = $(this).html();
			});
			$(boxs.tags).each(function() {
				var i = $(this).index();
				res[i] = $(this).val();
			});
			
			res = res.join('');
			console.log(res);
			
			if(!/^(\d+[\+\-\*\/]){3}\d+$/.test(res)) {
				Game.tip($(this), '输入有误！');
				return false;
			}
			
			send({path:'game/answer', value: res});
		});
		
		// 重置
		$("#reset").click(function() {
			if(Game.onStart) {
				Game.resetQuestion();
			}
		});
	};
	
	ws.onmessage = function(evt) {
		if(Game.ingame) freeTime = 0;
		
	  var res = JSON.parse(evt.data);
	  
	  console.log(res);
	  
	  if(Game[res.type]) return Game[res.type](res);
	  
	  switch(res.type) {
	  	case 'errorMsg':
	  		Game.msg(res.msg, 'error');
	  		break;
	  	case 'successMsg':
	  		Game.msg(res.msg, 'success');
	  		break;
	  	case 'ping':
	  		break;
	  	default:
	  		Game.msg(res.msg);
	  		break;
	  }
	};
	
	ws.onclose = function(evt) {
		clearInterval(ping);
		Game.msg('与服务器断开连接，请刷新页面重新开始。', 'error');
	  console.log('WebSocketClosed!');
	};
	
	ws.onerror = function(evt) {
	  console.log('WebSocketError!', evt);
	};
	
	// ping
	ping = setInterval(function() {
		if( !Game.creater && !Game.isReady && (freeTime/CONF.pingInterval) > CONF.maxPingTime ) {
			send({path:'site/out'});
			return;
		}
		
		if( (++freeTime % CONF.pingInterval) == 0 ) {
			ws.send(JSON.stringify({path:'site/ping'}));
		}
	}, 1000);
	
	// 选择
	var n = 0;
	$(boxs.number).click(function() {
		//if(!Game.onStart) return;
		var val = $(this).html();
		if( $(this).hasClass("sel") ) return;
		
		$(this).addClass("sel")
		$(boxs.result+"[idx=-1]:eq(0)").html(val).attr("idx", $(this).index());
	});
	
	// 反选
	$(boxs.result).click(function() {
		var idx = $(this).attr("idx");
		if(idx == -1) return;
		
		$(boxs.number).eq(idx).removeClass("sel");
		$(this).html('').attr("idx", -1);
	});
	
	// 游戏规则
	$('#game-rule').popover({
		html:true,
		content:'<ol class="game-rules"><li>4个数，使用四则运算，使结果等于24</li>'+
		'<li>多人抢答，先答对加一分，房主可配置答错是否扣分</li>'+
		'<li>完成房主指定题目数后游戏结束</li></ol>',
		placement:'top',
		trigger:'fouce'
	});
	
});


