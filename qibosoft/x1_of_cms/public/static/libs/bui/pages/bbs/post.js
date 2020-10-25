/**
 * 发布评论模块
 * 默认模块名: pages/comment/comment
 * @return {[object]}  [ 返回一个对象 ]
 */
loader.define({
	 beforeCreate: function() {
        // 只在创建脚本前执行,缓存的时候不执行
        console.log(this.moduleName + " before create")
    },
    created: function() {
        // 只在创建后执行,缓存的时候不执行
        console.log(this.moduleName + " createed")
    },
    beforeLoad: function() {
        // 页面每次跳转前都会执行
        console.log(this.moduleName + " before load")
    },
    loaded: function() {
        // 页面每次跳转后都会执行
        console.log(this.moduleName + " loaded")
    },
    hide: function(e) {
        // 页面每次跳转后退都会执行当前模块的触发
        console.log(this.moduleName + " hide ="+e.type)
    },
    show: function(e) {
        // 页面每次跳转后退都会执行当前模块的触发
        console.log(this.moduleName + " show ="+e.type)

			if(e.type=='back'){
				bui.back();
			}

		var pageview = {};
		var near_fid = 0;
		var map_x = 0;
		var map_y = 0;

		store.compile(".bui-bar");	//重新加载全局变量数据

			// 模块初始化定义
		pageview.init = function() {
				// 长度限制
				var comment = bui.input({
					id: "#feedback",
					target: "textarea",
					showIcon: false,
					maxLength: 1000,
					showLength: true
				});

				router.$("#post_btn").click(function(){
					post_content();
				});

				$.get("/index.php/bbs/wxapp.sorts/get_near_fid.html",function(res){
					if(res.code==0){
						near_fid = res.data.id;
					}
				});

				
				loader.import(["/public/static/libs/bui/js/map.js","/public/static/js/exif.js"],function(){
					get_gps_location(function(x,y){
						map_x = x;
						map_y = y;						
						show_address(x,y);						
					});
				});
				// 上传初始化
				this.upload();
			}
			
			//显示当前位置的街道名
			function show_address(x,y){
				$.get("/member.php/member/wxapp.user/edit_map.html?point="+x+","+y);	//更新个人位置

				$.get("/index.php/index/wxapp.map/get_address.html?xy="+x+","+y,function(res){
					if(res.code==0){
						router.$(".map-position i").html( res.data.address );
					}
				});
			}

			var havepost = false;
			function post_content(){
				var pics = [];
				$("#buiPhoto img").each(function(){
					var url = $(this).attr("src");
					if(url.indexOf('://')==-1 && url.indexOf('/public/')>-1){
						if(url.indexOf('://')==-1 && url.indexOf('/public/')>-1){
							url = url.replace("/public/","");
						}
					}
					pics.push(url);
				});		
				var content = $("#content").val();
				if(pics.length<1 && content==""){
					layer.alert('没有上传图片,内容不能为空!');
					return false;
				}
				if(havepost==true){
					layer.msg('请不要重复提交');
					return false;
				}
				var title = "";
				if(content!=""){
					if(content.length<5){
						layer.alert('内容不能小于5个字');
						return false;
					}
					title = content.substring(0,50);
				}else{
					title = "图片主题,发布于:"+(new Date().format("yyyy-MM-dd hh:mm:ss"));
					content = "详情如图片所示";
				}
				var url = "/index.php/bbs/wxapp.post/add/ext_sys/0/ext_id/0.html";
				havepost = true;
				$.post(url, {
					title:title,
					content:content,
					fid:near_fid,
					map:map_x+','+map_y,
					picurl:pics.length>0?pics.join(','):"",
				}).success(function (res) {				
						if (res.code==0) {
							layer.msg(res.msg);
							setTimeout(function(){
								var tourl = "/index.php/bbs/show.html?id="+res.data.id;
								bui.load({ url: "/public/static/libs/bui/pages/frame/show.html",param:{url:tourl}});
								//bui.load({ url: "/public/static/libs/bui/pages/bbs/index.html"});
							},800);
						} else {
							havepost = false;
							layer.open({title: '提示',content: res.msg});
						}
					}).fail(function () {
						havepost = false;
						layer.open({title: '提示',content: '服务器发生错误'});
					});
			}

			// 上传
			pageview.upload = function() {
				// 拍照上传
				var photos = $("#buiPhoto");
				var uiUpload = bui.upload();


				// 上拉菜单 js 初始化:
				var uiActionsheet = bui.actionsheet({
					trigger: "#btnUpload",
					buttons: [{ name: "拍照上传", value: "camera" }, { name: "从相册选取", value: "photo" }],
					callback: function(e) {
						var ui = this;
						var val = $(e.target).attr("value");
						switch (val) {
							case "camera":
								ui.hide();
								uiUpload.add({
									"from": "camera",
									"onSuccess": function(val, data) {
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												//photos.prepend(templatePhoto(url))
												upload_pic(url)

											}
										});

										// 也可以直接调用start上传图片
									}
								})

								break;
							case "photo":
								ui.hide();
								uiUpload.add({
									"from": "",
									"onSuccess": function(val, files) {
										var Orientation=0
										 var filefield = document.getElementById($('input[name="uploadFiles"]').attr('id')) 
											 var file = filefield.files[0];
											EXIF.getData(file, function(){												 
												Orientation = EXIF.getTag(this, 'Orientation'); console.log("d "+Orientation);												
											});										
										//console.log(val);
										//console.log(this.data()[0]);
										//var url = window.URL.createObjectURL(files[0]);
										//document.querySelector('img').src = window.URL.createObjectURL(url);
										// 展示本地图片
										this.toBase64({
											onSuccess: function(url) {
												//photos.prepend(templatePhoto(url))
												upload_pic(url,Orientation)
											}
										});
										// 也可以直接调用start上传图片
									}
								})

								break;
							case "cancel":
								ui.hide();
								break;
						}
					}
				})

				function templatePhoto(url) {
					return `<div class="span1">
							<div class="bui-upload-thumbnail"><img src="${url}" alt="" /></div>
						</div>`
				}
				
				function upload_pic(base64,Orientation){
					var image = new Image();
					image.src = base64;
					image.onload = function() {
						var resized = resizeUpImages(image);
						var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";
						$.post(severUrl, {'imgBase64':resized,'Orientation':Orientation,'tags':''}).done(function (res) {
							if(res.code==1){
								//console.log(res);
								var url = res.path;
								if(url.indexOf('://')==-1 && url.indexOf('/public/')==-1){
									url = '/public/'+url;
								}
								$("#buiPhoto").prepend( templatePhoto(url) )
							}else{
								alert(res.info);
							}
						}).fail(function () {
							alert('操作失败，请跟技术联系');
						});
					}
					
				}


				function resizeUpImages(img) {
					//压缩的大小
					var max_width = 1920; 
					var max_height = 1080; 

					var canvas = document.createElement('canvas');
					var width = img.width;
					var height = img.height;
					if(width > height) {
						if(width > max_width) {
							height = Math.round(height *= max_width / width);
							width = max_width;
						}
					}else{
						if(height > max_height) {
							width = Math.round(width *= max_height / height);
							height = max_height;
						}
					}

					canvas.width = width;
					canvas.height = height;

					var ctx = canvas.getContext("2d");
					ctx.drawImage(img, 0, 0, width, height);
					//压缩率
					return canvas.toDataURL("image/jpeg",0.72); 
				}

			}


			Date.prototype.format = function (format) {
			   var args = {
				   "M+": this.getMonth() + 1,
				   "d+": this.getDate(),
				   "h+": this.getHours(),
				   "m+": this.getMinutes(),
				   "s+": this.getSeconds(),
				   "q+": Math.floor((this.getMonth() + 3) / 3),  //quarter
				   "S": this.getMilliseconds()
			   };
			   if (/(y+)/.test(format))
				   format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
			   for (var i in args) {
				   var n = args[i];
				   if (new RegExp("(" + i + ")").test(format))
					   format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? n : ("00" + n).substr(("" + n).length));
			   }
			   return format;
		   };

			// 初始化
			pageview.init();

			// 输出模块
			return pageview;
    },
    beforeDestroy: function() {
        // 页面每次后退前执行
        console.log(this.moduleName + " before destroy")
    },
    destroyed: function() {
        // 页面每次后退后执行
        console.log(this.moduleName + " destroyed")
    }
}/*function(){

    var pageview = {};

	store.compile(".bui-bar");	//重新加载全局变量数据

    // 模块初始化定义
    pageview.init = function() {
        // 长度限制
        var comment = bui.input({
            id: "#feedback",
            target: "textarea",
            showIcon: false,
            maxLength: 1000,
            showLength: true
        });

		router.$("#post_btn").click(function(){
			post_content();
		});

		window.HOST_TYPE = "2";
		window.BMap_loadScriptTime = (new Date).getTime();
		loader.import(["https://api.map.baidu.com/getscript?v=2.0&ak=MGdbmO6pP5Eg1hiPhpYB0IVd&services=&t=20190622163250","/public/static/js/bdmap.js","/public/static/js/map-gps.js"],function(){
			reload_map();
		});
        // 上传初始化
        this.upload();
    }

	var map_x = 0;
	var map_y = 0;
	//获取当前坐标位置
	function reload_map(){
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(result){
			if(this.getStatus() == window.BMAP_STATUS_SUCCESS){
			  map_x = result.point.lng;
			  map_y = result.point.lat;
			  $.get("/member.php/member/wxapp.user/edit_map.html?point="+map_x+","+map_y);
			  showMapPosition(map_x,map_y);
				//var geoc = new BMap.Geocoder();
				//geoc.getLocation(result.point, function(rs){
				//	var addComp = rs.addressComponents;
				//	alert(addComp.district + addComp.street + addComp.streetNumber);
				//});

				//gg = GPS.bd_decrypt(result.point.lat, result.point.lng);	//百度转谷歌
				//wgs = GPS.gcj_decrypt(gg.lat, gg.lon); //谷歌转GPS
				//showMapPosition(wgs.lon,wgs.lat);
			} else {
				alert('failed:'+this.getStatus());
			}        
		},{enableHighAccuracy: true})
	}

	function showMapPosition(longitude,latitude){
		//显示当前位置的街道名
		var gpsPoint = new BMap.Point(longitude, latitude);
		BMap.Convertor.translate(gpsPoint, 0, function(point){
		//alert('x:'+point.lng+' y:'+point.lat);
			var geoc = new BMap.Geocoder();
			geoc.getLocation(point, function(rs){
				var addComp = rs.addressComponents;
				router.$(".map-position i").html( addComp.district + addComp.street + addComp.streetNumber);
			});
		});
	}

	var havepost = false;
	function post_content(){
		var pics = [];
		$("#buiPhoto img").each(function(){
			var url = $(this).attr("src");
			if(url.indexOf('://')==-1 && url.indexOf('/public/')>-1){
				if(url.indexOf('://')==-1 && url.indexOf('/public/')>-1){
					url = url.replace("/public/","");
				}
			}
			pics.push(url);
		});		
		var content = $("#content").val();
		if(pics.length<1 && content==""){
			layer.alert('没有上传图片,内容不能为空!');
			return false;
		}
		if(havepost==true){
			layer.msg('请不要重复提交');
			return false;
		}
		var title = "";
		if(content!=""){
			if(content.length<5){
				layer.alert('内容不能小于5个字');
				return false;
			}
			title = content.substring(0,50);
		}else{
			title = "图片主题";
			content = "详情如图片所示";
		}
		var url = "/index.php/bbs/wxapp.post/add/ext_sys/0/ext_id/0.html";
		havepost = true;
        $.post(url, {
			title:title,
			content:content,
			map:map_x+','+map_y,
			picurl:pics.length>0?pics.join(','):"",
		}).success(function (res) {				
                if (res.code==0) {
					layer.msg(res.msg);
					setTimeout(function(){
						var tourl = "/index.php/bbs/show.html?id="+res.data.id;
						bui.load({ url: "/public/static/libs/bui/pages/frame/show.html",param:{url:tourl}});
						//bui.load({ url: "/public/static/libs/bui/pages/bbs/index.html"});
					},800);
                } else {
					havepost = false;
					layer.open({title: '提示',content: res.msg});
                }
            }).fail(function () {
				havepost = false;
				layer.open({title: '提示',content: '服务器发生错误'});
            });
	}

    // 上传
    pageview.upload = function() {
        // 拍照上传
        var photos = $("#buiPhoto");
        var uiUpload = bui.upload();


        // 上拉菜单 js 初始化:
        var uiActionsheet = bui.actionsheet({
            trigger: "#btnUpload",
            buttons: [{ name: "拍照上传", value: "camera" }, { name: "从相册选取", value: "photo" }],
            callback: function(e) {
                var ui = this;
                var val = $(e.target).attr("value");
                switch (val) {
                    case "camera":
                        ui.hide();
                        uiUpload.add({
                            "from": "camera",
                            "onSuccess": function(val, data) {
                                // 展示本地图片
                                this.toBase64({
                                    onSuccess: function(url) {
                                        //photos.prepend(templatePhoto(url))
										upload_pic(url)

                                    }
                                });

                                // 也可以直接调用start上传图片
                            }
                        })

                        break;
                    case "photo":
                        ui.hide();
                        uiUpload.add({
                            "from": "",
                            "onSuccess": function(val, data) {console.log(this);
                                // 展示本地图片
                                this.toBase64({
                                    onSuccess: function(url) {
                                        //photos.prepend(templatePhoto(url))
										upload_pic(url)
                                    }
                                });
                                // 也可以直接调用start上传图片
                            }
                        })

                        break;
                    case "cancel":
                        ui.hide();
                        break;
                }
            }
        })

        function templatePhoto(url) {
            return `<div class="span1">
                    <div class="bui-upload-thumbnail"><img src="${url}" alt="" /></div>
                </div>`
        }
		
		function upload_pic(base64){
			var severUrl = "/index.php/index/attachment/upload/dir/images/from/base64/module/bbs.html";
			$.post(severUrl, {'imgBase64':base64,'Orientation':'','tags':''}).done(function (res) {
				if(res.code==1){
					//console.log(res);
					$("#buiPhoto").prepend( templatePhoto(res.path) )
				}else{
					alert(res.info);
				}
			}).fail(function () {
				alert('操作失败，请跟技术联系');
			});
		}



    }

    // 初始化
    pageview.init();

    // 输出模块
    return pageview;
}*/)
