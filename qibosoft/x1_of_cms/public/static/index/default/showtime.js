function format_time(time){
	var show_time='';
	if(time>0){
		if(Math.floor(time/31536000)>0){
			show_time+=Math.floor(time/31536000)+"年"
		}
		if(Math.floor(time%31536000/86400)>0){
			show_time+=Math.floor(time%31536000/86400)+"天"
		}
		if(Math.floor(time%31536000%86400/3600)>0){
			var thishour=Math.floor(time%31536000%86400/3600);
			if(thishour<10){
				thishour="0"+thishour;
			}
			show_time+=" <i>"+thishour+"</i> : "
		}else{
			show_time+=" <i>00</i> : "
		}
		if(Math.floor(time%31536000%86400%3600/60)>0){
			var thismis=Math.floor(time%31536000%86400%3600/60);
			if(thismis<10){
				thismis="0"+thismis;
			}
			show_time+=" <i>"+thismis+"</i> : "
		}else{
			show_time+=" <i>00</i> : "
		}
		var thissend=Math.floor(time%60);
		if(thissend<10){
			thissend="0"+thissend;
		}
		show_time+="<i>"+thissend+"</i>"
	}else{
		show_time="<p>特惠活动结束</p>";
	}
	return show_time;
}