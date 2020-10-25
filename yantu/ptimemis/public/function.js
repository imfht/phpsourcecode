	//本地存储
    function kset(key,value){window.localStorage.setItem(key,value);}
    function kget(key){return window.localStorage.getItem(key);}
    function kremove(key){window.localStorage.removeItem(key);}

	

	//分页
    function getPager(total){
      	var pager = [];
      	pager['currentPage'] = total.currentPage*1;
      	pager['lastPage']    = total.lastPage==0?1:total.lastPage;
      	pager['pages']       = [];
      	for (var i = 1; i < total.last_page+1; i++) {
         	pager['pages'].push(i);
      	};
      	return pager;
    }
    //获取标准时间格式
    function getTime(){
    	var date = new Date();
		Y = date.getFullYear() + '-';
		M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
		D = (date.getDate()<10?'0'+(date.getDate()):date.getDate()) + ' ';
		h = (date.getHours()<10?'0'+(date.getHours()):date.getHours()) + ':';
		m = (date.getMinutes()<10?'0'+(date.getMinutes()):date.getMinutes()) + ':';
		s = (date.getSeconds()<10?'0'+(date.getSeconds()):date.getSeconds()); 
		return Y+M+D+h+m+s;
    }
    //时间格式化
    angular.module('objectFilters', []).filter('timeFormat', function() {
	  	return function(time) {
	  		if(time == null) return "";
	  		var publishTime = Date.parse(time)/1000;
	    	var timeNow   = parseInt(new Date().getTime()/1000);            
	    	var d 		  = timeNow - publishTime;       
	    	var d_days    = parseInt(d/86400);       
		    var d_hours   = parseInt(d/3600);       
		    var d_minutes = parseInt(d/60);

		    if(d_days>0 && d_days<4){       
		        return d_days+"天前";       
		    }else if(d_days<=0 && d_hours>0){       
		        return d_hours+"小时前";       
		    }else if(d_hours<=0 && d_minutes>0){       
		        return d_minutes+"分钟前";       
		    }else if(d<=60){       
		        return "刚刚";       
		    }else{
		        var s    = new Date(publishTime*1000);
		        var year = "";   
		       	if(s.getFullYear() != new Date().getFullYear())
		       		year = s.getFullYear()+"年";
		        return year+(s.getMonth()+1)+"月"+s.getDate()+"日";       
		    }
	  	};

	//int转布尔
	}).filter('int2bool', function() {
	  	return function(number) {
	  		return number==1?true:false;
	  	};
	});

