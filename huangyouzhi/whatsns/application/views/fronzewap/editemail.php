<!--{template header}-->
<style>
.control-label{
font-weight:700;
margin:10px auto;
}
.checkbox{
line-height:30px;
}
hr {
    margin-top: 10px;
    margin-bottom: 10px;
    border: 0;
    border-top: 1px solid #eee;
}
</style>
<section class="ui-container">
<!--{template user_title}-->
    <ul class="ui-tab-nav ui-border-b">

        <li class="current"><a href="{url user/editemail}">激活邮箱账号</a></li>
        
   <li class=""><a href="{url user/editphone}">激活手机号</a></li>
    </ul>
    <section class="ui-panel ui-panel-pure ui-border-t">
    
                     <div style="padding:10px;">
                   <label for="" class="control-label col-sm-4">邮箱激活/修改</label>
     <hr>
                  {if $user['active']==0}


                 
                 <div class="ui-tooltips ui-tooltips-guide">
    <div class="ui-tooltips-cnt  ui-border-b">
        <i class="fa fa-info"></i>      邮箱没有激活
    </div>
</div>
                     {else}
            
                 <div class="ui-tooltips ui-tooltips-guide">
    <div class="ui-tooltips-cnt  ui-border-b">
        <i class="fa fa-info"></i>              邮箱已经激活
    </div>
</div>

    
               {/if}
           </div>
    

                 <form class="profileform ui-form"  method="POST" name="upinfoForm"  action="{url user/editemail}" >
      <input type="hidden" name="formkey" id="formkey" value="{$_SESSION['formkey']}" >

     
          <div class="ui-form-item ui-border-b">
            <label>
               邮箱账号
            </label>
            <input type="text" autocomplete="off" name="email" id="email"  value="{$user['email']}" placeholder="输入个人邮箱账号">
            <a href="#" class="ui-icon-close">
            </a>
        </div>
         {if $user['active']==0}
        <div class="ui-form-item  ui-border-b">
            
              <button class="ui-btn" id="sendvertifile" >邮箱激活验证发送</button>
           
        </div>
          {/if}
       
 
       
             <div class="ui-row">
        
          <div class="ui-col ui-col-50 ui-form-item ui-form-item-pure ui-border-b">
               <input type="text" id="code" name="code" placeholder="输入验证码">
          </div>
        
        
             <div class="ui-col ui-col-25">
             <!-- 若按钮不可点击则添加 disabled 类 -->
                <button type="button" class="ui-border-l"><img class="ui-border-l" src="{url user/code}" onclick="javascript:updatecode();" id="verifycode"></button>
         
           </div>
        </div>
         
         
                       {if $user['active']==0}
    
  <div class="ui-btn-wrap">
            <button type="submit" name="submit" id="submit" value="submit" class="ui-btn-lg ui-btn-primary">
保存并激活
            </button>
        </div>
        
               {else}

 <div class="ui-btn-wrap">
            <button type="submit" name="submit" value="submit" id="submit" class="ui-btn-lg ui-btn-primary">
修改并重新激活
            </button>
        </div>
        
     
               {/if}
               
     
 </form>
 
</section>
              
         
  
           
             
            
</section>
 <!--用户中心结束-->
  {if $user['active']==0}
<script>
$("#sendvertifile").click(function(){
	
   var _formkey=$("#formkey").val();
   var email='{$user['email']}';
   if($.trim(email)==''||$.trim(email)=='null'||email=='undefined'){
	   alert("您还没设置过邮箱，请先点击保存按钮保存邮箱");
	   return false;
   }
   if(confirm("您将要激活{$user['email']},如果不想激活当前邮箱，请先修改保存在激活，系统将会发送激活邮件")){
    $.ajax({
        //提交数据的类型 POST GET
        type:"POST",
        //提交的网址
        url:'{url user/sendcheckmail}',
        data:{formkey:_formkey},
        //返回数据的格式
        datatype: "text",//"xml", "html", "script", "json", "jsonp", "text".
        
        //成功返回之后调用的函数
        success:function(data){
        	$(".messagetip").html(data);
          $("#modeltip").modal("show");
      
        }   ,
       
        //调用出错执行的函数
        error: function(){
            //请求出错处理
        }
    });	
   }
})
</script>
{/if}
 
<!--{template footer}-->