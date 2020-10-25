/**
 * CopyRight Samphay.
 * 2017/5/5
 */
"use strict";
(function (window, undefined) {
    function Location() {
        this.lng = 0;
        this.lat = 0;
        this.bMapAK = "140Kirzl5UDrjqibOTTPvULw";
        this.address = "";
        this.addressData = {};
    }
    Location.prototype = {
        get point (){
            return {
                lng : this.lng,
                lat : this.lat
            }
        },
        set point (point){
            this.lng = point.lng || this.lng;
            this.lat = point.lat || this.lat;
        },
        getLocation : function(){

        },
        bMap : function (onload) {
            if(document.querySelectorAll("#bMap").length===0){
                var bMapAK = this.bMapAK;
                var script = document.createElement("script");
                script.type = "text/javascript";
                // script.src = "http://api.map.baidu.com/api?v=2.0&ak="+bMapAK+"";
                script.src = "http://api.map.baidu.com/getscript?v=2.0&ak="+bMapAK+"";
                script.id = "bMap";
                document.body.appendChild(script);
                script.onload = function () {
                    onload();
                }
            }else{
                try{
                    onload()
                }catch (e){
                    throw e;
                }
            }
        },
        bLocation : function (callback) {
            var This = this;
            this.bMap(function (){
                var geolocation = new BMap.Geolocation();
                geolocation.getCurrentPosition(function(r){
                    if(this.getStatus() === BMAP_STATUS_SUCCESS){
                        var mk = new BMap.Marker(r.point);
                        // alert('您的位置：'+r.point.lng+','+r.point.lat);
                        This.transformToAddress({
                            point : r.point
                        },callback)
                    }
                    else {
                        alert('failed'+this.getStatus());
                    }
                },{enableHighAccuracy: true});
            })
        },
        wxLocation : function (callback) {
            var This = this;
            try{
                if(typeof wx ==="undefined"){
                    return alert("请确保在微信环境运行，并保证已经正确启用微信jssdk！")
                }
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude ; // 经度，浮点数，范围为180 ~ -180。
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                        This.transformToAddress({
                            point : {
                                lat : latitude,
                                lng : longitude
                            },
                            needTransPoint : true
                        },callback);
                    }
                });
            }catch (e){
                throw e;
            }
        },
        localLocation : function (callback) {
            var This = this;
            try{
                var geolocation = window.navigator.geolocation;
                geolocation.getCurrentPosition(function (position) {
                    This.transformToAddress({
                        point : {
                            lat : position.coords.latitude,
                            lng : position.coords.longitude
                        },
                        needTransPoint : true
                    },callback);
                })
            }catch (e){
                throw e;
            }
        },
        transformToAddress : function (opt,callback) {
            var This = this;
            var point = opt.point||{};
            var needTransPoint = opt.needTransPoint;
            this.bMap(function () {
                function doTrans(point) {
                    var geoc = new BMap.Geocoder();
                    geoc.getLocation(point, function(rs){
                        var addComp = rs.addressComponents;
                        This.addressData = rs;
                        This.address = rs.address;
                        This.point = rs.point;
                        if(typeof callback === "function"){
                            callback.call(This,addComp);
                        }
                    });
                }
                if(needTransPoint){
                    var convertor = new BMap.Convertor();
                    convertor.translate([new BMap.Point(point.lng,point.lat)], 1, 5, function (data) {
                        doTrans(data.points[0]);
                    })
                }else{
                    doTrans(point);
                }

            })
        }
    };
    window.Location = Location;
})(window);