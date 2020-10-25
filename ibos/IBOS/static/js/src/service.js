/**
 * 智齿客服组件
 * Glogal
 */
(function($, Ibos){
	var L = {
		VERSION: '当前版本',
		SITE_URL: '系统地址',
		INSTALED_MODULES: '已安装模板'
	};
	var initCSDialog = function(){
		var modulesArr = [],
			userObj = Ibos.data.getUser('u_' + G.uid),
			dialogColor = '3697db', 
			remarkInfo;

		$.each(G.modules, function(k, m){
			modulesArr.push(m.name);
		});

		remarkInfo = L.VERSION + ': ' + G.VERSION + ' ; ' + L.SITE_URL + ': ' + G.SITE_URL + ' ; ' + L.INSTALED_MODULES + ': ' + modulesArr.join(', ');

		//初始化智齿咨询组件实例
        var zhiManager = (getzhiSDKInstance());
        //再调用load方法
        zhiManager.on("load", function() {
            zhiManager.initBtnDOM();
            zhiManager.set('userinfo', {
            	partnerId: G.uid,
            	uname: userObj.text + '-' + G.shortname,
	            tel: userObj.phone,
	            realname: userObj.text,
	            remark: remarkInfo
            });
            // 设置组件弹窗颜色
            zhiManager.set('color',dialogColor);
        });

        return zhiManager;
	};

	Ibos.initCSDialog = initCSDialog;
})(jQuery, Ibos);