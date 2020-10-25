$.country = {
	province:null,
	city:null,
	district:null,
	getProvince:function(selected) {
		this.province.empty();
		this.get(0,this.province,selected);
	},
	get:function(pid,obj,selected) {
		var _this = this;
		obj[0].add(new Option("",""));
		$.get("util.php?action=district",{pid:pid},function(data) {
			if(data == "failure") {
				jAlert("发生错误,请刷新页面后重新执行该操作");
			} else {
				for(var i in data) {
					obj[0].add(new Option(data[i]['district'],data[i]['districtid']));
				}
				if(selected) {
					obj.val(selected);
				}
			}
		},"json");
	},
	getCity:function(selected){
		this.city.empty();
		this.district.empty();
		
		var selprovince = this.province.val();
		if(!selprovince) return ;
		this.get(selprovince,this.city,selected);
	},
	getDistrict:function(selected) {
		this.district.empty();
		
		var selcity		= this.city.val();
		if(!selcity) return ;
		this.get(selcity,this.district,selected);
	},
	getZip:function() {
		var districtid = this.district.val();
		$.get("util.php?action=zip",{districtid:districtid},function(data){
			if(data == "failure") {
				return ;
			} else {
				$("#zipcode").val(data);
			}
		});
	},
	init:function(showp) {
		this.province = $("#province");
		this.city	  = $("#city");
		this.district = $("#district");
		if(showp) {
			this.getProvince();
		}
	}
};








