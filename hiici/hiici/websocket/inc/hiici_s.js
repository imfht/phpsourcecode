navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;

var cs = {}, r_cs = {}, pr = null, pr_id = null;

init();
function init() {
	pr_init();

	var a_v = (1 == l_m) ? {audio:true} : {audio:true, video:{ mandatory: { minWidth: 1280, minHeight: 720 }} } ;
	navigator.getUserMedia(a_v, function(st){
		$('#hiici_s').prop('src', URL.createObjectURL(st));
		window.l_s = st;
		log('直播间正常运行。');
	}, function(){});
}

function pr_init() {
	var opt = {host: 'hiici.herokuapp.com', port: 80, path: '/', debug: 3, config: {'iceServers': [ { url: 'stun:stun.ekiga.net' } ]}};
	if (null == pr_id) {
		pr = new Peer(opt);
		pr.on('open', function(){
			pr_id = pr.id; 
			pr_l();
			setInterval(pr_l, 300000);
		});
	} else {
		pr = new Peer(pr_id, opt);
	}

	pr.on('disconnected', function(){
		pr.connections = false;
		pr.destroy();
		pr_init();
	});

	pr.on('connection', function(c){
		if (1 == p_m) {
			if (c.metadata.password != password) {
				r_cs[c.peer] = c;
				setTimeout(function(){ for (r in r_cs) { r_cs[r].close(); delete r_cs[r] } }, 2000);
				return false;
			}
		}

		back_c(c.peer);
		cs[c.peer] = c;

		msg = c.label+' 进入了直播间。（共 '+Object.keys(cs).length+' 人）';
		log(msg);
		for (k in cs) {
			cs[k].send('"></a>'+msg);  
		}

		c.on('data', function(dt) {
			log('<a target="_blank" href="?c=center&a=index&user_id='+dt);
			for (k in cs) {
				cs[k].send(dt);  
			}
		});
		c.on('close', function() {
			$('audio#'+c.peer).remove();
			delete cs[c.peer];
		});
	});
}

function back_c(p_id) {
	var c = pr.call(p_id, window.l_s);
	if (1 == a_m) {
		c.on('stream', function(st){
			$('#hiici_s').after('<audio id="'+c.peer+'" src="'+URL.createObjectURL(st)+'" style="display:none" autoplay>');
		});
	}
}

function pr_l() {
	$.get('?c=websocket&a=flex_n_id&n_id='+pr_id, function(rs){});
}

function send() {  
	var msg = $('#msg').val(); 
	$('#msg').val('');  
	if (!msg) return false;  

	log('主播：'+msg);
	for (k in cs) {
		cs[k].send(user.id+'.htm">['+user.name+']</a> '+msg);  
	}
}  
