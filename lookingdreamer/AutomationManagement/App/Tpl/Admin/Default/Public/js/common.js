function showTip(info){
	$('tips').innerHTML	=	info;
}
function sendForm(formId,action,response,target,effect){
	// Ajax方式提交表单
	if (CheckForm($(formId),'ThinkAjaxResult'))//表单数据验证
	{
		ThinkAjax.sendForm(formId,action,response,target);
	}
	//Form.reset(formId);
}
rowIndex = 0;

function prepareIE(height, overflow){
	bod = document.getElementsByTagName('body')[0];
	bod.style.height = height;
	//bod.style.overflow = overflow;

	htm = document.getElementsByTagName('html')[0];
	htm.style.height = height;
	//htm.style.overflow = overflow; 
}

function hideSelects(visibility){
   selects = document.getElementsByTagName('select');
   for(i = 0; i < selects.length; i++) {
		   selects[i].style.visibility = visibility;
	}
}
document.write('<div id="overlay" class="none"></div><div id="lightbox" class="none"></div>');
// 显示light窗口
function showPopWin(content,width,height){
	     //  IE 
		 prepareIE('100%', 'hidden');
		 window.scrollTo(0, 0); 
		 hideSelects('hidden');//隐藏所有的<select>标记
		$('overlay').style.display = 'block';
		var arrayPageSize = getPageSize();
		var arrayPageScroll = getPageScroll();
		$('lightbox').style.display = 'block';
		$('lightbox').style.top = (arrayPageScroll[1] + ((arrayPageSize[3] - 35 - height) / 2) + 'px');
		$('lightbox').style.left = (((arrayPageSize[0] - 25 - width) / 2) + 'px');
		$('lightbox').innerHTML	=	content;
}

function fleshVerify(){
//重载验证码
var timenow = new Date().getTime();
$('verifyImg').src= APP+'/Public/verify/'+timenow;
}

function setseokey(){
	var sk = $('#seokey_list').val();
	if(sk){
		UE_content.focus();
		UE_content.execCommand("insertHTML",sk.join(','));
	}
}

function get_url_by_title(){
	if($(".titletxt") && $(".urltxt")){
		var title = $(".titletxt").val();
    $.ajax({
        type:"POST",
        url:'/index.php/Admin/Public/urlbytitle?isajax=1',
        data:'title='+title,
        success:function(data){
            if(data.status==1){
                $(".urltxt").val(data.info);
            }
        }
    })
	}
}

function allSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= true;
	}
}
function allUnSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= false;
	}
}

function InverSelect(){
	var	colInputs = document.getElementsByTagName("input");
	for	(var i=0; i < colInputs.length; i++)
	{
		colInputs[i].checked= !colInputs[i].checked;
	}
}

function WriteTo(id){
	var type = $F('outputType');
	switch (type)
	{
	case 'EXCEL':WriteToExcel(id);break;
	case 'WORD':WriteToWord(id);break;
	
	}
	return ;
}

function build(id){
	window.location = APP+'/Card/batch/type/'+id;
}
function shortcut(){
	var name	=	 window.prompt("输入该快捷方式的显示名称","");
	if (name !=null)
	{
	var url	=	location.href;
	ThinkAjax.send(location.protocol+'//'+location.hostname+APP+'/Shortcut/ajaxInsert/','ajax=1&name='+name+'&url='+url);
	}

}

function show(){
	if (document.getElementById('menu').style.display!='none')
	{
	document.getElementById('menu').style.display='none';
	document.getElementById('main').className = 'full';
	}else {
	document.getElementById('menu').style.display='inline';
	document.getElementById('main').className = 'main';
	}
}

function CheckAll(strSection)
	{
		var i;
		var	colInputs = document.getElementById(strSection).getElementsByTagName("input");
		for	(i=1; i < colInputs.length; i++)
		{
			colInputs[i].checked=colInputs[0].checked;
		}
	}
