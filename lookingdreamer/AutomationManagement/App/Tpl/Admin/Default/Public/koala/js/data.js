
var staticData = [];
for(var i=1;i<=1000;i++){
	var data = {};
	data.id = i;
	data.name = "用户" + i;
	data.roleDesc = "角色" + i;
	for(var j=1;j<=10;j++){
		data["col" + (j+3)] = "(" + i + "," + (3+j) + ")";
	}
	staticData.push(data);
}

var columns = [{title : "角色名称",name : "name",width : 150}, 
			   {title : "角色描述",name : "roleDesc",width : 150}
			  ];

for(var i=1;i<=10;i++){
	var column = {};
	column.title = "第" + (3+i) + "列";
	column.name = "col" + (3+i);
	column.width = 75;
	
	columns.push(column);
}