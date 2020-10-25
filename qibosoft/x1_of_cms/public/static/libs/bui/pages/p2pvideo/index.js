let localStream = null;
let peer = null;

var parents;
if(window.parent.frames['iframe_msg']!=undefined){
	parents = window.parent.frames['iframe_msg'];
}else{
	parents = window.parent;
}

var mycid = parents.clientId;		//自己的唯一标志
var hecid;	//请求通话者的唯一标志
var userinfo = parents.userinfo;  //当前用户信息
var localVideo = document.querySelector("video#localVideo");
var remoteVideo = document.querySelector("video#remoteVideo");
var videoSelect = document.querySelector("select#videoSelect");
var btnCall = document.querySelector("button#btnCall");
var accept_send_stream = false;

function list_user(online_members){
	var str = '';
	var weburl = window.location.href;	//var weburl = window.location.href.indexOf("http")==0 ? '' : '../../../../../..';
		weburl = weburl.substring(0,weburl.indexOf('/public/static/')); //方便手机获取真实路径
	online_members.forEach((rs)=>{
		rs.username = rs.username.substring(0,8)
		str += `<li class="user" data-id="${rs.uid}"><a href="#"><img src="${rs.icon}" onerror="this.src='${weburl}/public/static/images/noface.png'" ><br>${rs.username}</a></li>`;
	});
	$(".list_user .list").html(str);
	$(".list_user .user").click(function(){
		$(".list_user .user").removeClass('ck');
		$(this).addClass('ck');
		if(localStream==null){
			layer.alert("你的电脑没有摄像头,不能发起视频通话!");
			return ;
		}		
		parents.mod_class.p2pvideo.is_invite = true;
		var arr = {
			uid:$(this).data('id'),
			user:userinfo,
			cid:mycid,
		}
		parents.ws_send({
			type:"qun_to_alluser",	//群发给所有圈内成员的标志,固定格式
			tag:"ask_video_phone",	//接收标志,根据不同的应用,需要重新自定义
			data:arr,				//接收变量
		});
		layer.msg("请求已发出,请耐心等候对方接受并同意!");
	});
}

function palyer(cid){
	console.log('cid',cid);
	hecid = cid;
	if(typeof(userinfo)!='object'||userinfo.uid<1){
		alert("请先登录！");
		return ;
	}

	if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        layer.alert("你的浏览器不支持视频电话!");
        return;
    }
	navigator.mediaDevices.enumerateDevices().then(gotDevices).catch(function(error){layer.alert('找不到摄像头 '+error.message+","+error.name);});
	
	start();

	init_peer();

	videoSelect.onchange = start;

	if(typeof(cid)=='undefined'){
		//list_user();
		
		parents.ws_send({type:'get_online_user',tag:'gave_p2p_user'});  //请求在线用户
	}
}

//接收到的在线用户数
parents.ws_onmsg.getlistuser = function(obj){
	if(obj.type=='gave_p2p_user'){
		console.log(obj.data);
		list_user(obj.data);
	}
};

//开启本地摄像头 或切换摄像头
function start() {
    if (localStream!=null) {	//切换摄像头的话，先销毁
        localStream.getTracks().forEach(track => {
            track.stop();
        });
    }
	const videoSource = videoSelect.value;
    const constraints = {
        audio: true,
        video: { width: 320, deviceId: videoSource ? { exact: videoSource } : undefined }
    };
    navigator.mediaDevices.getUserMedia(constraints)
        .then(function(stream){
			var ischange = false;
			if(localStream!=null){
				ischange = true;
			}
			localStream = stream;	//本地视频流
			localVideo.srcObject = localStream;
			localVideo.muted=true;
			accept_give_stream(ischange);	//接受邀请后，就发送视频流给发起方 , 切换了摄像头，也要得新发视频流给对方。
		})
        .then(gotDevices)	//绑定摄像头列表到下拉框
        .catch(function(error){			
			if(typeof(hecid)!='undefined'){
				layer.alert("你的电脑没有摄像头,不能接受视频通话!");
			}else{
				layer.alert('摄像头不存在,视频信息采集失败 '+error.message+","+error.name);
			}
		});
}



//被呼叫方,接受邀请后，就发送视频流给发起方
function accept_give_stream(ischang){
	if(accept_send_stream != true && ischang!=true){	//接通后，切换摄像头的话，就都要执行下面的
		return ;
	}
	var call = peer.call(hecid, localStream);	//拿自己的视频流去换取对方的视频流
	call.on('stream', function (stream) {	//同时收到发起方的视频流
		console.log('received remote stream');
		remoteVideo.srcObject = stream;		//显示发起方的视频
		sendMessage(mycid, hecid, "accept-ok"); //通知发起方，已接收成功
	});
	//call.close();
}

//绑定摄像头列表到下拉框
function gotDevices(deviceInfos) {
    if (deviceInfos===undefined){
        return
    }
    for (let i = 0; i !== deviceInfos.length; ++i) {
        const deviceInfo = deviceInfos[i];
        const option = document.createElement('option');
        option.value = deviceInfo.deviceId;
        if (deviceInfo.kind === 'videoinput') {
            option.text = deviceInfo.label || `camera ${videoSelect.length + 1}`;
            videoSelect.appendChild(option);
        }
    }
}

let localConn = null;
function sendMessage(from, to, action) {
    var message = { "from": from, "to": to, "action": action ,'hecid':mycid };
    if (localConn==null) {
        localConn = peer.connect(to);
        localConn.on('open', () => {
            localConn.send(JSON.stringify(message));
        });
    }else if (localConn.open){
        localConn.send(JSON.stringify(message));
    }
}

function init_peer(){
	let connOption = {
		host: 'www.qibosoft.net',
		port: 9000,
		secure:true,
		path: '/myapp',
		debug: 3,
		config: {'iceServers': [{'url': 'stun:129.226.168.5:3478'},{ 'url': 'turn:test@129.226.168.5:3478','credential': '123456','username': 'test:123456'}]},
	};
	peer = new Peer(mycid, connOption);		//注册信息
	peer.on('open', function (id) {
		console.log(" register success. " + id,mycid);
		if(typeof(hecid)!='undefined'){	//初始化的时候。接受视频邀请，才有这个值，要注意的是，接通后，双方都有对方的唯一标志
			accept_send_stream = true;
		}
	});
	
	peer.on('call', function (call) {	//对方通过他的视频流来请求你的频流，你就要把你的视频流回复给对方。物物交换的意思差不多
		call.answer(localStream);
	});

	peer.on('connection', (conn) => {
		conn.on('data', (data) => {
			var res = JSON.parse(data);
			console.log(res);			
			if (res.action === "accept-ok") { //发起方,收到被邀请方的同意信息, 其实被邀请方,已在显示你的视频了
				console.log("accept-ok call => " + JSON.stringify(res));
				hecid = res.hecid;	//接通后，双方都有对方的唯一标志
				var call = peer.call(res.from, localStream);	//拿自己的视频流去换取对方的视频流
					call.on('stream', function (stream) {
                        console.log('received remote stream');
                        remoteVideo.srcObject = stream;                            
                    });
                }
            });
	});
}