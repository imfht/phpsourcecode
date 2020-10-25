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
<!--用户中心-->


    <div class="container person">

              <div class="row " >
   <!--{template user_title}-->
       <ul class="ui-tab-nav ui-border-b">

        <li class=""><a href="{url user/editemail}">激活邮箱账号</a></li>
        
   <li class="current"><a href="{url user/editphone}">激活手机号</a></li>
    </ul>
           <div class="col-md-12">
           

                 <div style="padding:10px;">
                   <label for="" class="control-label col-sm-4">手机号码激活/修改</label>
     <hr>
                  {if $user['phoneactive']==0}


                 
                 <div class="ui-tooltips ui-tooltips-guide">
    <div class="ui-tooltips-cnt  ui-border-b">
        <i class="fa fa-info"></i>      手机号没有激活
    </div>
</div>
                     {else}
            
                 <div class="ui-tooltips ui-tooltips-guide">
    <div class="ui-tooltips-cnt  ui-border-b">
        <i class="fa fa-info"></i>              手机号已经激活
    </div>
</div>

    
               {/if}
           </div>


  <div class="ui-form ui-border-t">
    <form action="{url user/editphone}" method="post">
        <div class="ui-form-item ui-border-b">
            <label>
               手机号码
            </label>
            <input type="text" name="userphone" autocomplete="off" id="userphone"  value="{$user['phone']}" maxlength="11" placeholder="11位数手机号码">
            <a href="#" class="ui-icon-close">
            </a>
        </div>
        <div class="ui-form-item  ui-border-b">
            
              <a class="ui-btn" onclick="gosms()" id="testbtn">发送验证码</a>
           
        </div>
        <div class="ui-form-item  ui-border-b">
            <label>
                验证码
            </label>
             <input autocomplete="off" name="code" id="code" type="text" maxlength="10" placeholder="手机短信验证码">
            <a href="#" class="ui-icon-close">
            </a>
        </div>
        
              {if $user['phoneactive']==0}
    
  <div class="ui-btn-wrap">
            <button type="submit" name="submit" id="submit" value="submit" class="ui-btn-lg ui-btn-primary">
              激活验证
            </button>
        </div>
        
               {else}

 <div class="ui-btn-wrap">
            <button type="submit" name="submit" value="submit" id="submit" class="ui-btn-lg ui-btn-primary">
    重新激活短信验证
            </button>
        </div>
        
     
               {/if}
        
        
      
    </form>
</div>
 




<!--{template footer}-->