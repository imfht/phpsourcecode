var c_c = null, l_s = null;
var pr = new Peer({host: 'hiici.herokuapp.com', port: 80, path: '/', debug: 0, config: {'iceServers': [
	{ url: 'stun:stun.ekiga.net' } 
	]}});

if (1 == p_m) get_password();
else {
	pr.on('open', function(){
		conn();
	});
}

pr.on('call', function(c){
	if (1 == a_m) {
		navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
		navigator.getUserMedia({audio:true}, function(st){
			c.answer(st);
		}, function(){});
	} else {
		c.answer();
	}

	c.on('stream', function(st){
		$('#hiici_s').prop('src', URL.createObjectURL(st));
		l_s = st;
		log('欢迎进入！');
	});
});

function get_password() {
	$('div.websocket').hide();
	$('div#get_password').show();
}
function do_get_password() {
	encrypt_password();
	conn($('input#password').val());
	$('div.websocket').show();
	$('div#get_password').remove();
}

function conn(p_w) {
	c_c = pr.connect(n_id, {
		label: (!user) ? '游客' : user.name,
	    metadata: {
		    password: p_w
	    }
	});
	c_c.on('data', function(dt) {
		log('<a target="_blank" href="?c=center&a=index&user_id='+dt);
	});
	c_c.on('close', function(){ if (null == l_s) log('密码错误！'); else log('主播关闭了直播间。'); });
}

function send() {  
	if (!user) jump_to_login('?c=websocket&a=hiici_ui&n_id_md5='+n_id_md5); 

	var msg = $('#msg').val();
	$('#msg').val('');  
	if (!msg) return false;  

	c_c.send(user.id+'.htm">['+user.name+']</a> '+msg);  
}  
