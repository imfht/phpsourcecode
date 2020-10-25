$(function() {
	Img = {
		width   : 66,
		slide   : $("#slide"),
		slideul	: $("#slide ul"),
		slideli : $("#slide li"),
		imgnum	: 0,
		pic		: $("#pic"),
		picimg	: $("#picimg"),
		init:function() {
			var width = parseInt(this.slideli.outerWidth(true)) || 0;
			this.setWidth(width);
			this.setMouse();
			this.setClick();
			this.pic.jqzoom();
		},
		setWidth:function(width) {
			this.width	= parseInt(width) || 0;
			this.imgnum = parseInt(this.slideli.length) || 0;
			if(this.imgnum) {
				this.slideul.css("width",this.width * this.imgnum);
			}
		},
		setMouse:function() {
			var _this = this;
			this.slide.mouseover(function(ev){
				if(ev.target.nodeName.toLowerCase() == "img") {
					var $target = $(ev.target),
						$parent = $target.parent(),
						src     = $target.attr('src').replace('_50x50.jpg','');
					_this.picimg.attr("src",src + "_310x310.jpg");
					_this.pic.attr("href",src);
					var ele = _this.pic[0];
					ele.largeimageloaded = false;
					$parent.css("border-color","#ff6600");
				}
			}).mouseout(function(ev){
				if(ev.target.nodeName.toLowerCase() == "img") {
					var $target = $(ev.target),
						$parent = $target.parent();
					$parent.css("border-color","#E4E4E4");
				}
			});
		},
		setClick:function() {
			var _this = this;
			$("#prevBtn a").click(function(){
				var mleft	= parseInt(_this.slideul.css("marginLeft")) || 0;
				if(mleft % _this.width !=0) {
					_this.slideul.animate({marginLeft:0});
					return ;
				}
				var leftnum = Math.abs(mleft / _this.width);
				if(leftnum>0) {
					_this.slideul.animate( {marginLeft:mleft + _this.width} );
				}
			});
			$("#nextBtn a").click(function(){
				var mleft   = parseInt(_this.slideul.css("marginLeft")) || 0;
				if(mleft % _this.width !=0) {
					_this.slideul.animate({marginLeft:0});
					return ;
				}
				var leftnum = Math.abs(mleft / _this.width) + 4;
				if(_this.imgnum > leftnum) {
					_this.slideul.animate({marginLeft:mleft - _this.width});
				}
			});
		}
	},
	Item = {
		url:'',
		qaurl:'',
		commenturl:'',
		itemid:0,
		buynum:$("#buynum"),
		shareopt:{},
		init:function(options) {
			this.url		= options.url;
			this.qaurl		= options.qaurl;
			this.commenturl = options.commenturl;
			this.itemid		= options.itemid;
			this.shareopt	= options.share;
			this.ajaxget('sale');
			this.ajaxget('qa');
			this.ajaxget('comment');
		},
		ajaxget:function(type) {
			var getdata = {itemid:this.itemid,t:new Date().getTime(),type:type};
			$.get(this.url,getdata,function(data){
				$("#for"+type).html(data);
			});
		},
		opernum:function(type) {
			var num = parseInt(this.buynum.val()) || 1;
			if(type == 'jia') {
				num += 1;
			} else if(type == 'jian') {
				num = num>1?num-1:1;
			}
			this.buynum.val(num);
		},
		tab:function(type,obj) {
			var $parent = $(obj).parent();
			$parent.siblings().removeClass("on");
			if(!$parent.hasClass("on")) $parent.addClass("on");
			$("#infotab,#saletab,#qatab,#commenttab").hide();
			$("#"+type+"tab").show();
		},
		chgimg:function(id) {
			$("#"+id).attr("src",'util.php?action=seccode&t='+new Date().getTime());
			return false;
		},
		share:function(web) {
			var url		= "",
				title	= this.shareopt.title,
				content = this.shareopt.content,
				img		= this.shareopt.img,
				itemurl = this.shareopt.itemurl;
			if(web == "qzone") {
				url = "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?"
					+ "title="+content+"&url="+encodeURIComponent(itemurl)+"&pics="+img;
			} else if(web=="tsina") {
				url = "http://v.t.sina.com.cn/share/share.php?searchPic=false&"
					+ "title="+encodeURIComponent(content)+"&pic="+img +"&url="+itemurl;
			} else if(web == "kaixin") {
				url = "http://www.kaixin001.com/repaste/share.php?"
					+ "rtitle="+title+"&rcontent="+content+"&rurl="+itemurl;
			} else if(web == "tqq") {
				url = "http://v.t.qq.com/share/share.php?"
					+ "title="+content+"&pic="+img+"&url="+itemurl;
			} else if(web == "msn") {
				url = "http://profile.live.com/badge/?"
					+ "url="+itemurl +"&title="+title+"&description="+content+"&screenshot="+encodeURIComponent(img);
			} else if(web == "douban") {
				url = "http://www.douban.com/recommend/?"
					+ "title="+content+"&url="+itemurl;
			} else if(web == "renren") {
				url = "http://share.renren.com/share/buttonshare/post/1004?"
					+ "title="+title+"&content="+content+"&pic="+img+"&url="+itemurl;
			}
			if(url) {
				window.open(encodeURI(url),"","height=500,width=600");
			}
		},
		saveQa:function(btn) {
			var $qaseccode	= $("#qaseccode"),
				$content	= $("#qacont"),
				qaseccode	= $.trim($qaseccode.val()),
				content		= $.trim($content.val()),
				can			= true;
			
			if(!content) {
				$content.addClass('hintborder');
				can = false;
			}
			if(!qaseccode) {
				$qaseccode.addClass('hintborder');
				can = false;
			}
			if(!can) {
				this.qachgimg();
				return false;
			}
			var _this = this,
				$btn  = $(btn);
			$.post(this.qaurl,{qaseccode:qaseccode,content:content,itemid:this.itemid},function(data) {
				$content.val('');
				$qaseccode.val('');
				_this.qachgimg();
				jAlert(data.text,"警告");
			},"json");
		},
		saveComment:function(btn) {
			var $content = $("#commentcont"),
				content  = $.trim($content.val()),
				$score	 = $("input[name='score']:checked"),
				can		 = true;
			if(!content) {
				$content.addClass('hintborder');
				can = false;
			}
			if(!can) return false;
			var _this	= this,
				$btn	= $(btn);
			$.post(this.commenturl,{content:content,itemid:this.itemid,score:$score.val()},function(data){
				$content.val('');
				jAlert(data.text,"警告");
			},"json");
		},
		removeborder:function(obj) {
			$(obj).removeClass('hintborder');
		},
		upper:function(obj) {
			obj.value=obj.value.toUpperCase();
		},
		showseccode:function(id) {
			var $obj = $('#'+id);
			$obj.next().show();
			$obj.removeClass('hintborder');
			this.chgimg(id+'img');
		}
	};
});

