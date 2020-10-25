<?php
function_exists('urls') || die('ERR');
$info[$name] || $info[$name] = '113.263661,23.155131';
$jscode = '';
if(fun('field@load_js',$field['type'])){
	$weixin_appid = weixin_share("appId");
	$weixin_time = weixin_share("timestamp")?:0;
	$weixin_nonceStr = weixin_share("nonceStr");
	$weixin_signature = weixin_share("signature");
	$qun_url = iurl('qun/near/point_address');
	$jscode = <<<EOT
<style type="text/css">
.bmap{width:100%;height:350px;border: 1px solid #ccc;}
.searchResultPanel{border:1px solid #C0C0C0;width:150px;height:auto;display:none;}
.baaapsou{width: 10%;float: left;cursor:pointer;height: 28px;line-height: 28px;text-align: center;border: 1px solid #C0C0C0;border-radius: 3px;margin-left: 1%;}
</style>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=MGdbmO6pP5Eg1hiPhpYB0IVd"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
	set_baidu_map();
});
function set_baidu_map(){
    $('.js-bmap').each(function() {
        var that = $(this);
        var map_canvas = that.find('.bmap').attr('id');
        var address = that.find('.bmap-address');
        var address_id = address.attr('id');
        var map_point = that.find('.bmap-point');
        var search_result = that.find('.searchResultPanel');
        var point_lng = 113.268332;
        var point_lat = 23.130274;
        var map_level = that.data('level');
        // 百度地图API功能
        var map = new BMap.Map(map_canvas);
        //开启鼠标滚轮缩放
        map.enableScrollWheelZoom(true);
        // 左上角，添加比例尺
        var top_left_control = new BMap.ScaleControl({
            anchor: BMAP_ANCHOR_TOP_LEFT
        });
        // 左上角，添加默认缩放平移控件
        var top_left_navigation = new BMap.NavigationControl();
        map.addControl(top_left_control);
        map.addControl(top_left_navigation);
        // 智能搜索
        var local = new BMap.LocalSearch(map, {
            onSearchComplete: function() {
                var point = local.getResults().getPoi(0).point; //获取第一个智能搜索的结果
                map.centerAndZoom(point, 18);
                // 创建标注
                create_mark(point);
            }
        });
        // 发起检索
        $("#baaapsou-{$name}").click(function() {
            var city = document.getElementById("bmap-address-{$name}").value;
            if (city != "") {
                local.search(city);
            }
        });
        // 创建标注
        var create_mark = function(point) {
            // 清空所有标注
            map.clearOverlays();
            var marker = new BMap.Marker(point); // 创建标注
            map.addOverlay(marker); //添加标注
            marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
            // 写入坐标
            map_point.val(point.lng + "," + point.lat);
        };
        // 建立一个自动完成的对象
        var ac = new BMap.Autocomplete({
            "input": address_id,
            "location": map
        });
        // 鼠标放在下拉列表上的事件
        ac.addEventListener("onhighlight", function(e) {
            var str = "";
            var _value = e.fromitem.value;
            var value = "";
            if (e.fromitem.index > -1) {
                value = _value.province + _value.city + _value.district + _value.street + _value.business;
            }
            str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;
            value = "";
            if (e.toitem.index > -1) {
                _value = e.toitem.value;
                value = _value.province + _value.city + _value.district + _value.street + _value.business;
            }
            str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
            search_result.html(str);
        });
        // 鼠标点击下拉列表后的事件
        var myValue;
        ac.addEventListener("onconfirm", function(e) {
            var _value = e.item.value;
            myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
            search_result.html("onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue);
            local.search(myValue);
        });
        // 监听点击地图时间
        map.addEventListener("click", function(e) {
            // 创建标注
            create_mark(e.point);
        });
	
		//解决部分手机的兼容问题
		map.addEventListener("touchend", function (e) {
			map.disableDragging();
			create_mark(e.point);
		});
		map.addEventListener("touchmove", function (e) {
		   map.enableDragging();
		});

        if (map_point.val() != '') {
            var curr_point = map_point.val().split(',');
            point_lng = curr_point[0];
            point_lat = curr_point[1];
            map_level = 16;
        } else if (address.val() != '') {
            local.search(address.val());
        } else {
            // 根据ip获取当前城市，并定位到当前城市
            var myCity = new BMap.LocalCity();
            myCity.get(function(result) {
                var cityName = result.name;
                map.setCenter(cityName);
            });
        }
        // 初始化地图,设置中心点坐标和地图级别
        var point = new BMap.Point(point_lng, point_lat);
        map.centerAndZoom(point, map_level);
        if (map_point.val() != '') {
            // 创建标注
            create_mark(point);
        }
        if (address.val() != '') {
            ac.setInputValue(address.val())
        }

		if(map_point.val() == '113.263661,23.155131'||map_point.val() == ''){	//新发表的时候，获取当前定位坐标
			get_location_point(map_point);
		}
    });
}
 </script>

<script type="text/javascript" src="https://developer.baidu.com/map/jsdemo/demo/convertor.js"></script>
<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.3.2.js"></script>
<script type="text/javascript" src="__STATIC__/js/map-gps.js"></script>
<script type="text/javascript">
//公众号或WAP浏览器获取当前坐标位置
function reload_baidu_map(obj){
	var geolocation = new BMap.Geolocation();
	geolocation.getCurrentPosition(function(result){
		if(this.getStatus() == window.BMAP_STATUS_SUCCESS){
		  map_x = result.point.lng;
		  map_y = result.point.lat;
		  obj.val(map_x+','+map_y);
		  if(typeof(set_baidu_map)=='function'){set_baidu_map();}

		  //显示当前位置的街道名
			var geoc = new BMap.Geocoder();
			geoc.getLocation(result.point, function(rs){
				var addComp = rs.addressComponents;
				$("#atc_address").val(addComp.district + addComp.street + addComp.streetNumber);
			});

			//gg = GPS.bd_decrypt(result.point.lat, result.point.lng);	//百度转谷歌
			//wgs = GPS.gcj_decrypt(gg.lat, gg.lon); //谷歌转GPS
		    //showMapPosition(wgs.lon,wgs.lat);
		} else {
			alert('failed:'+this.getStatus());
		}        
	},{enableHighAccuracy: true})
}

//小程序获取当前坐标位置
function load_wx_map(obj){ 
	wx.getLocation({
			type: 'gcj02', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'' 
      success: function (res) {
		  var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
          var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
			var o = GPS.bd_encrypt(res.latitude,res.longitude);
           map_x = o.lon;//res.longitude;
		   map_y = o.lat;//res.latitude; 
		  obj.val(map_x+','+map_y);
		  if(typeof(set_baidu_map)=='function'){set_baidu_map();}		  
		  $.get("{$qun_url}?x=" + map_y +  "&y=" + map_x  ,function(res){
			  $("#atc_address").val(res.data);
		  });
      },
      cancel: function (res) {
        alert('用户拒绝授权获取地理位置');
      }
    }); 
}

var is_in_wxapp = -1;
function get_location_point(obj){
	wx.miniProgram.getEnv(function(res) {
		if(res.miniprogram==true){	//在小程序中,用不了百度地图定位
			is_in_wxapp = 1;
			wx.config({
				debug: false,
				appId: '{$weixin_appid}',
				timestamp: {$weixin_time},
				nonceStr: '{$weixin_nonceStr}',
				signature: '{$weixin_signature}',
				jsApiList: [
						'openLocation',
						'getLocation'
				]
			});
			wx.ready(function () {
				load_wx_map(obj);
			});
			wx.error(function (res) {
				alert(res.errMsg);
			});		
		}else{
			is_in_wxapp = 0;
			reload_baidu_map(obj); //在公众号或WAP浏览器里
		}
	});
	setTimeout(function(){
			if(is_in_wxapp==-1){
				reload_baidu_map(obj);
			}
		},1500);
}

</script>

EOT;
}
return <<<EOT
$jscode
<div class="js-bmap">
		<input class="bmap-address" style="width:85%;float: left;" id="bmap-address-{$name}" name="{$name}_address" type="text" value="" placeholder="请输入要搜索的地址,或者手工在下面精准定位"> 
		<div id="baaapsou-{$name}" class="baaapsou">搜索</div>
        <div class="searchResultPanel"></div>
        <input class="bmap-point" type="text" id="atc_{$name}" name="{$name}" value="{$info[$name]}">
        <div class="bmap" id="bmap-canvas-{$name}"></div>			
</div>
EOT;
;