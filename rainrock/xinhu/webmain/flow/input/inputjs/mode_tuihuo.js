//流程模块【tuihuo.退货单】下录入页面自定义js页面,初始函数
function initbodys(){
	
	c.onselectdataall=function(fid,seld,sna,sid){
		if(fid.substr(0,8)=='temp_aid'){
			this.setrowval(fid,{
				'unit':seld.unit,
				'price':seld.price,
			});
		}
	}
	
	if(form('custractid'))$(form('custractid')).change(function(){
		var val = this.value,txt='';
		custractidchange(val);
	});
}

function custractidchange(v){
	if(v=='' || v=='0'){
		form('custid').value='';
		form('custname').value='';
		return;
	}
	js.ajax(geturlact('ractchange'),{ractid:v},function(a){
		form('custid').value=a.custid;
		form('custname').value=a.custname;
		form('money').value=a.money;
		form('discount').value=a.discount;
		
		var ret = a.zbarr;
		for(var i=0;i<ret.length;i++){
			if(i==0){
				c.setrowdata(0,0,ret[i]);
			}else{
				c.insertrow(0,ret[i],true);
			}
		}
		
	},'get,json');
}