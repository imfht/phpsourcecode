/*----------------------------注释结束--程序开始-----------------------------------------------------------*/
window.ShearPhoto.MINGGE(function() {
	//██████████重要设置████████████████
	var relativeUrl = "/public/plugs/avatar"; //你不要在后面加斜杠，系统会自动给你加上斜杠，不信看下面！   index.html的JS引用路径自己改，很简单的说

	//█████████重要设置█████████████████
	relativeUrl = relativeUrl.replace(/(^\s*)|(\s*$)/g, ""); //去掉相对路径的所有空格
	relativeUrl === "" || (relativeUrl += "/"); //在相对地址后面加斜框，不需要用户自己加
	var publicRelat = document.getElementById("relat"); //"relat"对像     
	var publicRelatImg = publicRelat.getElementsByTagName("img"); //"relat"下的两张图片对像   
	var Shear = new ShearPhoto;
	Shear.config({
		/*---------------用户设置部份开始-----------------------------------------------------------------------*/
		relativeUrl: relativeUrl, //取回相对路径，不懂原理的话，你不要改动哦，否则你又鸡巴痛了

		traverse: true, //可选 true,false 。 是否在拖动或拉伸时允许历遍全图（是否让大图动呢）,

		/*HTML5重点功能*/
		translate3d: false, //是否开启3D移动，CPU加速。可选true  false。默认关闭的，作者认为PC端没必要！在PC端开启后，有部份浏览器页面走位的问题。主要是各大浏览器不统一所致，手机端效果会明显！PC端没什么感觉。 原来是采用left top进行定位的，那么3D移动就是CSS3的translate3d属性。去百度一下translate3D吧

		/*HTML5重点功能*/
		HTML5: true, //可选 true,false  是否使用HTML5进行切图 ，支持HTML5浏览器会使用HTML5进行切图，没有HTML5浏览器则采用原始的方式(先上传大图再截取)，SHEARPHOTO这个方案无可挑剔了吧！

		/*HTML5重点功能*/
		HTML5MAX: 500, //默认请设0 (最大尺寸做事)， HTML上传截图最大宽度， 宽度越大，HTML5截出来的图片容量越大，服务器压力就大，截图就更清淅！ 设得越小 HTML5截出来的图片容量越小.但是造成一定程序的不清淅，请适量设置 当然开启HTML5切图，该设置才有效

		/*HTML5重点功能*/
		HTML5Quality: 0.9, //截好的截图  0至1范围可选（可填小数）   HTML5切图的质量   为1时 最高	，当然开启HTML5切图，该设置才有效,设得越高，越清淅，但文件体积越大，同上！		

		/*HTML5重点功能*/
		HTML5FilesSize: 50, //如果是HTML5切图时，选择的图片不能超过多少，单位M，,你设大点都不怕，        ------因为HTML5ZIP会对原图进行压缩处理

		/*HTML5重点功能*/
		HTML5Effects: true, //是否开启图片特效功能给用户  可选true false,  提示：有HTML5浏览器才会开启的！当然开启HTML5切图，该设置才有效

		/*HTML5重点功能*/
		HTML5ZIP: [900, 0.9], //HTML5截图前载入的大图 是否压缩图片(数组成员 是数字) ，如果不压缩的话填false，在处理特效时或者拉伸时会明显出卡顿,不流畅！官方强烈建意你进行设置 ，默认填写的是[900,0.9] ,代表宽和高都不能大于900，质量是0.9（最大是1） 

		/*记住 preview (预览图片功能) 尽量设false*/

		preview: [150], // 开启动态预览图片 (数组成员整数型，禁止含小数点 可选false 和数组)     数组内是宽度设置，没有高度设！因为高度会按比例做事 ，此设置代表预览150 大小的预览图（你可以增加多个预览图,如[100,70,50]），设置越多预览图,shearphoto性能越差！官方不建意你开启这个功能，尽可能请设为preview:false

		/*记住 preview 尽量设false*/

		url: "/user/upload/avatar", //后端处理地址，保证正确哦，这是常识，连这个地址都能写错，你就是菜B，已经在本版本中帮你加入相对路径，你基本不用改这里了

		scopeWidth: 500, //可拖动范围宽  也就是"main"对象的初始大小(整数型，禁止含小数点) 宽和高的值最好能一致  

		scopeHeight: 500, //可拖动范围高  也就是"main"对象的初始大小(整数型，禁止含小数点) 宽和高的值最好能一致      

		proportional: [1 / 1, <!--截框的宽高比例（宽除以高的比例值，这个设置其实就是1，对！你可以直接写1  如填3/4 那么就是0.75的比例,不设比例请设为0，注意更改比例后，后端也要进行相应设置，否则系统会给你抱出错误-->
			/*
			2.3版本加了一个新API ，动态修改比例！接口示例：Shear.SetProportional(3/4);  意思就是：动态修改比例为3/4;
			*/
			100, //必须整数！启动后的截框初始宽度(整数型，禁止含小数点)  

			133 //比例设置后，这个高度无效，由宽和比例来决定(整数型，禁止含小数点)  
		],

		Min: 50, //截框拉伸或拖拽不能少于多少PX(整数型，禁止含小数点)  

		Max: 500, //一开始启动时，图片的宽和高，有时候图片会很大的，必须要设置一下(整数型，禁止含小数点)，尽可能和scopeWidth值 一致  

		backgroundColor: "#000", //遮层色

		backgroundOpacity: 0.6, //遮层透明度-数字0-1 可选

		Border: 0, //截框的边框大小 0代表动态边框。大于0表示静态边框，大于0时也代表静态边框的粗细值

		BorderStyle: "solid", //只作用于静态边框，截框的边框类型，其实是引入CSS的border属性，和CSS2的border属性是一样的

		BorderColor: "#09F", //只作用于静态边框，截框的边框色彩
		/*---------------用户设置截图功能部份..还没结束----------------------页面下面还有一些细节设置，去看一下-------------------------------------------------*/
		relat: publicRelat, //请查看 id:"relat"对象 
		scope: document.getElementById("main"), //main范围对象 
		ImgDom: publicRelatImg[0], //截图图片对象（小）  
		ImgMain: publicRelatImg[1], //截图图片对象（大）
		black: document.getElementById("black"), //黑色遮层对象
		form: document.getElementById("smallbox"), //截框对象
		ZoomDist: document.getElementById("ZoomDist"), //放大工具条,可从HTML查看此对象，不作详细解释了
		ZoomBar: document.getElementById("ZoomBar"), //放大工具条，可从HTML查看此对象
		to: {
			BottomRight: document.getElementById("BottomRight"), //拉伸点中右
			TopRight: document.getElementById("TopRight"), //拉伸点上右，下面如此类推，一共8点进行拉伸,下面不再作解释
			Bottomleft: document.getElementById("Bottomleft"),
			Topleft: document.getElementById("Topleft"),
			Topmiddle: document.getElementById("Topmiddle"),
			leftmiddle: document.getElementById("leftmiddle"),
			Rightmiddle: document.getElementById("Rightmiddle"),
			Bottommiddle: document.getElementById("Bottommiddle")
		},
		Effects: document.getElementById("shearphoto_Effects") || false,
		DynamicBorder: [document.getElementById("borderTop"), document.getElementById("borderLeft"), document.getElementById("borderRight"), document.getElementById("borderBottom")],
		SelectBox: document.getElementById("SelectBox"), //选择图片方式的对象
		Shearbar: document.getElementById("Shearbar"), //截图工具条对象
		UpFun: function() { //鼠标健松开时执行函数
			Shear.MoveDiv.DivWHFun(); //把截框现时的宽高告诉JS    
		}

	});
	/*--------------------------------------------------------------截图成功后，返回来的callback-------------------------*/
	Shear.complete = function(serverdata) { //截图成功完成时，由shearphoto.php返回数据过来的成功包
			// alert(serverdata);//你可以调试一下这个返回包
			var point = this.arg.scope.childNodes[0];
			point.className === "point" && this.arg.scope.removeChild(point);
			var complete = document.createElement("div");
			complete.className = "complete";
			complete.style.height = this.arg.scopeHeight + "px";
			this.arg.scope.insertBefore(complete, this.arg.scope.childNodes[0]);
			var length = serverdata.length,
				creatImg;
			for (var i = 0; i < length; i++) {
				creatImg = document.createElement("img");
				complete.appendChild(creatImg);
				creatImg.src = serverdata[i]["ImgUrl"];
			}
			this.HTML5.EffectsReturn();
			this.HTML5.BOLBID && this.HTML5.URL.revokeObjectURL(this.HTML5.BOLBID);
			creatImg = document.createElement("DIV");
			creatImg.className = "completeTxt";
			creatImg.innerHTML = '<strong><i></i>恭喜你！截图成功</strong> <p>以上是你图片的' + length + '种尺寸</p><a href="javascript:;" id="completeA">完成</a>';
			complete.appendChild(creatImg);
			var completeA = document.getElementById("completeA");
			var this_ = this;
			this_.preview.close_();
			completeA.onclick || (completeA.onclick = function() {
				completeA.onclick = null;
				this_.arg.scope.removeChild(complete);
				this_.again();
				this_.pointhandle(3e3, 10, "截图完成！已返回！", 2, "#fbeb61", "#3a414c");
			});
		}
		/*--------------------------------------------------------------截图成功后，返回来的callback-------------------------*/

	/*.................................................选择图片上传的设置...............................................................*/

	var ShearPhotoForm = document.getElementById("ShearPhotoForm"); //FORM对象
	ShearPhotoForm.UpFile.onclick = function() {
			return false
		} //一开始时先不让用户点免得事件阻塞
	var up = new ShearPhoto.frameUpImg({

		url: "/user/upload/avatar", //HTML5切图时，不会用到该文件，后端处理地址，保证正确哦，这是常识，连这个地址都能写错，你就是菜B，已经在本版本中帮你加入相对路径，你基本不用改这里了

		FORM: ShearPhotoForm, //FORM对象传到设置

		UpType: new Array("jpg", "jpeg", "png", "gif"), //图片类限制，上传的一定是图片，你就不要更改了

		FilesSize: 2, //选择的图片不能超过 单位M（注意：是非HTML5时哦）

		HTML5: Shear.HTML5, //切匆改动这句，不然你他妈又问为什么出错

		HTML5FilesSize: Shear.arg.HTML5FilesSize, //切匆改动这句 如果是HTML5切图时，选择的图片不能超过 单位M，设太大话，如果客户端HTML5加截超大图片时，会卡爆的

		HTML5ZIP: Shear.arg.HTML5ZIP, //切匆改动这句, 把压缩设置转移到这里

		erro: function(msg) {
			Shear.pointhandle(3e3, 10, msg, 0, "#f82373", "#fff");
		},
		fileClick: function() { //先择图片被点击时，触发的事件
			Shear.pointhandle(-1); //关闭提示，防止线程阻塞事件冒泡
		},
		preced: function(fun) { //点击选择图，载入图片时的事件
			// try {
			// 	photoalbum.style.display = "none"; //什么情况下都关了相册
			// 	camClose.onclick(); //什么情况下都关了视频
			// } catch (e) {
			// 	console.log("在加载图片时，发现相册或拍照的对象检测不到，错误代码：" + e);
			// }
			Shear.pointhandle(0, 10, "正在为你加载图片，请你稍等哦......", 2, "#307ff6", "#fff", fun);
		}
	});

	up.run(function(data, True) { //upload.php成功返回数据后
		//alert(data);你可以调试一下这个返回包
		True || (data = ShearPhoto.JsonString.StringToJson(data));
		if (data === false) {
			Shear.SendUserMsg("错误：请保证后端环境运行正常", 5e3, 0, "#f4102b", "#fff", true, true);
			return;
		}
		if (data["erro"]) {
			Shear.SendUserMsg("错误：" + data["erro"], 5e3, 0, "#f4102b", "#fff", true, true);
			return;
		}
		Shear.run(data["success"], true);
	});
	/*.................................................选择图片上传的设置结束...............................................................*/

	/*............................截图，左旋，右旋，重新选择..................开始.........看好怎么调用截图，左旋，右旋，重新选择..........................................*/
	Shear.addEvent(document.getElementById("saveShear"), "click", function() { //按下截图事件，提交到后端的shearphoto.php接收
		Shear.SendPHP({
			shearphoto: "我要传参数到服端",
			mingge: "我要传第二个参数到服务器"
		}); //我们示例截图并且传参数，后端文件shearphoto.php用 示例：$_POST["shearphoto"] 接收参数，不需要传参数请清空Shear.SendPHP里面的参数示例 Shear.SendPHP();

	});

	Shear.addEvent(document.getElementById("LeftRotate"), "click", function() { //向左旋转事件
		Shear.Rotate("left");
	});

	Shear.addEvent(document.getElementById("RightRotate"), "click", function() { //向右旋转事件
		Shear.Rotate("right");
	});

	Shear.addEvent(document.getElementById("againIMG"), "click", function() { //重新选择事件
		Shear.preview.close_();
		Shear.again();
		Shear.HTML5.EffectsReturn();
		Shear.HTML5.BOLBID && Shear.HTML5.URL.revokeObjectURL(Shear.HTML5.BOLBID);
		Shear.pointhandle(3e3, 10, "已取消！重新选择", 2, "#fbeb61", "#3a414c");
	});

	/*............................截图，左旋，右旋，重新选择.................................................结束....................*/

	/*...........2.2加入的缓冲效果............................*/
	var shearphoto_loading = document.getElementById("shearphoto_loading");
	var shearphoto_main = document.getElementById("shearphoto_main");
	shearphoto_loading && shearphoto_loading.parentNode.removeChild(shearphoto_loading);
	shearphoto_main.style.visibility = "visible";
	/*................2.2加入的缓冲效果结束..................*/
});