$(function(){
	WST.upload({
  	  pick:'#storeImgPicker',
  	  formData: {dir:'shops',isThumb:1},
  	  accept: {extensions: 'gif,jpg,jpeg,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
  	  callback:function(f){
  		  var json = WST.toJson(f);
  		  if(json.status==1){
  			$('#uploadMsg').empty().hide();
            $('#preview').attr('src',WST.conf.RESOURCE_PATH+"/"+json.savePath+json.thumb);
            $('#storeImg').val(json.savePath+json.name);
  		  }
	  },
	  progress:function(rate){
	      $('#uploadMsg').show().html('已上传'+rate+"%");
	  }
    });
});




function edit(p){
	$('#editForm').isValid(function(v){
		if(v){
			var params = WST.getParams('.ipt');
			if(WST.conf.IS_CRYPT=='1' && params.loginPwd!=""){
	            var public_key=$('#token').val();
	            var exponent="10001";
	       	    var rsa = new RSAKey();
	            rsa.setPublic(public_key, exponent);
	            params.loginPwd = rsa.encrypt(params.loginPwd);
	            params.reUserPwd = rsa.encrypt(params.reUserPwd);
	        }
	        params.areaId = WST.ITGetAreaVal('j-areaIdPath');
			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('store/stores/toEdit'),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1},function(){
						location.href=WST.U('store/stores/index','p='+p);
					});
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}



var container,map,label,marker,mapLevel = 15;
function initQQMap(longitude,latitude,mapLevel){
    var container = document.getElementById("container");
    mapLevel = WST.blank(mapLevel,13);
    var mapopts,center = null;
    mapopts = {zoom: parseInt(mapLevel)};
	map = new qq.maps.Map(container, mapopts);
	if(WST.blank(longitude)=='' || WST.blank(latitude)==''){
		var cityservice = new qq.maps.CityService({
		    complete: function (result) {
		        map.setCenter(result.detail.latLng);
		    }
		});
		cityservice.searchLocalCity();
	}else{
        marker = new qq.maps.Marker({
            position:new qq.maps.LatLng(latitude,longitude), 
            map:map
        });
        map.panTo(new qq.maps.LatLng(latitude,longitude));
	}
	var url3;
	qq.maps.event.addListener(map, "click", function (e) {
		if(marker)marker.setMap(null); 
		marker = new qq.maps.Marker({
            position:e.latLng, 
            map:map
        });    
	    $('#latitude').val(e.latLng.getLat().toFixed(6));
	    $('#longitude').val(e.latLng.getLng().toFixed(6));
	    url3 = encodeURI(window.conf.__HTTP__+'apis.map.qq.com/ws/geocoder/v1/?location=' + e.latLng.getLat() + "," + e.latLng.getLng() + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
	    $.getJSON(url3, function (result) {
	        if(result.result!=undefined){
	            document.getElementById("storeAddress").value = result.result.address;
	        }else{
	            document.getElementById("storeAddress").value = "";
	        }

	    })
	});
	qq.maps.event.addListener(map,'zoom_changed',function() {
        $('#mapLevel').val(map.getZoom());
    });
}

function mapCity(obj){
    var citys = [];
    $('.j-'+$(obj).attr('data-name')).each(function(){
        citys.push($(this).find('option:selected').text());
    })
    if(citys.length==0)return;
    var url2 = encodeURI(window.conf.__HTTP__+'apis.map.qq.com/ws/geocoder/v1/?region=' + citys.join('') + "&address=" + citys.join('') + "&key="+window.conf.MAP_KEY+"&output=jsonp&&callback=?");
    $.getJSON(url2, function (result) {
        if(result.result.location){
            map.setCenter(new qq.maps.LatLng(result.result.location.lat, result.result.location.lng)); 
        }
    });
}