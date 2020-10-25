/*****************************/
/*		统计代码配置		*/
/*****************************/
document.writeln('<div style="display:none;">');
//百度统计
if(info['bdtjid']!=""){
	var _hmt = _hmt || [];
	(function() {
		var hm = document.createElement("script");
		hm.src = "//hm.baidu.com/hm.js?{bdtjid}";
		var s = document.getElementsByTagName("script")[0]; 
		s.parentNode.insertBefore(hm, s);
	})();
}
if(info['cnzzid']!=""){
	var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
	document.write(unescape("%3Cspan id='cnzz_stat_icon_{cnzzid}'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s4.cnzz.com/stat.php%3Fid%3D{cnzzid}%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));
}
//套电代码
if(info['taotelid']!=""){
	document.write('<script type="text/javascript" src="http://182.254.131.128/stat.php?uid={taotelid}" charset="utf-8"></script>');
	document.write('<img src="http://182.254.131.128/img_e.php?uid={taotelid}" width="0" height="0"/>');
}
document.writeln('</div>');
