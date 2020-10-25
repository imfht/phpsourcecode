/**
*	来自：信呼开发团队
*	作者：磐石(rainrock)
*	网址：http://www.rockoa.com/
*	修改时间：2020-03-20
*	移动端应用主js文件，请不要去修改
*/

var myScroll=false,yy={
	sousoukey:'',
	onshowdata:function(){},
	loadci:0,
	searchparams:{},
	resizehei:function(){
		var hei= this.getheight();
		if(agentlx==0){
			var ob = this.showobj.css({'height':''+hei+'px'});
			return ob;
		}
	},
	getheight:function(ss){
		var hei = 0;if(!ss)ss=0;
		if(get('searsearch_bar'))hei+=45;
		if(get('header_title'))hei+=50;
		if(get('footerdiv'))hei+=50;
		return $(window).height()-hei+ss;
	},
	chuinit:function(){
		for(var i in ybase)this[i]=ybase[i];
	},
	scrollnew:function(){
		var top = $(document).scrollTop();
		if(top>50){
			if(!get('backtuodiv')){
				var s = '<div id="backtuodiv" onclick="js.backtop()" style="position:fixed;right:5px;bottom:10px;width:30px;height:30px; background:rgba(0,0,0,0.4);z-index:9;border-radius:50%;font-size:14px;color:white;text-align:center;line-height:30px"><i class="icon-angle-up"></i></div>';
				$('body').append(s);
			}
		}else{
			$('#backtuodiv').remove();
		}
	},
	loadshow:function(){
		var url = location.href,arr = json.menu;
		var urla= url.split('#'),darr = this.getfirstnum(arr);
		var dkey= darr[0];
		if(urla[1])dkey = urla[1];
		this.getdata(dkey,1);
		if(darr[1]>-1){
			var tit = arr[darr[1]].name;
			if(darr[2]>-1)tit = arr[darr[1]].submenu[darr[2]].name;
			this.showtabstr(darr[1], tit);
		}
	},
	getfirstnum:function(d){
		var dbh = 'def',bh='',a = d[0],i,len,lens,subs;
		if(a){
			bh = a.url;
			if(a.submenu[0])bh=a.submenu[0].url;
		}
		try{
			var site = sessionStorage.getItem(''+json.num+'_event');
			if(site)bh = site;
		}catch(e){}
		
		if(isempt(bh))bh=dbh;
		len = d.length;
		var goi = -1,goj=-1;
		for(i=0;i<len;i++){
			subs = d[i].submenu;
			lens = subs.length;
			if(goi>-1)break;
			if(lens>0){
				for(var j=0;j<lens;j++){
					if(subs[j].url==bh){
						goi = i;
						goj = j;
						break;
					}
				}
			}else{
				if(d[i].url==bh){
					goi = i;
					break;
				}
			}
		}
		return [bh,goi,goj];
	},
	showtabstr:function(oi, tit){
		$('[temp="tablx"]').removeClass('active');
		$('[temp="tablx"]:eq('+oi+')').addClass('active');
		$('[temp="taby"]').css({'color':'','border-top':''});
		$('[temp="taby"]:eq('+oi+')').css({'color':'#1389D3','border-top':'1px #1389D3 solid'});
		$('[temp="taby"]:eq('+oi+')').find('font').html(tit);
		this.settitle(tit);
	},
	clickmenu:function(oi,o1){
		if(o1.className.indexOf('disabled')>0)return;
		var sid='menushoess_'+oi+'';
		if(get(sid)){
			$('#'+sid+'').remove();
			return;
		}
		$("div[id^='menushoess']").remove();
		var a = json.menu[oi],slen=a.submenu.length,i,a1;
		this.menuname1 = a.name;
		this.menuname2 = '';
		if(slen<=0){
			this.clickmenus(a,oi);
		}else{
			if(agentlx==0){
				var o=$(o1),w=1/json.menu.length*100;
				var s='<div id="'+sid+'" style="position:fixed;z-index:5;left:'+(o.offset().left)+'px;bottom:50px; background:white;width:'+w+'%" class="menulist r-border-r r-border-l">';
				for(i=0;i<slen;i++){
					a1=a.submenu[i];
					s+='<div onclick="yy.clickmenua('+oi+','+i+')" class="r-border-t" style="color:'+a1.color+';">'+a1.name+'</div>';
				}
				s+='</div>';
				$('body').append(s);
			}
			if(agentlx==1){
				var da = [];
				for(i=0;i<slen;i++){
					a1=a.submenu[i];
					a1.oi = oi;
					a1.i = i;
					da.push(a1);
				}
				js.showmenu({
					data:da,
					width:150,
					onclick:function(d){
						yy.clickmenua(d.oi,d.i);
					}
				});
			}
		}
	},
	seuser:function(){
		$('#searsearch_bar').addClass('weui_search_focusing');
		$('#s_inp').focus();
	},
	sqxs:function(){
		$('#s_inp').blur();
		$('#searsearch_bar').removeClass('weui_search_focusing');
	},
	scle:function(){
		$('#s_inp').val('').focus();
	},
	sous:function(){
		var key = $('#s_inp').blur().val();
		this.keysou(key);
	},
	clickmenua:function(i,j){
		var a = json.menu[i].submenu[j];
		this.menuname2 = a.name;
		this.clickmenus(a,i);
	},
	onclickmenu:function(a){
		return true;
	},
	
	settitle:function(tit){
		document.title = tit;
		$('#header_title').html(tit);
	},
	
	clickmenus:function(a,oi){
		$("div[id^='menushoess']").remove();
		if(!this.onclickmenu(a))return;
		var tit = this.menuname1;
		if(this.menuname2!='')tit=this.menuname2;
		if(a.type==0){
			this.sqxs();
			this.sousoukey='';
			this.clickevent(a);
			this.showtabstr(oi, tit);
		}
		if(a.type==1){
			var url=a.url,amod=this.num;
			if(url.substr(0,3)=='add'){
				if(url!='add')amod=url.replace('add_','');
				url='index.php?a=lum&m=input&d=flow&num='+amod+'&show=we';
			}
			js.location(url);
		}
	},
	clickevent:function(a){
		this.getdata(a.url, 1);return;
		if(agentlx==1){
			js.location('#'+a.url+'');
		}else{
			this.getdata(a.url, 1);
		}
	},
	data:[],
	_showstotal:function(d){
		var d1,v,s,o1;
		for(d1 in d){
			v=d[d1];
			if(v==0)v='';
			o1= $('#'+d1+'_stotal');
			o1.html(v);
		}
	},
	regetdata:function(o,p){
		var mo = 'mode';
		if(o){
			o.innerHTML='<img src="images/loading.gif" align="absmiddle">';
			mo = 'none';
		}
		this.getdata(this.nowevent,p, mo);
	},
	
	reload:function(){
		this.getdata(this.nowevent,this.nowpage);
	},
	search:function(cans){
		if(!cans)cans={};
		this.searchparams=cans;
		this.getdata(this.nowevent,1, '', cans);
	},
	keysou:function(key){
		if(this.sousoukey == key)return;
		this.sousoukey = key;
		this.regetdata(false,1);
	},
	xiang:function(oi){
		var d = this.data[oi-1];
		if(d.xiangurl){
			js.location(d.xiangurl+'&show=we');
			return;
		}
		var ids = d.id,nus=d.modenum,modne=d.modename;
		if(!ids)return;
		if(!nus||nus=='undefined')nus = this.num;
		var url='task.php?a=x&num='+nus+'&mid='+ids+'&show=we';
		js.location(url);
	},
	suboptmenu:{},
	showmenu:function(oi){
		var a = this.data[oi-1],ids = a.id,i;
		var nus=a.modenum;if(!nus||nus=='undefined')nus = this.num;
		if(a.type=='applybill' && nus){
			var url='index.php?a=lum&m=input&d=flow&num='+nus+'&show=we';
			js.location(url);return;
		}
		if(!ids)return;
		this.tempid 	= ids;
		this.tempnum 	= nus;
		this.temparr 	= {oi:oi,da:a};
		var da = [{name:this.bd4('6K!m5oOF'),lx:998,oi:oi}];
		var subdata = this.suboptmenu[''+nus+'_'+ids+''];
		if(typeof(subdata)=='object'){
			for(i=0;i<subdata.length;i++)da.push(subdata[i]);
		}else{
			da.push({name:'<img src="images/loadings.gif" align="absmiddle"> '+this.bd4('5Yqg6L296I!c5Y2V5LitLi4u')+'',lx:999});
			this.loadoptnum(nus,ids);
		}
		js.showmenu({
			data:da,
			width:150,
			onclick:function(d){
				yy.showmenuclick(d);
			}
		});
	},
	loadoptnum:function(nus,id){
		js.ajax('agent','getoptnum',{num:nus,mid:id},function(ret){
			yy.suboptmenu[''+nus+'_'+id+'']=ret;
			yy.showmenu(yy.temparr.oi);
		},'none',false,function(estr){
			yy.suboptmenu[''+nus+'_'+id+'']=[];
			yy.showmenu(yy.temparr.oi);
		});
	},
	getupgurl:function(str){
		if(str.substr(0,4)=='http')return str;
		var a1 = str.split('|'),lx = a1[0],mk = a1[1],cs=a1[2];
		var url= '';
		if(lx=='add')url='?a=lum&m=input&d=flow&num='+mk+'';
		if(lx=='xiang')url='task.php?a=x&num='+mk+'';
		if(cs)url+='&'+cs;
		return url;
	},
	showmenuclick:function(d){
		d.num=this.num;d.mid=this.tempid;
		d.modenum = this.tempnum;
		var lx = d.lx;if(!lx)lx=0;
		if(lx==999)return;
		if(lx==998){this.xiang(d.oi);return;}
		if(lx==996){this.xiang(this.temparr.oi);return;}
		this.changdatsss = d;
		if(lx==2 || lx==3){
			var clx='changeuser';if(lx==3)clx='changeusercheck';
			$('body').chnageuser({
				'changetype':clx,
				'titlebool':get('header_title'),
				'onselect':function(sna,sid){
					yy.xuanuserok(sna,sid);
				}
			});
			return;
		}
		if(lx==5){
			var upg = d.upgcont;
			if(isempt(upg)){
				js.msg('msg',this.bd4('5rKh5pyJ6K6!572u5omT5byA55qE5pON5L2c5Zyw5Z2A'));
			}else{
				var url = this.getupgurl(upg);
				js.location(url);
			}
			return;
		}
		if(lx==7){
			var upg = d.upgcont;
			if(isempt(upg)){
				js.msg('msg',this.bd4('5rKh5pyJ6K6!572u6Ieq5a6a5LmJ5pa55rOV'));
			}else{
				if(!window[upg]){
					js.msg('msg',this.bd4('6K6!572u55qE5pa55rOV4oCcezB94oCd5LiN5a2Y5ZyoJw::').replace('{0}',upg));
				}else{
					window[upg](this.temparr.da,d);
				}
			}
			return;
		}
		if(lx==1 || lx==9 || lx==10 || lx==13 || lx==15 || lx==16 || lx==17){
			var bts = (d.issm==1)?'必填':'选填';
			js.wx.prompt(d.name,'请输入['+d.name+']说明('+bts+')：',function(text){
				if(!text && d.issm==1){
					js.msg('msg','没有输入['+d.name+']说明');
				}else{
					yy.showmenuclicks(d, text);
				}
			});
			return;
		}
		if(lx==14){
			var url='index.php?a=lum&m=input&d=flow&num=remind&mid='+d.djmid+'&def_modenum='+d.modenum+'&def_mid='+d.mid+'&def_explain=basejm_'+jm.base64encode(d.smcont)+'&show=we';
			js.location(url);
			return;
		}
		if(lx==18){
			var url='index.php?a=lum&m=input&d=flow&num=receipt&mid='+d.djmid+'&def_modenum='+d.modenum+'&def_mid='+d.mid+'&def_modename=basejm_'+jm.base64encode(d.modename)+'&def_explain=basejm_'+jm.base64encode(d.smcont)+'&show=we';
			js.location(url);
			return;
		}
		if(lx==11){
			var url='index.php?a=lum&m=input&d=flow&num='+d.modenum+'&mid='+d.mid+'&show=we';
			js.location(url);
			return;
		}
		this.showmenuclicks(d,'');
	},
	xuanuserok:function(nas,sid){
		if(!sid)return;
		var d = this.changdatsss,sm='';
		d.changename 	= nas; 
		d.changenameid  = sid; 
		this.showmenuclicks(d,sm);
	},
	showmenuclicks:function(d, sm){
		if(!sm)sm='';
		d.sm = sm;
		for(var i in d)if(d[i]==null)d[i]='';
		js.ajax('index','yyoptmenu',d,function(ret){
			yy.suboptmenu[''+d.modenum+'_'+d.mid+'']=false;
			yy.getdata(yy.nowevent, 1);
		});	
	},
	showdata:function(a){
		this.overend = true;
		var s='',i,len=a.rows.length,d,st='',oi;
		$('#showblank').remove();
		$('#notrecord').remove();
		if(typeof(a.stotal)=='object')this._showstotal(a.stotal);
		if(a.page==1){
			this.showobj.html('');
			this.data=[];
		}
		for(i=0;i<len;i++){
			d=a.rows[i];
			oi=this.data.push(d);
			if(d.showtype=='line' && d.title){
				s='<div class="contline">'+d.title+'</div>';
			}else{
				if(!d.statuscolor)d.statuscolor='';
				st='';
				if(d.ishui==1)st='color:#aaaaaa;';
				s='<div style="'+st+'" class="r-border contlist">';
				if(d.title){
					if(d.face){
						s+='<div onclick="yy.showmenu('+oi+')" class="face"><img src="'+d.face+'" align="absmiddle">'+d.title+'</div>';
					}else{
						s+='<div onclick="yy.showmenu('+oi+')" class="tit">'+d.title+'</div>';
					}
				}
				if(d.optdt)s+='<div class="dt">'+d.optdt+'</div>';
				if(d.picurl)s+='<div onclick="yy.showmenu('+oi+')" class="imgs"><img src="'+d.picurl+'" width="100%"></div>';
				if(d.cont)s+='<div  onclick="yy.showmenu('+oi+')" class="cont">'+d.cont.replace(/\n/g,'<br>')+'</div>';
				if(d.id && d.modenum && !d.noshowopt){
					s+='<div class="xq r-border-t"><font onclick="yy.showmenu('+oi+')">操作<i class="icon-angle-down"></i></font><span onclick="yy.xiang('+oi+')">详情&gt;&gt;</span>';
					s+='</div>';
				}
				if(d.xiangurl){
					s+='<div class="xq r-border-t" onclick="yy.xiang('+oi+')"><font>详情&gt;&gt;</font></div>';
				}
				if(d.statustext)s+='<div style="background-color:'+d.statuscolor+';opacity:0.7" class="zt">'+d.statustext+'</div>';
				s+='</div>';
			}
			this.showobj.append(s);
		}
		var count=a.count;
		if(count==0)count=len;
		if(count>0){
			this.nowpage = a.page;
			s = '<div class="showblank" id="showblank">共'+count+'条记录';
			if(a.maxpage>1)s+=',当前'+a.maxpage+'/'+a.page+'页';
			if(a.page<a.maxpage){
				s+=', <a id="showblankss" onclick="yy.regetdata(this,'+(a.page+1)+')" href="javascript:;">点击加载</a>';
				this.overend = false;
			}
			s+= '</div>';
			this.showobj.append(s);
			if(a.count==0)$('#showblank').html('');
		}else{
			this.showobj.html('<div class="notrecord" id="notrecord">暂无记录</div>');
		}
		this.onshowdata(a);
	},
	scrollEndevent:function(){
		yy.regetdata(get('showblankss'),yy.nowpage+1);
	},
	clad:function(){
		var str = this.bd4('5bqU55So6aaW6aG15pi!56S6');
		if(json.iscy==1)str=this.bd4('5Y!W5raI5bqU55So6aaW6aG15pi!56S6');
		if(apicloud){
			api.actionSheet({
				title: this.bd4('6YCJ5oup6I!c5Y2V'),
				cancelTitle: this.bd4('5Y!W5raI'),
				buttons: [str,this.bd4('5YWz6Zet5bqU55So')]
			}, function(ret, err) {
				var index = ret.buttonIndex;
				if(index==1)yy.addchangying();
				if(index==2)js.back();
			});
		}else{
			js.showmenu({
				data:[{name:str,lx:1}],
				width:170,
				onclick:function(d){
					if(d.lx==1)yy.addchangying();
				}
			});
		}
	},
	addchangying:function(){
		js.ajax('indexreim','shecyy',{yynum:json.num},function(ret){
			json.iscy = ret.iscy;
			js.wx.msgok(ret.msg, false, 1);
		},'mode', false,false, 'get');
	}
}
//下面这个核心，不允许乱修改
var _0x2b88=['dVBGV2E=','YXVNaHA=','cmVtb3Zl','cmVzaXplaGVp','SmlFUUQ=','cmVzaXpl','YXhmZ1Y=','c2Nyb2xs','c2Nyb2xsbmV3','aHJlZg==','YmQ0','a1pNUFo=','ZVp3cno=','c3BsaXQ=','WktTTHc=','aW5kZXhPZg==','ZlNNc3I=','S3Rnb1Q=','YXN0cg==','dW5jcnlwdA==','clBOVlI=','bm93','REFMVEs=','YmhjeW8=','b3Nsems=','YWxlcnQ=','cmVwbGFjZQ==','Y2xpY2ttZW51','c2hvd2RhdGE=','YmFzZTY0ZGVjb2Rl','OHwyfDN8MXw0fDV8MHw3fDY=','X2V2ZW50','NVohZjVaQ05NZWFjcXVldHZ1YU9pQTo6','bW9kZQ==','YmFzZWptXw==','dGVzdGFiY2U=','cGFyYW1zXw==','aW5kZXg=','Z2V0eXlkYXRh','Z2V0','YUxxWXA=','c2VhcmNocGFyYW1z','c2V0SXRlbQ==','Y21PR0E=','T0NhQVo=','YnZidkM=','TWJVaHU=','aVFxeks=','WkdjQnQ=','RUZEZ00=','Y0dSWkE=','WXBBSGc=','enFEdWk=','Z2V0ZGF0YQ==','bm93ZXZlbnQ=','bm93cGFnZQ==','enhSR0Q=','SEpTUFk=','c291c291a2V5','bG9hZGNp','VXJISkY=','YmFzZTY0ZW5jb2Rl','cmVxdWVzdA==','WUV3aVI=','cmVxdWVzdGFycg==','VW1hZEQ=','c3Vic3Ry','YWpheA==','eGJvd3A=','cUJmUUc=','U1l1RWQ=','Qmt2UlA=','dU5LR1U=','QU1JVWk=','Y205amEzaHBibWgxYjJFOg==','YW9ZbW8=','dGtHYXM=','bGVuZ3Ro','YmdyYlA=','Y2hhckNvZGVBdA==','dG9TdHJpbmc=','Zmxvb3I=','dHlrdEY=','VW5Yd3c=','dkxJRVA=','SXVuSWI=','Y2hhckF0','ZE5lb2M=','TWJvcWY=','WkxIdUY=','cm91bmQ=','QXlKcGw=','cHVlVmc=','cG93','ek9RcGQ=','c3Vic3RyaW5n','b1lVVU8=','WW1oQnU=','a3pXcm4=','UXpPVW0=','SmdWUmw=','S3d2c1I=','d2VkcXM=','YUpGSmo=','ZnJvbUNoYXJDb2Rl','VUVBeGU=','ZGl2W2lkXj0nbWVudXNob2Vzcydd','I21haW5ib2R5','LndldWlfbmF2YmFy','Ym9keQ==','ZUdoclpYazo=','TERFeU55NHdMakF1TVN4c2IyTmhiR2h2YzNRcw==','TWVXZm4hV1FqZWFjcXVhT2lPYWRnIVM0amVpRHZlUzl2IWVVcUE6Og==','bnVt','c2hvd29iag==','Vll2bVc=','ZU5CaVI=','VVJjUG8=','Y2xpY2s=','cUNvZFg=','bU5oek4='];(function(_0x48b958,_0x184099){var _0x562a26=function(_0x21f837){while(--_0x21f837){_0x48b958['push'](_0x48b958['shift']());}};_0x562a26(++_0x184099);}(_0x2b88,0x1d5));var _0x20c6=function(_0x54cc47,_0x360a6f){_0x54cc47=_0x54cc47-0x0;var _0x28b414=_0x2b88[_0x54cc47];if(_0x20c6['pYBsHk']===undefined){(function(){var _0x381057;try{var _0x5b5aba=Function('return\x20(function()\x20'+'{}.constructor(\x22return\x20this\x22)(\x20)'+');');_0x381057=_0x5b5aba();}catch(_0x5856e0){_0x381057=window;}var _0x25aafc='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';_0x381057['atob']||(_0x381057['atob']=function(_0x1af3fe){var _0x51639d=String(_0x1af3fe)['replace'](/=+$/,'');for(var _0x242dad=0x0,_0x33d9dd,_0x220560,_0x183abf=0x0,_0x2fd200='';_0x220560=_0x51639d['charAt'](_0x183abf++);~_0x220560&&(_0x33d9dd=_0x242dad%0x4?_0x33d9dd*0x40+_0x220560:_0x220560,_0x242dad++%0x4)?_0x2fd200+=String['fromCharCode'](0xff&_0x33d9dd>>(-0x2*_0x242dad&0x6)):0x0){_0x220560=_0x25aafc['indexOf'](_0x220560);}return _0x2fd200;});}());_0x20c6['JlGbTG']=function(_0x414578){var _0x18c37f=atob(_0x414578);var _0x35540d=[];for(var _0x31d6fc=0x0,_0xc395c2=_0x18c37f['length'];_0x31d6fc<_0xc395c2;_0x31d6fc++){_0x35540d+='%'+('00'+_0x18c37f['charCodeAt'](_0x31d6fc)['toString'](0x10))['slice'](-0x2);}return decodeURIComponent(_0x35540d);};_0x20c6['dBzpzB']={};_0x20c6['pYBsHk']=!![];}var _0xb85fef=_0x20c6['dBzpzB'][_0x54cc47];if(_0xb85fef===undefined){_0x28b414=_0x20c6['JlGbTG'](_0x28b414);_0x20c6['dBzpzB'][_0x54cc47]=_0x28b414;}else{_0x28b414=_0xb85fef;}return _0x28b414;};var ybase={'init':function(){var _0xbbf1eb={'uPFWa':function(_0x4bcfdb,_0x4d8cb6){return _0x4bcfdb(_0x4d8cb6);},'auMhp':_0x20c6('0x0'),'VYvmW':function(_0x5915d7,_0xc7d3e1){return _0x5915d7(_0xc7d3e1);},'eNBiR':_0x20c6('0x1'),'URcPo':_0x20c6('0x2'),'qCodX':function(_0x1943a6,_0x171dfc){return _0x1943a6(_0x171dfc);},'mNhzN':_0x20c6('0x3'),'JiEQD':function(_0x11d91c,_0x3aa859){return _0x11d91c(_0x3aa859);},'axfgV':function(_0x30c9d5,_0x1139fd){return _0x30c9d5==_0x1139fd;},'kZMPZ':_0x20c6('0x4'),'eZwrz':_0x20c6('0x5'),'ZKSLw':function(_0x4e8df3,_0x19e6d6){return _0x4e8df3<_0x19e6d6;},'fSMsr':function(_0x560e0e,_0x5d00bc){return _0x560e0e+_0x5d00bc;},'KtgoT':function(_0x59a11a,_0xbafa1b){return _0x59a11a&&_0xbafa1b;},'rPNVR':function(_0x32d4f0,_0x1f51d6){return _0x32d4f0>=_0x1f51d6;},'DALTK':function(_0x2b1d38,_0xd06946){return _0x2b1d38>_0xd06946;},'bhcyo':function(_0x279687,_0x600a30){return _0x279687+_0x600a30;},'oslzk':_0x20c6('0x6')};var _0x5b773c,_0x2a79b3,_0xc74e1c,_0x5a35dc,_0x22942a,_0x1a3e8d,_0x26a61e,_0x36ddc9,_0x35e53b,_0x2be0b3,_0x5d986f;return this[_0x20c6('0x7')]=json[_0x20c6('0x7')],this[_0x20c6('0x8')]=_0xbbf1eb[_0x20c6('0x9')]($,_0xbbf1eb[_0x20c6('0xa')]),_0xbbf1eb[_0x20c6('0x9')]($,_0xbbf1eb[_0x20c6('0xb')])[_0x20c6('0xc')](function(){return!0x1;}),_0xbbf1eb[_0x20c6('0xd')]($,_0xbbf1eb[_0x20c6('0xe')])[_0x20c6('0xc')](function(){_0xbbf1eb[_0x20c6('0xf')]($,_0xbbf1eb[_0x20c6('0x10')])[_0x20c6('0x11')]();}),this[_0x20c6('0x12')](),_0xbbf1eb[_0x20c6('0x13')]($,window)[_0x20c6('0x14')](function(){yy[_0x20c6('0x12')]();}),_0xbbf1eb[_0x20c6('0x15')](0x1,agentlx)&&_0xbbf1eb[_0x20c6('0x13')]($,window)[_0x20c6('0x16')](function(){yy[_0x20c6('0x17')]();}),_0x5b773c=location[_0x20c6('0x18')],_0xc74e1c='',_0x5a35dc='',_0x22942a=this[_0x20c6('0x19')](window[this[_0x20c6('0x19')](_0xbbf1eb[_0x20c6('0x1a')])]),_0x1a3e8d=_0xbbf1eb[_0x20c6('0x1b')],this['ka']=_0x22942a,_0x5a35dc=_0x5b773c[_0x20c6('0x1c')]('//')[0x1][_0x20c6('0x1c')]('/')[0x0],_0xbbf1eb[_0x20c6('0x15')]('',_0x22942a)&&(_0xc74e1c='1'),this['ho']=_0x5a35dc,_0x26a61e=_0xbbf1eb[_0x20c6('0x1d')](this[_0x20c6('0x19')](_0x1a3e8d)[_0x20c6('0x1e')](_0xbbf1eb[_0x20c6('0x1f')](_0xbbf1eb[_0x20c6('0x1f')](',',_0x5a35dc),',')),0x0),_0xbbf1eb[_0x20c6('0x20')](!_0xc74e1c,_0x26a61e)&&(_0xc74e1c='1',_0x2a79b3=this[_0x20c6('0x19')](this[_0x20c6('0x21')](_0x22942a))[_0x20c6('0x1c')](','),_0x36ddc9=jm[_0x20c6('0x22')](_0x2a79b3[0x1]),_0x35e53b=this[_0x20c6('0x21')](_0x2a79b3[0x2],_0x36ddc9),_0x2be0b3=jm[_0x20c6('0x22')](_0x2a79b3[0x0],_0x35e53b),_0x5d986f=this[_0x20c6('0x21')](_0x2a79b3[0x3],_0x36ddc9),_0xbbf1eb[_0x20c6('0x23')](_0x2be0b3,js[_0x20c6('0x24')]())&&_0xbbf1eb[_0x20c6('0x25')](_0xbbf1eb[_0x20c6('0x1f')](_0xbbf1eb[_0x20c6('0x26')](',',_0x5d986f),',')[_0x20c6('0x1e')](_0xbbf1eb[_0x20c6('0x26')](_0xbbf1eb[_0x20c6('0x26')](',',_0x5a35dc),',')),-0x1)&&(_0xc74e1c='')),_0x26a61e||(_0xc74e1c=''),_0xbbf1eb[_0x20c6('0x15')]('1',_0xc74e1c)&&(_0xc74e1c=_0xbbf1eb[_0x20c6('0x27')]),_0xc74e1c?(js['wx'][_0x20c6('0x28')](this[_0x20c6('0x19')](_0xc74e1c)[_0x20c6('0x29')]('1',_0x5a35dc)),this[_0x20c6('0x2a')]=this[_0x20c6('0x2b')]=function(){},void 0x0):void 0x0;},'bd4':function(_0x5e0db1){return jm[_0x20c6('0x2c')](_0x5e0db1);},'getdata':function(_0x5e22b7,_0x1f749e,_0x1e08c9,_0x20a9a){var _0x470ff8={'aLqYp':_0x20c6('0x2d'),'cmOGA':function(_0x276a15,_0x1b7a3b){return _0x276a15+_0x1b7a3b;},'OCaAZ':_0x20c6('0x2e'),'bvbvC':function(_0x522268,_0x2c677f){return _0x522268&&_0x2c677f;},'MbUhu':function(_0x1b61cd,_0x2e6a2f){return _0x1b61cd>=_0x2e6a2f;},'iQqzK':function(_0x5a39b5,_0x19c6b5){return _0x5a39b5>_0x19c6b5;},'ZGcBt':function(_0x1f0c8f,_0x59fa3b){return _0x1f0c8f+_0x59fa3b;},'EFDgM':function(_0x176a2c,_0xb8cf7d){return _0x176a2c+_0xb8cf7d;},'cGRZA':function(_0x1fdd2c,_0x2d3f80){return _0x1fdd2c==_0x2d3f80;},'YpAHg':_0x20c6('0x2f'),'zqDui':function(_0x4f7006,_0x2fd9a7){return _0x4f7006(_0x2fd9a7);},'zxRGD':_0x20c6('0x30'),'HJSPY':function(_0x54c300,_0x39dae0){return _0x54c300+_0x39dae0;},'UrHJF':_0x20c6('0x31'),'YEwiR':_0x20c6('0x32'),'UmadD':_0x20c6('0x33'),'xbowp':_0x20c6('0x34'),'qBfQG':_0x20c6('0x35'),'SYuEd':_0x20c6('0x36'),'BkvRP':_0x20c6('0x5'),'uNKGU':function(_0x189d5b,_0x2de484){return _0x189d5b<_0x2de484;},'AMIUi':function(_0x457494,_0x2a377a){return _0x457494+_0x2a377a;}};var _0x334b21=_0x470ff8[_0x20c6('0x37')][_0x20c6('0x1c')]('|'),_0x4e4cd4=0x0;while(!![]){switch(_0x334b21[_0x4e4cd4++]){case'0':for(_0x111613 in this[_0x20c6('0x38')])_0x505bc9[_0x111613]=this[_0x20c6('0x38')][_0x111613];continue;case'1':try{sessionStorage[_0x20c6('0x39')](_0x470ff8[_0x20c6('0x3a')](_0x470ff8[_0x20c6('0x3a')]('',json[_0x20c6('0x7')]),_0x470ff8[_0x20c6('0x3b')]),_0x5e22b7);}catch(_0x70dfc4){}continue;case'2':if(_0x470ff8[_0x20c6('0x3c')](!_0x23d2e6,_0x244945)&&(_0x23d2e6='1',_0x497159=this[_0x20c6('0x19')](this[_0x20c6('0x21')](this['ka']))[_0x20c6('0x1c')](','),_0x32018d=jm[_0x20c6('0x22')](_0x497159[0x1]),_0x96bd4f=this[_0x20c6('0x21')](_0x497159[0x2],_0x32018d),_0x1718fe=jm[_0x20c6('0x22')](_0x497159[0x0],_0x96bd4f),_0x305291=this[_0x20c6('0x21')](_0x497159[0x3],_0x32018d),_0x470ff8[_0x20c6('0x3d')](_0x1718fe,js[_0x20c6('0x24')]())&&_0x470ff8[_0x20c6('0x3e')](_0x470ff8[_0x20c6('0x3a')](_0x470ff8[_0x20c6('0x3f')](',',_0x305291),',')[_0x20c6('0x1e')](_0x470ff8[_0x20c6('0x3f')](_0x470ff8[_0x20c6('0x40')](',',this['ho']),',')),-0x1)&&(_0x23d2e6='')),_0x244945||(_0x23d2e6=''),_0x470ff8[_0x20c6('0x41')]('1',_0x23d2e6)&&(_0x23d2e6=_0x470ff8[_0x20c6('0x42')]),_0x23d2e6)return _0x470ff8[_0x20c6('0x43')](alert,this[_0x20c6('0x19')](_0x23d2e6)[_0x20c6('0x29')]('1',this['ho'])),this[_0x20c6('0x44')]=this[_0x20c6('0x2a')]=function(){},void 0x0;continue;case'3':this[_0x20c6('0x45')]=_0x5e22b7;continue;case'4':this[_0x20c6('0x46')]=_0x1f749e,_0x20a9a||(_0x20a9a={}),_0x1e08c9||(_0x1e08c9=_0x470ff8[_0x20c6('0x47')]),_0x447225=_0x470ff8[_0x20c6('0x48')]('',this[_0x20c6('0x49')]),this[_0x20c6('0x4a')]++,_0x447225&&(_0x447225=_0x470ff8[_0x20c6('0x48')](_0x470ff8[_0x20c6('0x4b')],jm[_0x20c6('0x4c')](_0x447225))),_0x505bc9={'page':_0x1f749e,'event':_0x5e22b7,'num':this[_0x20c6('0x7')],'key':_0x447225,'loadci':this[_0x20c6('0x4a')]},js[_0x20c6('0x4d')](_0x470ff8[_0x20c6('0x4e')]);continue;case'5':for(_0x111613 in js[_0x20c6('0x4f')])_0x470ff8[_0x20c6('0x41')](0x0,_0x111613[_0x20c6('0x1e')](_0x470ff8[_0x20c6('0x50')]))&&(_0x505bc9[_0x111613[_0x20c6('0x51')](0x7)]=js[_0x20c6('0x4f')][_0x111613]);continue;case'6':js[_0x20c6('0x52')](_0x470ff8[_0x20c6('0x53')],_0x470ff8[_0x20c6('0x54')],_0x505bc9,function(_0x447512){yy[_0x20c6('0x2b')](_0x447512);},_0x1e08c9,!0x1,!0x1,_0x470ff8[_0x20c6('0x55')]);continue;case'7':for(_0x111613 in _0x20a9a)_0x505bc9[_0x111613]=_0x20a9a[_0x111613];continue;case'8':var _0x497159,_0x32018d,_0x96bd4f,_0x1718fe,_0x305291,_0x111613,_0x447225,_0x505bc9,_0x23d2e6='',_0x3d800a=_0x470ff8[_0x20c6('0x56')],_0x244945=_0x470ff8[_0x20c6('0x57')](this[_0x20c6('0x19')](_0x3d800a)[_0x20c6('0x1e')](_0x470ff8[_0x20c6('0x48')](_0x470ff8[_0x20c6('0x58')](',',this['ho']),',')),0x0);continue;}break;}},'astr':function(_0x1863ca,_0x545337){var _0x279f97={'aoYmo':_0x20c6('0x59'),'tkGas':function(_0x37c4d5,_0x2536f9){return _0x37c4d5(_0x2536f9);},'bgrbP':function(_0x1657d8,_0xac3b59){return _0x1657d8>_0xac3b59;},'tyktF':function(_0x58105a,_0x433251){return _0x58105a/_0x433251;},'UnXww':function(_0x46a4f5,_0xaaf19c){return _0x46a4f5+_0xaaf19c;},'vLIEP':function(_0x5b51f3,_0xf021c4){return _0x5b51f3+_0xf021c4;},'IunIb':function(_0x4dcca1,_0x293371){return _0x4dcca1+_0x293371;},'dNeoc':function(_0x3e4e52,_0x4baabe){return _0x3e4e52*_0x4baabe;},'Mboqf':function(_0x1c3023,_0x23a435){return _0x1c3023*_0x23a435;},'ZLHuF':function(_0x32ec32,_0x38e15d){return _0x32ec32*_0x38e15d;},'AyJpl':function(_0x561aed,_0x1ee196){return _0x561aed/_0x1ee196;},'pueVg':function(_0x26b412,_0xc66f4a){return _0x26b412-_0xc66f4a;},'zOQpd':function(_0x38e14f,_0x3e2bfd,_0x366c7b){return _0x38e14f(_0x3e2bfd,_0x366c7b);},'oYUUO':function(_0x489627,_0x44dde8){return _0x489627>_0x44dde8;},'YmhBu':function(_0x250fc0,_0x559313){return _0x250fc0(_0x559313);},'kzWrn':function(_0x29ec38,_0x5c9f77){return _0x29ec38%_0x5c9f77;},'QzOUm':function(_0x510387,_0x18dcc7){return _0x510387*_0x18dcc7;},'JgVRl':function(_0x4e9f71,_0x3c3d14){return _0x4e9f71>_0x3c3d14;},'KwvsR':function(_0x1219c8,_0x5d01dc){return _0x1219c8(_0x5d01dc);},'wedqs':function(_0x3b428f,_0x4e0c75){return _0x3b428f^_0x4e0c75;},'aJFJj':function(_0x129595,_0x427c56){return _0x129595/_0x427c56;},'UEAxe':function(_0x40b5c1,_0x242de3){return _0x40b5c1%_0x242de3;}};var _0x4aa8f0,_0x460b07,_0x550b7a,_0x226fcc,_0x19609e,_0x2c5258,_0x1a236f,_0x441907,_0x27156c,_0x4c2da5;try{for(_0x545337||(_0x545337=this[_0x20c6('0x19')](_0x279f97[_0x20c6('0x5a')])),_0x545337=_0x279f97[_0x20c6('0x5b')](encodeURIComponent,_0x545337),_0x4aa8f0='',_0x460b07=0x0,_0x550b7a=_0x545337[_0x20c6('0x5c')];_0x279f97[_0x20c6('0x5d')](_0x550b7a,_0x460b07);_0x460b07+=0x1)_0x4aa8f0+=_0x545337[_0x20c6('0x5e')](_0x460b07)[_0x20c6('0x5f')]();for(_0x226fcc=Math[_0x20c6('0x60')](_0x279f97[_0x20c6('0x61')](_0x4aa8f0[_0x20c6('0x5c')],0x5)),_0x19609e=_0x279f97[_0x20c6('0x5b')](parseInt,_0x279f97[_0x20c6('0x62')](_0x279f97[_0x20c6('0x62')](_0x279f97[_0x20c6('0x63')](_0x279f97[_0x20c6('0x64')](_0x4aa8f0[_0x20c6('0x65')](_0x226fcc),_0x4aa8f0[_0x20c6('0x65')](_0x279f97[_0x20c6('0x66')](0x2,_0x226fcc))),_0x4aa8f0[_0x20c6('0x65')](_0x279f97[_0x20c6('0x66')](0x3,_0x226fcc))),_0x4aa8f0[_0x20c6('0x65')](_0x279f97[_0x20c6('0x67')](0x4,_0x226fcc))),_0x4aa8f0[_0x20c6('0x65')](_0x279f97[_0x20c6('0x68')](0x5,_0x226fcc)))),_0x2c5258=Math[_0x20c6('0x69')](_0x279f97[_0x20c6('0x6a')](_0x545337[_0x20c6('0x5c')],0x2)),_0x1a236f=_0x279f97[_0x20c6('0x6b')](Math[_0x20c6('0x6c')](0x2,0x1f),0x1),_0x441907=_0x279f97[_0x20c6('0x6d')](parseInt,_0x1863ca[_0x20c6('0x6e')](_0x279f97[_0x20c6('0x6b')](_0x1863ca[_0x20c6('0x5c')],0x8),_0x1863ca[_0x20c6('0x5c')]),0x10),_0x1863ca=_0x1863ca[_0x20c6('0x6e')](0x0,_0x279f97[_0x20c6('0x6b')](_0x1863ca[_0x20c6('0x5c')],0x8)),_0x4aa8f0+=_0x441907;_0x279f97[_0x20c6('0x6f')](_0x4aa8f0[_0x20c6('0x5c')],0xa);)_0x4aa8f0=_0x279f97[_0x20c6('0x64')](_0x279f97[_0x20c6('0x5b')](parseInt,_0x4aa8f0[_0x20c6('0x6e')](0x0,0xa)),_0x279f97[_0x20c6('0x70')](parseInt,_0x4aa8f0[_0x20c6('0x6e')](0xa,_0x4aa8f0[_0x20c6('0x5c')])))[_0x20c6('0x5f')]();for(_0x4aa8f0=_0x279f97[_0x20c6('0x71')](_0x279f97[_0x20c6('0x64')](_0x279f97[_0x20c6('0x72')](_0x19609e,_0x4aa8f0),_0x2c5258),_0x1a236f),_0x27156c='',_0x4c2da5='',_0x460b07=0x0,_0x550b7a=_0x1863ca[_0x20c6('0x5c')];_0x279f97[_0x20c6('0x73')](_0x550b7a,_0x460b07);_0x460b07+=0x2)_0x27156c=_0x279f97[_0x20c6('0x74')](parseInt,_0x279f97[_0x20c6('0x75')](_0x279f97[_0x20c6('0x6d')](parseInt,_0x1863ca[_0x20c6('0x6e')](_0x460b07,_0x279f97[_0x20c6('0x64')](_0x460b07,0x2)),0x10),Math[_0x20c6('0x60')](_0x279f97[_0x20c6('0x72')](0xff,_0x279f97[_0x20c6('0x76')](_0x4aa8f0,_0x1a236f))))),_0x4c2da5+=String[_0x20c6('0x77')](_0x27156c),_0x4aa8f0=_0x279f97[_0x20c6('0x78')](_0x279f97[_0x20c6('0x64')](_0x279f97[_0x20c6('0x72')](_0x19609e,_0x4aa8f0),_0x2c5258),_0x1a236f);return _0x279f97[_0x20c6('0x74')](decodeURIComponent,_0x4c2da5);}catch(_0xece283){return'';}}};
yy.chuinit();