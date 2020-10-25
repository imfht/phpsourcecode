var contents = [];
for(var i=0;i<10;i++){
	var content = {};
	content.title = "第" + (i+1) + "个";
	content.value = i+1;
	content.selected = i==0?true:false;
	
	contents.push(content);
}