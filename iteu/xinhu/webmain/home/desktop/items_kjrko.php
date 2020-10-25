<?php 
/**
*	桌面首页项(快捷入口)
*/
defined('HOST') or die ('not access');

?>
<script>
homeobject.showicons=function(a){
	a.push({name:'刷新统计中...',icons:'refresh',num:'refresh',color:'#888888'});
	this.menuarr = a;
	var o = $('.rowaaaa'),s='';
	o.html('');
	for(var i=0; i<a.length;i++){

		s='';
		s+='<div onclick="opentabsshowshwo('+i+')" align="center" style="cursor:pointer;width:135px;float:left;margin:10px 10px;" class="thumbnail">';
		s+='	<div style="position:relative;padding:15px 0px">';
		s+='		<div style="color:'+a[i].color+';font-size:34px"><i class="icon-'+a[i].icons+'"></i></div>';
		s+='		<a style="TEXT-DECORATION:none" id="'+a[i].num+'_text">'+a[i].name+'</a>';
		s+='		<span class="badge" badge="'+a[i].num+'" style="background:red;position:absolute;top:5px;right:5px;display:none"></span>';
		s+='	</div>';
		s+='</div>';
		o.append(s);
	}
}
opentabsshowshwo=function(oi,o1){
	var a = homeobject.menuarr[oi];
	if(a.num=='refresh'){
		homeobject.refresh();
	}else{
		var anum = {num:a.num,url:a.url,name:a.name,icons:a.icons,id:a.id};
		addtabs(anum);
	}
	return false;
}
</script>
<div class="rowaaaa" style="display:inline-block" align="left"></div>
<div class="blank1" style="margin:10px 0px"></div>
<div class="blank10"></div>