function add(id){
	if (id)
	{
		 location.href  = URL+"/add/action_name/"+ACTION_NAME+"/id/"+id;
	}else{
		 location.href  = URL+"/add/action_name/"+ACTION_NAME+"";
	}
}

function news(wd){
	if (wd)
	{
		 location.href  = APP+"/Admin/New/add/action_name/"+ACTION_NAME+"/wd/"+wd;
	}else{
		 location.href  = URL+"/Admin/New/add/action_name/"+ACTION_NAME+"";
	}
}

function showHideSearch(){
	if (document.getElementById('searchM').style.display=='inline')
	{
		document.getElementById('searchM').style.display='none';
		document.getElementById('showText').value ='高级';
		document.getElementById('key').style.display='inline';
	}else {
		document.getElementById('searchM').style.display='inline';
		document.getElementById('showText').value ='隐藏';
		document.getElementById('key').style.display='none';

	}
}

function top(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择置顶项！');
		return false;
	}

	location.href = URL+"/top/action_name/"+ACTION_NAME+"/id/"+keyValue;

}

function sort(id){
	var keyValue;
	keyValue = getSelectCheckboxValues();
	location.href = URL+"/sort/action_name/"+ACTION_NAME+"/sortId/"+keyValue;
}

function high(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择高亮项！');
		return false;
	}
	location.href = URL+"/high/action_name/"+ACTION_NAME+"/id/"+keyValue;
}
function recommend(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择推荐项！');
		return false;
	}
	location.href = URL+"/recommend/action_name/"+ACTION_NAME+"/id/"+keyValue;
}
function unrecommend(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择项目！');
		return false;
	}
	location.href = URL+"/unrecommend/action_name/"+ACTION_NAME+"/id/"+keyValue;
}
function pass(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择审核项！');
		return false;
	}

	if (window.confirm('确实审核通过吗？'))
	{
		window.location.href = URL+	'/checkPass/action_name/'+ACTION_NAME+'/id/'+keyValue;
		//ThinkAjax.send(URL+"/checkPass/","id="+keyValue+'&ajax=1');
	}
}
function sortBy (field,sort){
	location.href = "?_order="+field+"&_sort="+sort;
}
function cache(){
	ThinkAjax.send(URL+'/cache/action_name/'+ACTION_NAME,'ajax=1');
}
function forbid(id){
	location.href = URL+"/forbid/action_name/"+ACTION_NAME+"/id/"+id;
}
function recycle(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择要还原的项目！');
		return false;
	}
	location.href = URL+"/recycle/action_name/"+ACTION_NAME+"/id/"+keyValue;
}
function resume(id){
	location.href = URL+"/resume/action_name/"+ACTION_NAME+"/id/"+id;
}
function output(){
	location.href = URL+"/output/action_name/"+ACTION_NAME+"/";
}
function member(id){
	location.href = URL+"/../Member/edit/action_name/"+ACTION_NAME+"/id/"+id;
}
function chat(id){
	location.href = URL+"/../Chat/index/action_name/"+ACTION_NAME+"/girlId/"+id;
}
function login(id){
	location.href = URL+"/../Login/index/action_name/"+ACTION_NAME+"/type/4/id/"+id;
}
function child(id){
	location.href = URL+"/index/action_name/"+ACTION_NAME+"/pid/"+id;
}
function action(id){
	location.href = URL+"/action/action_name/"+ACTION_NAME+"/groupId/"+id;
}

function access(id){
	location.href= URL+"/access/action_name/"+ACTION_NAME+"/id/"+id;
}
function app(id){
	location.href = URL+"/app/action_name/"+ACTION_NAME+"/groupId/"+id;
}

function module(id){
	location.href = URL+"/module/action_name/"+ACTION_NAME+"/groupId/"+id;
}