var Product = {
	products:null,//所有货品
	itemid:null,
	type:'',imgsrc:'',text:'',
	target:{},			//当前被点击a
	parent:{},			//当前被点击li
	selectspec:{},		//当前已经选择
	specid:'',			//当前点击的specid
	specvalid:'',		//当前点击的specvalid
	specnum:0,			//所有规格
	pro: {},			//当前选择货品
	ssize:50,			//小图
	bsize:310,			//大图
	dis:false,			//是否折扣
	tuan:false,			//是否团购
	compriceurl:false,	//投诉价格
	init:function(options) {
		try{
			this.products = eval('(' + options.productstr + ')');
		}catch(ex) {
			this.products = {};
		}
		this.itemid		 = options.itemid;
		this.specnum	 = parseInt(options.specnum) || 0;
		this.favorurl	 = options.favorurl;
		this.notifyurl	 = options.notifyurl;
		this.carturl	 = options.carturl;
		this.dis		 = parseInt(options.dis) || 0;
		this.tuan		 = parseInt(options.tuan) || 0;
		this.compriceurl = options.compriceurl;
	},
	select:function(obj,type) {
		this.target		= $(obj);//当前点击元素
		this.parent		= this.target.parent();
		this.type		= type;  //当前点击类型,pic,text
		//选择该元素
		this.parent.siblings().removeClass("selected").end().addClass('selected');
		this.setSpec();
		//改变图片
		if(this.type == "pic") {
			this.imgsrc	= this.target.css('background-image').replace(/\"/g,"").replace(/url\(|\)$/ig, "");
			this.chgpic();
		}
		//设置按钮的选择与不可选择
		this.setAllow();
		if(this.checkCanBuy()) {
			this.hideHint();
			this.setPro();
		}
	},
	setSpec:function() {
		//当前选择元素的specvalid,specid,加入selectspec
		this.specvalid = this.target.attr("id").replace('specval_','');
		this.specid	   = this.parent.parent().attr("id").replace('spec_','');
		this.selectspec[this.specid] = this.specvalid;
	},
	chgpic:function() {//改变产品图片
		if(this.target.hasClass('notallow')) return ;
		var simg = "_" + this.ssize + "x" + this.ssize + ".jpg",
			bimg = "_" + this.bsize + "x" + this.bsize + ".jpg";

		var src = this.imgsrc.replace(simg,'');
		$("#picimg").attr("src" , src + bimg);
		$("#pic").attr("href",src);
		$("#pic")[0].largeimageloaded = false;
	},
	setAllow:function() {//设置allow数组
		if(this.target.hasClass('notallow')) return ;
		var allow = this.getAllow();
		for(var k in allow) {
			$("#spec_" + k).find("a").each(function() {
				var $this = $(this),specvalid = $this.attr("id").replace('specval_','');
				($.inArray(specvalid,allow[k]) != -1) && $this.parent().removeClass("notallow") || $this.parent().addClass('notallow');
			});
		}
	},
	getAllow:function(){//返回allow数组
		var p		= true,
			allow	= {},
			_this	= this;
		$.each(this.products,function(i,v) {
			p = true;
			for(var k in _this.selectspec) { //判断产品相似部分
				if(v['spec'][k] != _this.selectspec[k]) {
					p = false;
					break;
				}
			}
			if(!p) return true;
			for(var k in v['spec']) {
				if( k == _this.specid ) continue;
				if(typeof allow[k] == "undefined") {
					allow[k] = [v['spec'][k]];
				} else if($.inArray(v['spec'][k],allow[k]) == -1) {
					allow[k].push(v['spec'][k]);
				}
			}
		});
		return allow;
	},
	setPro:function() {
		var _this = this;
		$.each(this.products,function(i,v) {
			var thepro = true;
			for(var k in v['spec']) {
				if(v['spec'][k] != _this.selectspec[k]) {
					thepro = false;
					break;
				}
			}
			if(thepro) {
				_this.pro = v;
				return false;
			}
		});
		$("#inventory").text(_this.pro.inventory);
		if(_this.pro.inventory == 0) { //如果库存为0
			$(".button_car").hide();
			$(".button_notice").show();
		} else { //库存不为0
			$(".button_car").show();
			$(".button_notice").hide();
		}
		if(_this.tuan) return true;
		if(_this.dis) {//如果是价格
			$("#disprice").text(_this.pro.price);
		} else {
			$("#price").text(_this.pro.price);
		}
	},
	checkCanBuy:function() {
	   return this.specnum == $.util.objectLen(this.selectspec)
	},
	getPostData:function (){
		if(typeof this.pro.productid != "undefined") {
			return {productid:this.pro.productid};
		} else {
			return {itemid:this.itemid};
		}
	},
	addCart:function(){ //
		if(this.checkCanBuy()) { //如果可以购买
			var postdata	= this.getPostData();
			postdata['num'] = parseInt($("#buynum").val());
			$.tbox.popup(this.carturl,"POST",postdata);
		} else {
			this.showHint();
		}
	},
	addFavor:function() {
		$.tbox.popup(this.favorurl,"POST",{itemid:this.itemid});
	},
	addNotify:function(type) {
		var postdata	= this.getPostData();
		postdata['type'] = type;
		$.tbox.popup(this.notifyurl,"POST",postdata);
	},
	addComprice:function() {
		var postdata	= this.getPostData();
		$.tbox.popup(this.compriceurl,"GET",postdata);
	},
	showHint:function() {
		$(".buyinfo").addClass('nobuy');
		$(".buybutton").hide();
		$(".spechint").removeClass('none');
	},
	hideHint:function() {
		$(".buyinfo").removeClass('nobuy');
		$(".spechint").addClass('none');
		$(".buybutton").show();
	}
}
