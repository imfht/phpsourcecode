function initbodys(){
	$(form('custid')).change(function(){
		var val = this.value,txt='';
		if(val!=''){
			txt = this.options[this.selectedIndex].text;
		}
		form('custname').value=txt;
		form('saleid').value = '';
	});
	
	$(form('saleid')).change(function(){
		salechange(this.value);
	});
}
function salechange(v){
	if(v==''){
		form('custid').value='';
		form('custname').value='';
		return;
	}
	js.ajax(geturlact('salechange'),{saleid:v},function(a){
		form('custid').value=a.custid;
		form('custname').value=a.custname;
		form('money').value=a.money;
	},'get,json');
}