function user(id){
	location.href = URL+"/user/action_name/"+ACTION_NAME+"/id/"+id;
}

	//+---------------------------------------------------
	//|	打开模式窗口，返回新窗口的操作值
	//+---------------------------------------------------
	function PopModalWindow(url,width,height)
	{
		var result=window.showModalDialog(url,"win","dialogWidth:"+width+"px;dialogHeight:"+height+"px;center:yes;status:no;scroll:no;dialogHide:no;resizable:no;help:no;edge:sunken;");
		return result;
	}
	
function viewstyle(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	location.href =  URL+"/style/action_name/"+ACTION_NAME+"/tplid/"+keyValue;
}	

function read(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/read/action_name/"+ACTION_NAME+"/id/"+keyValue;
}

function edit(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValue();
	}
	if (!keyValue)
	{
		alert('请选择编辑项！');
		return false;
	}
	location.href =  URL+"/edit/action_name/"+ACTION_NAME+"/id/"+keyValue;
}
var selectRowIndex = Array();
function del(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}
	if (window.confirm('确实要删除选择项吗？'))
	{
		location.href =  URL+"/delete/action_name/"+ACTION_NAME+"/id/"+keyValue;
	}
}
function foreverdel(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要永久删除选择项吗？'))
	{
		ThinkAjax.send(URL+"/foreverdelete/action_name/"+ACTION_NAME+"/","id="+keyValue+'&ajax=1',doDelete);
	}
}
function getTableRowIndex(obj){ 
	selectRowIndex[0] =obj.parentElement.parentElement.rowIndex;/*当前行对象*/
}

function doDelete(data,status){
		if (status==1)
		{
		var Table = $('checkList');
		var len	=	selectRowIndex.length;
		for (var i=len-1;i>=0;i-- )
		{
			//删除表格行
			Table.deleteRow(selectRowIndex[i]);
		}
		selectRowIndex = Array();
		}

}
	function delAttach(id,showId){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择删除项！');
		return false;
	}

	if (window.confirm('确实要删除选择项吗？'))
	{
		$('result').style.display = 'block';
		ThinkAjax.send(URL+"/delAttach/action_name/"+ACTION_NAME+"/","id="+keyValue+'&_AJAX_SUBMIT_=1');
		if (showId != undefined)
		{
			$(showId).innerHTML = '';
		}
	}
}

function clearData(){
	if (window.confirm('确实要清空全部数据吗？'))
	{
	location.href = URL+"/clear/action_name/"+ACTION_NAME+"/";
	}
}
function takeback(id){
	var keyValue;
	if (id)
	{
		keyValue = id;
	}else {
		keyValue = getSelectCheckboxValues();
	}
	if (!keyValue)
	{
		alert('请选择回收项！');
		return false;
	}

	if (window.confirm('确实要回收选择项吗？'))
	{
	location.href = URL+"/takeback/action_name/"+ACTION_NAME+"/id/"+keyValue;
	}
}


function getSelectCheckboxValue(){
	var obj = document.getElementsByName('key');
	var result ='';
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true)
				return obj[i].value;

	}
	return false;
}

