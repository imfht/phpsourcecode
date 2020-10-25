function subinit()
{
  if (form1.username.value=="")
   { 
	alert("您没有填姓名");
	document.form1.username.focus();
	return false;
	 }
  if (form1.tel.value=="")
   { 
	alert("您没有填电话或QQ号");
	document.form1.tel.focus();
	return false;
	 }
  if (form1.todate.value=="")
   { 
	alert("您没有填就诊日期");
	document.form1.todate.focus();
	return false;
	 }
	if (form1.disease.value=="")
   { 
	alert("您没有填病种");
	document.form1.disease.focus();
	return false;
	 }
  if (form1.doctor.value=="")
   { 
	alert("您没有填专家");
	document.form1.doctor.focus();
	return false;
	 }
return true;
  }