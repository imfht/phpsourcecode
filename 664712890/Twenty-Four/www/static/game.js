var Game = {
	creater: false,
	tipTime: 0,
	onStart: false, // 是否开始答题
	isReady: false, // 是否准备
	ingame: false, // 是否在游戏中
	uid: '',
	beforeUser:{}, // 上一次答题正确的用户
	boxs:{
		room:"#room-box",
		nickname: "#nickname-box",
		start:"#start-box",
		ready:"#ready-box",
		count:"#count-down",
		result:"#result-box",
		game:"#game-box",
		users:"#users-box",
		over:"#game-over",
		message:"#message-box"
	},
	
	// 昵称设置成功
	nicknameSetSuccess: function(res) {
		$(this.boxs.nickname).hide();
		$(this.boxs.room).show();
	  
		this.uid = res.data.uid;
		this.msg(res.msg, 'success');
	},
	
	// 创建房间成功
	createRoomSuccess: function(res) {
		$(this.boxs.room).hide();
		$(this.boxs.start).show();
		
		User.show(res.data.users);
		this.creater = true;
		this.msg('创建成功，房间号：<code>'+res.data.id.replace(CONF.roomPrefix, '')+'</code>, 发送给小伙伴一起来玩游戏', 'success');
	},
	
	// 加入房间成功
	joinRoom: function(res) {
		$(this.boxs.room).hide();
		$(this.boxs.ready).show().find(".ready").html("准备").removeAttr("disabled");
		
		User.show(res.data.users);
		this.msg('成功加入房间', 'success');
	},
	
	// 离开房间成功
	leaveRoom: function(res) {
		this.isReady = false;
		$(this.boxs.room).show();
		$(this.boxs.ready).hide();
		$(this.boxs.start).hide();
		
		User.clearUserList();
		this.msg('退出房间成功', 'success');
	},
	
	// 准备成功
	readySuccess: function(res) {
		this.isReady = true;
		$(this.boxs.ready).find(".ready").html("已准备").attr("disabled", "disabled");
		
		User.ready(this.uid);
		this.msg('已准备, 等待游戏开始!', 'success');
	},
	
	// 收到题目
	question: function(res) {
		this.onStart = true;
		this.msg(res.msg, 'warning');
		
		$(this.boxs.count).hide();
		$(this.boxs.result).show();
		
		$(boxs.number).each(function(i){
			$(this).html(res.data[i]);
		});
		
		this.beforeUser = {};
		this.resetQuestion();
	},
	
	// 某人完成答题
	answered: function(res) {
		this.onStart = false;
		
		this.msg('<code>'+res.data.nickname+'</code> 抢答成功。');
		
		this.beforeUser = res.data;
	},
	
	// 答题结果
	answerResult: function(res) {
		this.msg(res.data.result ? '回答正确' : '回答错误，在计算一下吧！', res.data.result ? 'success' : 'error');
	},
	
	// 房间解散
	roomeDissolve: function(res) {
		this.leaveRoom();
		this.msg('房间已解散', 'error');
		User.clearUserList();
	},
	
	// 成为房主
	toRoomOwner: function(res) {
		if(!this.ingame) {
			$(this.boxs.ready).hide();
			$(this.boxs.start).show();
		}
		
		this.creater = true;
		this.msg('成为房主', 'warning');
	},
	
	// 游戏开始
	startGame: function(res) {
		$([this.boxs.ready,this.boxs.start,this.boxs.over].join()).hide();
		$(this.boxs.game).show();
		this.ingame = true;
		
		this.countDown();
		this.msg('所有玩家准备完毕，'+CONF.gameWaitTime+'秒后游戏开始。', 'warning');
		$(this.boxs.game).find(".answer-config-desc").show().html('本轮游戏共'+res.data.questionNumber+'题，答错'+(res.data.isPoints ? '' : '不')+'扣分');
	},
	
	// 游戏结束
	finishGame: function(res) {
		this.onStart = false;
		this.ingame = false;
		this.isReady = false;
		
		$(this.boxs.game).hide();
		$(this.boxs.over).show();
		
		if(this.creater) {
			$(this.boxs.start).show()
		} else {
			$(this.boxs.ready).show().find(".ready").html("准备").removeAttr("disabled");
		}
		
		var html = '';
		for(var uid in res.data) {
			html += '<li class="list-group-item"><span class="badge">'+res.data[uid].score+'分</span>'+res.data[uid].nickname+'</li>'
		}
		$(this.boxs.over).find("ul").html(html);
		
		this.msg('游戏结束', 'warning');
	},
	
	// 广播：用户加入房间
	broadcastUserJoin: function(res) {
		User.add(res.data.uid, res.data);
		this.msg('<code>'+res.data.nickname+'</code>加入房间。', 'broadcast');
	},
	
	// 广播：用户离开房间
	broadcastUserLeave: function(res) {
		User.del(res.data.uid);
		this.msg('<code>'+res.data.nickname+'</code>退出房间。', 'broadcast');
	},
	
	// 广播 房主变更
	broadcastChangeOwner:function(res) {
		User.changeOwner(res.data.uid);
		this.msg('房主移交给 <code>'+res.data.nickname+'</code>', 'broadcast');
	},
	
	// 广播 用户准备
	broadcastUserReady: function(res) {
		User.ready(res.data.uid);
		this.msg('<code>'+res.data.nickname+'</code> 已准备', 'broadcast');
	},
	
	// 广播 准备开始下一题
	broadcastNextQuestion: function(res) {
		this.countDown();
		this.msg(CONF.gameWaitTime+'秒后开始下一题。');
	},
	
	// 消息
	msg: function(msg, cls) {
		cls = cls || 'normal';
		var d = new Date;
		var time = [d.getHours(),d.getMinutes(),d.getSeconds()];
		for(var i = 0; i < time.length; i++) {
			if(time[i] < 10) time[i] = '0'+time[i];
		}
		
		$(this.boxs.message).prepend('<p><span class="msg-time">'+time.join(":")+'</span> <span class="msg-'+cls+'">'+msg+'</span></p>');
	},
	
	// tip
	tip: function(obj, msg) {
		obj.tooltip('destroy')
			.tooltip({
			placement:'bottom',
			trigger:'manual',
			title:msg
		})
		.tooltip('show');
		
		if(this.tipTime) clearTimeout(this.tipTime);
		this.tipTime = setTimeout(function() {
			obj.tooltip('hide');
		}, 3000);
	},
	
	// 重置答案
	resetQuestion: function() {
		$(boxs.number).each(function() {
			$(this).removeClass("sel");
		});
		
		$(boxs.result).each(function() {
			$(this).attr("idx", -1).html('');
		});
		
		$(boxs.tags).each(function() {
			$(this).val("+");
		});
	},
	
	// 游戏倒计时
	countDown: function() {
		var n = CONF.gameWaitTime;
		var obj = $(this.boxs.count);
		
		$(this.boxs.result).hide();
		
		msg = (this.beforeUser.nickname ? '<code>'+this.beforeUser.nickname+'</code>抢答成功<br>' : '')+
			'<span>'+n+'</span> 秒后开始下一题';
		
		obj.html(msg).show();
		var time = setInterval(function() {
			n--;
			if(n <= 0) {
				obj.html("游戏即将开始...");
				clearInterval(time);
				return;
			}
			
			obj.find("span").html(n)
		}, 999);
		
	}
}