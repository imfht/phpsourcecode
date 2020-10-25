//声明全局变量
var formvalue = "";
var flag = 1;
var index=1;
var firstCell = "";
var secondCell = "";

$(function() {
    //初始化第一行
    firstCell = $("#row0 td:eq(0)").html();
    secondCell = $("#row0 td:eq(1)").html();
});      

//-----------------新增一行-----------start---------------
function insertNewRow() {
    //获取表格有多少行
    var rowLength = $("#orderTable tr").length;
    //这里的rowId就是row加上标志位的组合。是每新增一行的tr的id。
    var rowId = "row" + flag;

    //每次往下标为flag+1的下面添加tr,因为append是往标签内追加。所以用after
    var insertStr = "<tr id=" + rowId + ">"
                  + "<td style='width: 70%'>" + firstCell + "</td>"
                  + "<td style='width: 20%'>" + secondCell + "</td>"
                  + "<td class='text-center'><input type='button' class='btn btn-xs btn-danger' name='delete' value='删除' onclick='deleteSelectedRow(\"" + rowId + "\")' />";
                  +"</tr>";
    //这里的行数减1，是因为要减去顶部的一行，剩下的为开始要插入行的索引

     $("#orderTable tr:eq(" + (rowLength - 1) + ")").after(insertStr); //将新拼接的一行插入到当前行的下面
     //为新添加的行里面的控件添加新的id属性。
     $("#" + rowId + " td:eq(0)").children().eq(0).attr("id", "name" + flag);
     $("#" + rowId + " td:eq(1)").children().eq(0).attr("id", "sort" + flag);
     $("#" + rowId + " td:eq(1)").children().eq(0).attr("value", flag*5);
     
     //每插入一行，flag自增一次
     flag++;
 }
    
//-----------------删除一行，根据行ID删除-start--------    
function deleteSelectedRow(rowID) {
  	if(rowID=="row0"){
  		alert("不能删除！");
  		return;
  	}
     if (confirm("确定删除该行吗？")) {
         $("#" + rowID).remove();
     }
 }

 $("#saveData").click(function(){
	 var ifalg=1;
	 $("#orderTable tr").each(function(i) {
		 if (i >= 1) {
			 var trid = $(this).attr("id");
			 var xh = trid.substring(3);
			 //console.log("xh:"+xh);
			 $(this).children().each(function(j) {
				 var value = $(this).children().eq(0).val();
				 if(j==0 && value==""){
					 $("#name"+xh).next("span").empty();
					 $("#name"+xh).after('<span class="error">请输入小类名称！</span>');
					 ifalg=0;
				 }else if(j==0 && value!=""){
					 $("#name"+xh).next("span").empty();
				 }
				 
				 if(j==1 && value==""){
					 $("#sort"+xh).next("span").empty();
					 $("#sort"+xh).after('<span class="error">请输排序！</span>');
					 ifalg=0;
				 }else if(j==1 && value!=""){
					 var re =  /^[1-9]+[0-9]*]*$/;
					 //console.log(value);
					 if (!re.test(value)) {
						 $("#sort"+xh).next("span").empty();
						 $("#sort"+xh).after('<span class="error">请输大于0的数字！</span>');
						 ifalg=0;
					 }else{
						 $("#sort"+xh).next("span").empty();
					 }
				 }
			 });
		 }
	 });
	 if(ifalg==0){
		 return false;
	 }else{
		 return true;
	 }
 });