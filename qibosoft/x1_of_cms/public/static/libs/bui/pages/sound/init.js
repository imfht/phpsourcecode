//这个函数将弃用,以前框架APP时候用的
function apk_recode_end(url){
	//layer.msg(url);	
	$.post("/member.php/member/wxapp.msg/add.html",{
		content:'<audio controls="controls"><source src="'+url+'" type="audio/mp3" />不支持的浏览器</audio>',
		uid:uid,
		},function(res){
			refresh_timenum = 1;	//加快刷新时间
			if(res.code==0){
				layer.msg('发送成功');
			}else{
				layer.alert(res.msg);
			}
	});
}


//init() logic_init() once() finish() 的使用教程网址 http://help.php168.com/1435153
mod_class.sound = {
	recMp3:null,
	wx_allow_power:null,
	init:function(res){	//init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
		var that = this;	//引用传递
		var url = typeof(api)=='object'?web_url:'';
		var str = `<link rel="stylesheet" href="${url}/public/static/libs/bui/pages/sound/style.css" />
				<div class="sound_warp">
					<ul class="voicemenu">
							<div id="change_word_btn"><i class="fa fa-list"></i></div>
							<div id="voiceBtn">按住说话(别松手)</div>
					</ul>
				</div>`;
		router.$(".chat_mod_btn").after(str);

		router.$("#btn_sound").click(function(){			
			if(typeof(api)!='object' && typeof(wx)!='object' && typeof(window.inApk)!='object'){
				bui.hint('只有在APP或微信中才能使用语音聊天!');
				return ;
			}
			if(typeof(api)=="object" && that.recMp3 == null){
				that.recMp3 = api.require('recMp3')
			}
			if(typeof(wx)=="object" && that.wx_allow_power==null){	//提前申请权限,避免后续操作中断
				//wx_record_start();
				wx.startRecord({
					success: function() {
						bui.hint('长按录音,松开结束录音!');
						wx.stopRecord({});
					},
					cancel: function() {
						layer.alert('你拒绝了授权录音');
					}
				});
			}
			router.$(".chatbar>div").hide();
			router.$(".sound_warp").show();
		});

		router.$('#change_word_btn').click(function(){
			router.$(".sound_warp").hide();
			router.$(".bui-input").show();		
		});
		var layer_msg,recordTimer;
		var btnRecord = router.$('#voiceBtn');
		btnRecord.on('touchstart', function(event) {	//按住不放,开始录音
			event.preventDefault();
			btnRecord.addClass('re_cord');	
			startTime = new Date().getTime();
			// 延时后录音，避免误操作
			recordTimer = setTimeout(function() {
				layer_msg = layer.msg('请不要松手,正在开始录音...',{time:60000});
				start_recode();	//开始执行录音
			}, 200);
		}).on('touchend', function(event) {  //松手停止录音.
			event.preventDefault();
			btnRecord.removeClass('re_cord');	
			layer.close(layer_msg);
			// 间隔太短
			if (new Date().getTime() - startTime < 200) {
				startTime = 0;
				// 不录音
				clearTimeout(recordTimer);
				layer.alert('请按住不要松开,才能录音');
			} else { // 松手结束录音				
				layer.msg('录音完毕,上传中...');
				stop_recode(); //录音结束
			}
		});

		if(typeof(wx)!='undefined'){
			wx.onVoiceRecordEnd({
				complete: function (res) {
				  layer.msg('提示：每次录音不得超过一分钟',{time:1000});
				}
			});
		}

		//按住不放开始录音
		function start_recode(){
			if(typeof(api)=="object"){	//仿原生APP中录音
				that.recMp3.start(function(ret,err){
					if(ret){
						if(ret.db!=undefined){
							// 一秒钟采样10次
							//document.getElementById('value').style.height=ret.db+"px";
							//document.getElementById('value').style.marginTop=(100-ret.db)+"px";
							//api.toast("录音正式开始了")
							console.log(ret.db+"px");
						}
					}else{
						layer.alert(err.message);
					}
				});
			}else if(typeof(window.inApk)=='object'){ //套壳APP
				if(token==''){
					layer.alert("你还没登录");
				}else{
					window.inApk.voice_record(token);
				}
			}else{	//小程序
				wx_record_start();
			}
		}
		
		//结束录音
		function stop_recode(){
			if(typeof(api)=="object"){
				app_record_end();
			}else if(typeof(window.inApk)=='object'){
				if(token!=''){
					window.inApk.voice_record("end");
					//异步还要执行一个全局函数,因为全局函数才能被APP调用
				}
			}else{
				wx_record_end();
			}
		}

		function wx_record_start(){
			/*
			try{
				wx.stopRecord({});
			}catch(e){
				console.log(e);
			}*/
			wx.startRecord({
				success: function() {
					that.wx_allow_power = true;
				},
				cancel: function() {
					layer.alert('你拒绝了授权录音');
				}
			});
		}
		
		//仿原生APP录音结束
		function app_record_end(){
			that.recMp3.stop(function(ret,err){
				if(ret){
					var duration = ret.duration;
					var path = ret.path;
					console.log(ret.message+", 时长(秒)："+duration+"毫秒"+ret.millisecond+",路径："+path);
					if(duration>0){
						upload_mp3(path);
						console.log(path);
						api.startPlay({		//试听
							path: path
						}, function (ret, err) {
						});
						//document.getElementById('TestDiv').innerHTML= '<a href="javascript:void(0)" data-src="'+path+'" onclick="playRecord(this)"> '+path+' 播放</a>';
					}
				}else{
					 layer.alert(err.message)
				}
			});
		}

		function upload_mp3(path){
			sound2word(path);
			api.ajax({
				url: web_url+"/index.php/bbs/wxapp.post/postfile.html",
				method: 'post',
				data: {
					values: {
						"token": u_token
					},
					files: {
						"file": path
					}
				}
			}, function(res, err) {
				if (res) {
					if(res.code==0){
						postmsg('<audio controls="controls"><source src="'+res.data.url+'" type="audio/mp3" />你收到一条语音信息</audio>');	//发送数据到服务器
					}else{
						layer.alert(res.msg);
					}
					console.log(res);
					//layer.alert(res.data.url);
				} else {
					layer.alert({ msg: JSON.stringify(err) });
				}
			});
		}
		
		//服务器识别语音
		function sound2word(path){
			api.ajax({
				url: "http://svn.php168.com/mp3topcm.php",
				method: 'post',
				data: {
					values: {
						"token": 'test'
					},
					files: {
						"file": path
					}
				}
			}, function(res, err) {
				if (res) {
					postmsg('<div class="fanyi-sound">译:'+res.data+'</div>');
				} else {
					alert( JSON.stringify(err) );
				}
			});
		}
		
		//微信识别语音
		function wx_fanyi(localId){
			wx.translateVoice({
			  localId: localId,
			  isShowProgressTips: 1, // 默认为1，显示进度提示
			  //success: function (res) {alert(res.translateResult);}, // 语音识别的结果
			  complete: function (res) {
				if (res.hasOwnProperty('translateResult')) {
					//alert('识别结果：' + res.translateResult );
					postmsg('<div class="fanyi-sound">译:'+res.translateResult+'</div>');
				} else {
					layer.msg('无法识别');
				}
			  }
			});
		}

		//微信录音结束
		function wx_record_end(){
			wx.stopRecord({
				success:function(res){
					//voice.localId = res.localId;				
					//tran_record();
					wx.playVoice({	//播放录音
						localId: res.localId,
					});					
					wx.uploadVoice({
						localId: res.localId,
						success: function (res) {
							//layer.alert(' 上传语音成功，serverId 为' + res.serverId);
							$.post("/member.php/member/wxapp.msg/add.html",{voiceid:res.serverId,uid:uid},function(res){
								if(res.code==0){
									layer.msg('发送成功');
								}else{
									layer.alert(res.msg);
								}
							});
						}
					});
					wx_fanyi(res.localId);
				},
				fail:function(res) {
					layer.alert('录音无法识别,请重新按住录音!'+JSON.stringify(res));
				}
			});
		}
	},
	finish:function(res){  //所有模块加载完才执行
	},
	logic_init:function(res){  //init()只做界面渲染与页面元素的事件绑定,若做逻辑的话,更换圈子时PC端不执行,执行的话,会导致界面重复渲染。logic_init()做逻辑处理,满足更换圈子房间的需要
	},

}

