function check(checkbox,type) {
	new Spec(checkbox,type).chkforsel();
}
function Spec(checkbox,type) {this.checkbox = $(checkbox),this.type = type;}
Spec.prototype =  {
	specid:'',specvalid:'',specvalname:'',specvalimg:'',type:'',
	checkbox:{},span:{},img:{},li:{},
	chkforsel:function() {
		//用户点击checkbox
		var checked = this.checkbox.prop('checked');
		//设置值信息
		this.setspec();
		if(checked) {
			//显示可自定义上传图片
			this.type == "pic" && this.showUploadPic();
			this.specvalname = $.trim(this.span.text());
			//设置名称可输入
			this.setSpanWrite();
			//增加可选择specbox
			this.setSpecBox();
		} else {
			this.chkcancel();		
		}
	},
	chkcancel:function() {
		if(this.type == 'pic') {
			$("#pic_"+this.specid+"_"+this.specvalid).hide();
		}
		this.span.text($.trim(this.span.find("input").val()));
		$("#specbox_"+this.specid+"_"+this.specvalid).remove();
	},
	setspec:function() { 
		var	val = this.checkbox.val(),index		= val.indexOf('_');
		//赋值specid,specvalid
		this.specid		= val.substr(0,index);
		this.specvalid	= val.substr(index+1);

		//赋值span
		if(this.type == "pic") {//图片类型
			//赋值img
			this.img		= this.checkbox.next('img');
			if($("#uploadpic_"+this.specvalid).find("img").length) {
				this.specvalimg = $("#uploadpic_"+this.specvalid).find("img").attr("src");
			} else {
				this.specvalimg = this.img.attr("src");
			}
			this.span = this.img.next("span");
			
		} else if(this.type == "text") {	//文字类型
			this.span = this.checkbox.next("span");
		}
	},
	showUploadPic:function() {//显示可自定义图片
		$("#pic_"+this.specid+"_" + this.specvalid).show();
		createupload("upload_"+this.specid+"_"+this.specvalid);
	},
	setSpanWrite:function() { //设置spec name可以修改
		var $input = $("<input type='text' class='input_tx short' />");//定义文本框属性
		$input.attr("id","input_"+this.specid+"_"+this.specvalid);
		$input.val(this.specvalname);
		$input.attr("name","self["+this.specid+"]["+this.specvalid+"]");
		$input.blur($.proxy(this.setSpanWriteOk,this)); //文本框失去焦点执行
		this.span.html($input);
	},
	setSpanWriteOk:function(event) { //文本框失去焦点执行
		var $curinput = $("#" + event.currentTarget.id),val = $curinput.val();
		if(this.type == "pic") {
			$("#specbox_"+this.specid+"_"+this.specvalid).find("img").attr("title",val);
			$("#products .forspecnone_"+this.specid).find("img").attr("title",val);
			$("#name_"+this.specvalid).text(val);
		} else if(this.type == "text") {
			$("#specbox_"+this.specid+"_"+this.specvalid).find("a").text(val);
			$("#products .forspecnone_"+this.specid).find("a").text(val);
		}
	},
	setSpecBox:function() {
		this.li = $("<li></li>");
		this.li.attr("id","specbox_"+this.specid+"_"+this.specvalid);
		$a = $("<a href='javascript:void(0)'></a>");
		$a.click($.proxy($.specval.ok,$.specval));
		if(this.type == "pic") {
			$img = $("<img width='30' height='30' src='"+this.specvalimg+"' class='mid img_"+this.specid+"_"+this.specvalid+"' title='"+this.specvalname+"'/>");
			$a.append($img);
		} else if(this.type == "text") {
			$a.append(this.specvalname);
		}
		this.li.append($a);
		$("#specbox_"+this.specid).append(this.li);
	}
};
(function($) {
	$.specval = {
		specbox:$("#specbox"),specvalbox:{},
		curtarget:{},curtd:{},curtr:{},specs:{},//对应当前的点击a,td,tr,一共有几个specs
		specnum:'',
		left:'',top:'',xwidth:'',xleft:'',xtop:'',
		select:function(curtarget,specid){
			//设置specbox的显示
			this.setProperty(curtarget,specid);
			//获取notallow，设置
			var notallow = this.getNotAllow();
			this.specvalbox.find("li").each(function(){
				var $this = $(this),id = $this.attr("id"),specvalid=id.substr(id.lastIndexOf('_')+1);
				$.inArray(specvalid,notallow) != -1 && $this.find("a").addClass("notallow") || $this.find("a").removeClass("notallow");
			});
			//显示
			this.specbox.find("ul").hide().end().css({left:this.xleft,top:this.xtop}).show();
			this.specvalbox.show();
		},
		ok:function(event) {
			var $ele = $(event.currentTarget);
			if($ele.hasClass("notallow")) return ;
			var id = $ele.parent().attr("id"),specvalid = id.substr(id.lastIndexOf('_')+1);
			this.curtarget.html($ele.html()).next("input").val(specvalid);
			this.specbox.hide();
		},
		setProperty:function(curtarget,specid) { //设置specbox的显示
			this.curtarget = $(curtarget);
			this.curtd     = this.curtarget.parent(),
			this.curtr	   = this.curtd.parent(),
			this.curclass  = this.curtd.attr("class"),
			this.specs	   = this.curtr.find("td[class^='forspecnone_']");
			this.specvalbox = $("#specbox_"+specid);
			this.specnum   = this.specs.length;
			var offset     = this.curtarget.offset();
			this.left	   = offset.left,this.top = offset.top;
			this.xwidth	   = this.specbox.width();
			this.xleft	   = (this.left - this.xwidth / 2 ) + "px";
			this.xtop	   = (this.top + 40) + "px";
		},
		getNotAllow:function() { //获取不允许的选项
			var notallow = [];
			var curpro = this.getCurPro();
			if($.isEmptyObject(curpro)) return notallow;
			var allpros = this.getAllPros();
			if(!allpros.length) return notallow;
			for(var i = 0;i<allpros.length;i++) {
				var thesame = true;
				for(var key in curpro) {
					if(curpro[key] != allpros[i][key] && key != this.curclass ) {
						thesame = false;
						break;
					}
				}
				thesame && notallow.push(allpros[i][this.curclass]);
			}
			return notallow;
		},
		getCurPro:function() {//获取当前被点击的货品
			var curpro = {};
			for(var i =0;i<this.specnum;i++) {
				var key = this.specs.eq(i).attr("class"),val = this.specs.eq(i).find("input").val();
				if((key!=this.curclass) && (val == "")) return {};
				curpro[key] = val;
			}
			return curpro;
		},
		getAllPros:function() {	//获取当前所有的货品
			var $products  = $("#products").find("tr"),
				allpros	   = [];
			$products.each(function() { //解析所有的货品
				var pro = {},$this = $(this);
				$this.find("td[class^='forspecnone_']").each(function() {//货品对应的spec
					var $ele = $(this),key = $ele.attr("class"),val = $ele.find("input:hidden").val();
					if(!val) { 
						pro = {};
						return false; //所有spec都选择后，组成货品
					}
					pro[key] = val;
				});
				!$.isEmptyObject(pro) && allpros.push(pro); //加入货品
			});
			return allpros;
		}
	}
})(jQuery);

