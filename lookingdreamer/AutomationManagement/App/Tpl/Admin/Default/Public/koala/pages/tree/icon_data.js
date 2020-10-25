
var treeData = [];
for(var i=1;i<=10;i++){
	var p1 = {};
	p1.pid = "root";
	p1.id = "" + i;
	p1.title = "节点" + i;
	p1.href="#";
	p1.children = [];
	for(var j=1;j<=10;j++){
		var child = {};
		child.pid = p1.id;
		child.title = "节点" + i * 100 + j;
		child.id = "" + i * 100 + j;
		child.href = "#";
		child.children = [];
		if(j==3){
			child.icon = "icon-hand-right";
		}
		if(j==5){
			child.icon = "icon-hand-left";
		}
		/*for(var k=1;k<=5;k++){
			var child2 = {};
			child2.pid = child.id;
			child2.title = "节点" + (i * 100 + j) * 100 + k;
			child2.id = "" + (i * 100 + j) * 100 + k;
			child2.href="#";
			child2.children = [];
			
			child.children.push(child2);
		}*/
		p1.children.push(child);
	}
	
	treeData.push(p1);
}