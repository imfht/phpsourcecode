//初始函数
function initbodys(){
	
}

function changesubmit(d){
	if(d.tqenddt && d.tqenddt>=d.enddt)return '提前终止日期必须小于截止日期';
	if(d.startdt>=d.enddt)return '截止日期必须大于开始日期';
}