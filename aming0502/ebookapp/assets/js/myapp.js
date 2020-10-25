var lastActive;
var currentActive;

function loadPage(id,page){
    htmlobj=$.ajax({url:page,async:false});
    document.getElementById("myapp-right").innerHTML=htmlobj.responseText;
    $("table").tablesorter({
        // 对第一列和第三列排序, order asc
        sortList: [[0,0],[2,0]]
    });
}

function active(obj){
    if(lastActive){
        lastActive.setAttribute('class','');
    }

    currentActive = obj.parentElement;
    currentActive.setAttribute('class','active');
    lastActive = currentActive;
}

function loadCommonFriend(pageNumber){
    var jobName = document.getElementsByName("jobName")[0].value;
    /*var pageNumber = document.getElementById("pageNumber").value;*/
    var pageSize = document.getElementsByName("pageSize")[0].value;
    var weiboType = escape(document.getElementsByName("weiboType")[0].value);

    page = "/commonFriendAjax.do";
    htmlobj=$.ajax({url:page,async:false,data:{jobName:jobName,pageNumber:pageNumber,pageSize:pageSize,weiboType:weiboType}});
    document.getElementById("commonFriend").innerHTML=htmlobj.responseText;
}

function loadGenCommonFriend(){
    var jobName = document.getElementById("jobName").value;
    var setID = document.getElementById("setID").value;
    var totalUsers = document.getElementById("totalUsers").value;
    var cosBottom      = document.getElementById("cosBottom").value;
    page = "/genClustersAjax.do";
    htmlobj=$.ajax({url:page,async:false,data:{jobName:jobName,setID:setID,totalUsers:totalUsers,cosBottom:cosBottom}});
    alert(htmlobj.responseText);
    //document.getElementById("commonFriend").innerHTML=htmlobj.responseText;
}

function modify_model(obj){
    var trElement = obj.parentElement.parentElement;
    $("#regModal input[name='modelName']")[0].value=trElement.childNodes[3].textContent;
    $("#regModal input[name='modelId']")[0].value=trElement.childNodes[1].textContent;
    $("#regModal input[name='remark']")[0].value=trElement.getElementsByTagName("a")[1].title;
    $("#regModal").modal('show');
}

function loadClassifyDetails(pageNumber){
    var modelName = document.getElementsByName("modelName")[0].value;
    var codeId = document.getElementsByName("codeId")[0].value;
    page = "/classifyDetailTotalAjax.do";
    htmlobj=$.ajax({url:page,async:false,data:{codeId:codeId,modelName:modelName,pageNumber:pageNumber,pageSize:20}});
    document.getElementById("commonFriend").innerHTML=htmlobj.responseText;
}



function editModel(){
    var modelId = document.getElementsByName("modelId")[0].value;
    var modelName = escape(document.getElementsByName("modelName")[0].value);
    var remark =  escape(document.getElementsByName("remark")[0].value);
    $('#regModal').modal('hide');
    page = "/editClassifyModel.do";
    htmlobj=$.ajax({url:page,async:false,data:{modelId:modelId,modelName:modelName,remark:remark}});
    loadPage('myapp-right','/classifyModelTotal.do');
}


function loadSinaUsers(pageNumber){
    var targetContainer = document.getElementById("sinaUser_container");
    if(!targetContainer){
        targetContainer = document.getElementById("myapp-right");
    }
    //"myapp-right"
    page = "/viewSinaUsers.do";
    htmlobj=$.ajax({url:page,async:false,data:{pageNumber:pageNumber}});
    targetContainer.innerHTML=htmlobj.responseText;
}


function show_user(modelId){
    page = "/classifyDetailTotalAjax.do";
    htmlobj=$.ajax({url:page,async:false,data:{modelName:modelId,pageNumber:1,pageSize:20}});
    document.getElementById("commonFriend").innerHTML=htmlobj.responseText;
    document.getElementsByName("modelName")[0].value = modelId;
    $("#hdLayer").modal('show');
}

function show_Reguser(modelId){
    page = "/viewSinaUsers.do";
    htmlobj=$.ajax({url:page,async:false,data:{modelId:modelId,pageNumber:1,pageSize:20}});
    document.getElementById("commonFriend").innerHTML=htmlobj.responseText;
    document.getElementsByName("modelName")[0].value = modelId;
    $("#hdLayer").modal('show');
}

//
function loadBigUser(pageNumber){
    var targetContainer = document.getElementById("sinaUser_container");
    if(!targetContainer){
        targetContainer = document.getElementById("myapp-right");
    }
    //"myapp-right"
    page = "/bigUserAjax.do";
    htmlobj=$.ajax({url:page,async:false,data:{pageNumber:pageNumber}});
    targetContainer.innerHTML=htmlobj.responseText;
    $("table").tablesorter({
        // 对第一列和第三列排序, order asc
        sortList: [[0,0],[2,0]]
    });
}

function updateClickTimes(articleid){
    page = "/track.php";
    htmlobj=$.ajax({url:page,async:false,data:{articleid:articleid}});
}

function load_adsence(){
    page = "/admin/adsence.php";
    htmlobj=$.ajax({url:page,async:false,data:{source:"web-site"}});
    document.getElementById("adsence").innerHTML=htmlobj.responseText;
}

function hufen(userId,screen_name,objbutton){
	//alert(screen_name);
	page = "/weibo/hufen.php";
    htmlobj=$.ajax({url:page,async:false,data:{targetId:userId}});
	$retCode = htmlobj.responseText;
	//alert($retCode);
	if($retCode!=1){
		if($retCode==0){
			alert("您还没登陆，请先登陆！");
		}
		if($retCode==21327){
			alert("对方的会话过期，请互粉下一个用户");
		}else{
			alert("错误码："+$retCode);
		}
		
	}else{
		objbutton.innerHTML="已互粉";
		objbutton.disabled=true;
	}
	
	//alert(objbutton.text);
}

//show_Reguser