function getSelectCheckboxValues(){
	var obj = document.getElementsByName('key');
	var result ='';
	var j= 0;
	for (var i=0;i<obj.length;i++)
	{
		if (obj[i].checked==true){
				selectRowIndex[j] = i+1;
				result += obj[i].value+",";
				j++;
		}
	}
	return result.substring(0, result.length-1);
}

 function   change(e)   
  {   
	  if (!document.all)
	  {return ;
	  }
	var e = e || event;
	var   oObj   =   e.srcElement   ||   e.target;  
	  //if(oObj.tagName.toLowerCase()   ==   "td")   
	 // {   
		  	  /*
	  var   oTable   =   oObj.parentNode.parentNode;   
	  for(var   i=1;   i<oTable.rows.length;   i++)   
	  {   
	  oTable.rows[i].className   =   "out";   
	  oTable.rows[i].tag   =   false;   
	  }   */
	var obj= document.getElementById('checkList').getElementsByTagName("input");
	  var   oTr   =   oObj.parentNode; 
	  var row = oObj.parentElement.rowIndex-1;
	  if(obj[row]!=undefined){
		  if (oTr.className == 'down')
		  {
			  	oTr.className   =   'out';   
				obj[row].checked = false;
			    oTr.tag   =   true;  
		  }else {
				oTr.className   =   'down';   
				obj[row].checked = true;
			    oTr.tag   =   true;  
		  }
		}
 	  //}   
  }   
    
  function   out(e)   
  {   
	var e = e || event;
  var   oObj   =   e.srcElement   ||   e.target;   


  
  var   oTr   =   oObj.parentNode;   
  if(!oTr.tag)   
  oTr.className   =   "out";   
 
  }   
    
  function   over(e)   
  {   
	var e = e || event;
  var   oObj   =   e.srcElement   ||   e.target;   
  
  var   oTr   =   oObj.parentNode;   
  if(!oTr.tag)   
  oTr.className   =   "over";   
  
  }   


function check_Sel_Multi(id1, id2){   
	if(jQuery("#sel_multi").children('option').size()>0){
		var ids = ','+jQuery("#sel_multi").val()+','+jQuery("#catid").val()+',';
		jQuery("#catstr").val(ids);
	}
} 

//---------------------------------------------------------------------
// 多选改进方法 by Liu21st at 2005-11-29
// 
//
//-------------------------begin---------------------------------------

function searchItem(item){
	for(i=0;i<selectSource.length;i++)
		if (selectSource[i].text.indexOf(item)!=-1)
		{selectSource[i].selected = true;break;}
}

function addItem(){
	for(i=0;i<selectSource.length;i++)
		if(selectSource[i].selected){
			selectTarget.add( new Option(selectSource[i].text,selectSource[i].value));
			}
		for(i=0;i<selectTarget.length;i++)
			for(j=0;j<selectSource.length;j++)
				if(selectSource[j].text==selectTarget[i].text)
					selectSource[j]=null;
}

function delItem(){
	for(i=0;i<selectTarget.length;i++)
		if(selectTarget[i].selected){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
		}
		for(i=0;i<selectSource.length;i++)
			for(j=0;j<selectTarget.length;j++)
			if(selectTarget[j].text==selectSource[i].text) selectTarget[j]=null;
}

function delAllItem(){
	for(i=0;i<selectTarget.length;i++){
		selectSource.add(new Option(selectTarget[i].text,selectTarget[i].value));
		
	}
	selectTarget.length=0;
}
function addAllItem(){
	for(i=0;i<selectSource.length;i++){
		selectTarget.add(new Option(selectSource[i].text,selectSource[i].value));
		
	}
	selectSource.length=0;
}

function getReturnValue(){
	for(i=0;i<selectTarget.length;i++){
		selectTarget[i].selected = true;
	}
}

function loadBar(fl)
//fl is show/hide flag
{
  var x,y;
  if (self.innerHeight)
  {// all except Explorer
    x = self.innerWidth;
    y = self.innerHeight;
  }
  else 
  if (document.documentElement && document.documentElement.clientHeight)
  {// Explorer 6 Strict Mode
   x = document.documentElement.clientWidth;
   y = document.documentElement.clientHeight;
  }
  else
  if (document.body)
  {// other Explorers
   x = document.body.clientWidth;
   y = document.body.clientHeight;
  }

    var el=document.getElementById('loader');
	if(null!=el)
	{
		var top = (y/2) - 50;
		var left = (x/2) - 150;
		if( left<=0 ) left = 10;
		el.style.visibility = (fl==1)?'visible':'hidden';
		el.style.display = (fl==1)?'block':'none';
		el.style.left = left + "px"
		el.style.top = top + "px";
		el.style.zIndex = 2;
	}
}