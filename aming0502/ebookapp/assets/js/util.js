//复选框全选、反选效果
function SelectAll() {
 for (var i=0;i<document.checkboxform.checkboxid.length;i++) {
  var e=document.checkboxform.checkboxid[i];
  e.checked=!e.checked;
 }
}

function pickAll(jobtype){
	document.checkboxform.jobtype.value=jobtype;
	var articles = getSelIds();
	document.checkboxform.articleids.value=articles;
	document.checkboxform.action="addjob.php";
	document.checkboxform.submit();
}

function getSelIds(){
	var ret="";
	for (var i=0;i<document.checkboxform.checkboxid.length;i++) {
	  var e=document.checkboxform.checkboxid[i];
	  if(e.checked){
		  	ret+=","+e.value;
	  }
 	}
	//alert(ret);
	return ret;
}