
jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1;}
var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*60*1000));}else{date=options.expires;}
expires='; expires='+date.toUTCString();}
var path=options.path?'; path='+options.path:'';var domain=options.domain?'; domain='+options.domain:'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('');}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break;}}}
return cookieValue;}};function getcookie(name){var cookie_start=document.cookie.indexOf(name);var cookie_end=document.cookie.indexOf(";",cookie_start);return cookie_start==-1?'':unescape(document.cookie.substring(cookie_start+name.length+1,(cookie_end>cookie_start?cookie_end:document.cookie.length)));}
function setcookie(cookieName,cookieValue,seconds,path,domain,secure){var expires=new Date();expires.setTime(expires.getTime()+seconds);document.cookie=escape(cookieName)+'='+escape(cookieValue)
+(expires?'; expires='+expires.toGMTString():'')
+(path?'; path='+path:'/')
+(domain?'; domain='+domain:'')
+(secure?'; secure':'');}