/**
 * 用户操作
 */
var User = {
	box:"#users-box .users-box",
	
	// 清空用户列表
	clearUserList: function() {
		$(this.box).html("");
	},
	
	// 获取用户
	get: function(uid) {
		return $(this.box).find("span[uid="+uid+"]");
	},
	
	// 添加用户
	add: function(uid, info) {
		var obj = this.get(uid);
		
		if(!obj.length) {
			var html = '<span uid="'+uid+'">'+info.nickname+'</span>'
			$(this.box).append(html);
			
			obj = this.get(uid);
		}
		
		info.isReady ? obj.addClass('ready') : obj.removeClass('ready');
		info.isOwner ? obj.addClass('owner') : obj.removeClass('owner');
	},
	
	// 用户准备
	ready: function(uid) {
		$(this.box).find("span[uid="+uid+"]").addClass('ready');
	},
	
	// 取消准备
	unReady: function(uid) {
		$(this.box).find("span[uid="+uid+"]").removeClass('ready');
	},
	
	// 删除用户
	del: function(uid) {
		$(this.box).find("span[uid="+uid+"]").remove();
	},
	
	// 切换房主
	changeOwner: function(uid) {
		$(this.box).find(".owner").removeClass("owner");
		$(this.box).find("span[uid="+uid+"]").addClass('owner').removeClass("ready");
	},
	
	// 显示用列表
	show: function(users) {
		$("#users-box").show();
		
		for(var uid in users) {
			this.add(uid, users[uid]);
		}
	}
	
}