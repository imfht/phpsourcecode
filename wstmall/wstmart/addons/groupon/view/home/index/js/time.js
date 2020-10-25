WST.countDown = function(opts){
	var f = {
		zero: function(n){
			var n = parseInt(n, 10);
			if(n > 0){
				if(n <= 9){
					n = n;	
				}
				return String(n);
			}else{
				return "0";	
			}
		},
		count: function(){
			if(opts.nowTime){
				var d = new Date();
				d.setTime(opts.nowTime.getTime()+1000);
				opts.nowTime = d;
				d = null;
			}else{
				opts.nowTime = new Date();
			}
			//现在将来秒差值
			var dur = Math.round((opts.endTime.getTime() - opts.nowTime.getTime()) / 1000), pms = {
				sec: "0",
				mini: "0",
				hour: "0",
				day: "0"
			};
			if(dur > 0){
				pms.sec = f.zero(dur % 60);
				pms.mini = Math.floor((dur / 60)) > 0? f.zero(Math.floor((dur / 60)) % 60) : "0";
				pms.hour = Math.floor((dur / 3600)) > 0? f.zero(Math.floor((dur / 3600)) % 24) : "0";
				pms.day = Math.floor((dur / 86400)) > 0? f.zero(Math.floor(dur / 86400)) : "0";
			}
			pms.last = dur;
			pms.nowTime = opts.nowTime;
			opts.callback(pms);
			if(pms.last>0)setTimeout(f.count, 1000);
		}
	};	
	f.count();
};