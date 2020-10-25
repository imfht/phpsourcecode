//返回个人信息 
function returnUserinfo(){
	jQuery('#useri_infos').slideDown();
	jQuery('#footer').slideDown();
	jQuery('#useri_nickname').slideUp();
	jQuery("#useri_sex").slideUp();
}
//修改昵称面板
function openNickName(){
	jQuery('#useri_infos').slideUp();
	jQuery('#footer').slideUp();
	jQuery('#useri_nickname').slideDown();
}
//修改昵称
function editNickName(){
    var userName = $('#userName').val();
    if(userName==''){
    	WST.msg('昵称不能为空','info');
	    $('#userName').focus();
        return false;
    }
    $('.nickname_onclick').attr("onclick", "null"); 
    $.post(WST.U('mobile/users/editUserInfo'), {userName:userName}, function(data){
        var json = WST.toJson(data);
        if(json.status == '1'){
        	WST.msg(json.msg,'success');
            $('#nickname').html(userName);
    	    setTimeout(function(){
    	    	setTimeout(function(){
        	    $('.nickname_onclick').attr("onclick", "editNickName()"); 
    	    	},1000);
    	    	returnUserinfo();
    	    },1500);
        }else{
        	WST.msg('修改昵称失败，请重试','warn');
	    	setTimeout(function(){
        	    $('.nickname_onclick').attr("onclick", "editNickName()"); 
    	    	},1500);
            return false;
        }
    });
}
//修改性別面板
function openUserSex(){
	jQuery('#useri_infos').slideUp();
	jQuery('#footer').slideUp();
	jQuery("#useri_sex").slideDown();
}
//修改性别
function eidtUserSex(obj, userSex){
	$(obj).children('.wst-list-infose2').html('<i class="ui-icon-checked-s wst-icon-checked-s_se"></i>');
	$(obj).siblings().children('.wst-list-infose2').html('');
    $('.wst-listse').attr("onclick", "null");
    $.post(WST.U('mobile/users/editUserInfo'), {userSex:userSex}, function(data){
        var json = WST.toJson(data);
        if(json.status == '1'){
            var newUserSex = '';
            if(userSex==0){
                newUserSex = '保密';
            }else if(userSex==1){
                newUserSex = '男';
            }else if(userSex==2){
                newUserSex = '女';
            }
            WST.msg(json.msg,'success');
            $('#usersex').html(newUserSex);
    	    setTimeout(function(){
    	    	returnUserinfo();
    	    	setTimeout(function(){
            	    $('.wst-listse1').attr("onclick", "eidtUserSex(this, 0)");
            	    $('.wst-listse2').attr("onclick", "eidtUserSex(this, 1)"); 
            	    $('.wst-listse3').attr("onclick", "eidtUserSex(this, 2)"); 
    	    	},1000);
    	    },1500);
        }else{
        	WST.msg('修改性别失败，请重试','warn');
	    	setTimeout(function(){
        	    $('.wst-listse1').attr("onclick", "eidtUserSex(this, 0)");
        	    $('.wst-listse2').attr("onclick", "eidtUserSex(this, 1)"); 
        	    $('.wst-listse3').attr("onclick", "eidtUserSex(this, 2)"); 
	    	},1000);
            return false;
        }
    });
}
/*签到*/
function inSign(){
	$("#j-sign").attr('disabled', 'disabled');
	$.post(WST.U('mobile/userscores/signScore'),{},function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			$("#j-sign").addClass('sign2');
			$("#j-sign").removeAttr('onclick');
			$("#currentScore").html(json.data.totalScore);
			WST.msg(json.msg,'success');
		}else{
			$("#j-sign").removeClass('sign2');
			WST.msg(json.msg,'warn');
			$("#j-sign").removeAttr('disabled');
		}
	});
}
function bindPhone(){
	location.href=WST.U('mobile/users/editPhone');
}
$(document).ready(function(){
	if(WST.conf.IS_LOGIN==0){//是否登录
		// WST.inLogin();
		// return;
	}
	if(parseInt($('#pageId').val()) == 0){
		WST.initFooter('user');
	}else{
		WST.selectCustomMenuPage('user');
	}
    WST.imgAdapt('j-imgAdapt');
})