function cal(type) {
	var classname	= "cal" + type;
		$ele		= $("#products").find("."+classname);
	if(type == "inventory") {
		var all = 0;
		$ele.each(function() {
			all += parseInt($(this).val());
		});
		$("#inventory").val(all);
	} else if(type == "price") {
		var maxprice = 0;
		$ele.each(function() {
			var val = parseFloat($(this).val());
			if(val > maxprice) {
				maxprice = val;
			}
		});
		$("#price").val(maxprice.toFixed(2));
	}
}

var ItemSelect = {
	popurl:'',
	setPopUrl:function(url) {
		this.popurl = url;
	},
	selectitem:function(itemid) {
		var ele = $("#listtbody").find("input:checked");
		var ids = $.util.checkedval(ele);
		$(ids).each(function(k,id){
			$html = $("#forrelnone").clone();
			$html.find(".forimg").html($("#img_"+id).html());
			$("#reltb").append($html.html().replace(new RegExp("{itemid}","gm"),id)).show();
		});
		$.tbox.close();
	},
	popitem:function() {
		$.tbox.popup(this.popurl);
	}
};

function togglespec(obj) {
	var $this = $("#forspec"),$obj = $(obj);
	$this.toggle();
	$obj.text($this.is(":hidden")?"开启规格":"关闭规格");
}

function formcheck() {
	var $alltr = $("#products").find("tr"),cansubmit = true;
	$alltr.each(function() {
		var $this = $(this),$allinput = $this.find("input");
		$allinput.each(function(){
			if(!$(this).val()) {
				cansubmit = false;
				jAlert("规格属性尚未填写完全！","警告");
				return false;
			}
		});
		if(!cansubmit) return false;
	});
	return cansubmit;
}
function addProduct() {
	var $html = $("#forspecnone").clone();
	$("#products").append($html.html());
}
function onComplete(id, fileName, data) {
	if(data.err){
		jAlert(data.err,"警告");
	} else {
		console.log(data);
		var buttonid  = data.buttonid,
			buttonarr = buttonid.split("_"),
			specid	  = buttonarr[1],
			specvalid = buttonarr[2],
			img		  = data.msg;
		$("#uploadpic_" + specvalid).html("<img src='"+img+"_50x50.jpg' width='50' height='50'/>");
		$("#uploadval_" + specvalid).val(img);
		$(".img_"+specid+"_"+specvalid).each(function(){
			$(this).attr('src',img+"_50x50.jpg");
		});
	}	
}
function onItemComplete(id,filename,data) {
	if(data.err) {
		jAlert(data.err,"警告");
	} else {
		var img = data.msg;
		$html = $("#forimgnone").clone();
		$html.find(".forimg").html("<img src='"+img+"_50x50.jpg' width='50' height='50'/>");
		$html.find(".forval").val(img);
		$("#imgtd").append($html.html());
	}
}

function itemupload(url) {
	var itemupload = new qq.FileUploader({ //申明一个upload
		 element: document.getElementById('buttonholder'),
		 action: url,
		 onComplete:onItemComplete,
		 multiple:true
	});
}
