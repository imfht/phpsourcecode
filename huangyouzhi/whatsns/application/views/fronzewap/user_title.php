<style>
.icon_setting span{
font-size:18px;"
}
.user-header-info{
height: 85px;
}
.mysignnature{
margin-top:5px;
color: #eee;
}
</style>
<div class="user-header-info">
   <i class="ui-icon-set icon_setting"><span class="">设置</span></i>
    <ul class="ui-row">
        <li class="ui-col ui-col-25">
            <div class="ui-avatar-lg">
                <span style="background-image:url({$user['avatar']})"></span>
            </div>
        </li>
        <li class="ui-col ui-col-75">
              <p>
        
                  <span class="ui-txt-highlight user-name">
                  {$user['username']}
                   {if $user['author_has_vertify']!=false}<i class="fa fa-vimeo {if $user['author_has_vertify'][0]=='0'}v_person {else}v_company {/if}  " data-toggle="tooltip" data-placement="right" title="" {if $user['author_has_vertify'][0]=='0'}data-original-title="个人认证" {else}data-original-title="企业认证" {/if} ></i>{/if}
                  </span>
                  
              </p>
 <div class="mysignnature">{if $user['signature']}$user['signature']{else}设置签名让别人了解你{/if}<i title="修改个人签名" onclick="window.location.href='{url user/profile}'" class="fa fa-edit hand" style="margin-left:5px;"></i></div>
 
                <ul class="ui-user-tiled">
                    <li><div>{$user['answers']}</div><i>回答</i></li>
                    <li><div>{$user['questions']}</div><i>提问</i></li>
                    <li><div>{$user['followers']}</div><i>粉丝</i></li>
                 
                    <li><div>{$user['credit2']}</div><i>财富</i></li>
                    
                </ul>

        </li>
     
        </ul>
        
        <section class="ui-leftsilde">
<div class="ui-actionsheet">  
  <div class="ui-actionsheet-cnt">
    <h4>我的管理中心导航</h4> 

            <button onclick="window.location.href='{url user/usernotify}'">私信和通知设置</button>   
    <button onclick="window.location.href='{url user/editimg}'">修改头像</button>  
     <button onclick="window.location.href='{url user/profile}'">修改个人信息</button>
     <button onclick="window.location.href='{url user/uppass}'">修改密码</button>
       <button onclick="window.location.href='{url user/editemail}'">激活邮箱</button>  
      <button class="ui-actionsheet-del" onclick="window.location.href='{url user/logout}'">
                退出
            </button>
    <button onclick="hidemenu()">取消</button> 
  </div>         
</div>
   
<script type="text/javascript">
$(".icon_setting").click(function(){
	 window.location.href="{url user/default/set}";
	
})
function hidemenu(){
	 $('.ui-actionsheet').removeClass('show');
}
//编辑用户名和权限
function editmodle(){
	$("#mypay").val("{$user['mypay']}")
	 $('#dialogeditusername').dialog('show');
	
}
function change(){
	var _val=$("#mypay").val();
	if(parseInt(_val)<1){
		alert('最小金额不低于一元。')
		
		return false;
	}
	if(parseInt(_val)>20000){
	alert('最大金额不超过2W人民币。')
		
		return false;
	}
}

</script>
    
</section>
</div>