<?php echo $header; ?>

<body> 
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper loginPageWrapper"> 
   <?php echo $nav; ?>
    <!-- header outer wrapper ends --> 
    
    <!-- page content wrapper starts -->
    
    <div class="pageContentWrapper"> 
      
      <!-- page title starts -->
      <h3 class="pageTitle">登录</h3>
      <!-- page title ends -->
      <p><?php if ($ubind==1) { ?>
系统未检测到您的绑定信息，请返回首页输入"绑定"或任何信息即可点击绑定。<br>
您也可以通过手机号码在以下进行登录。</p>
     <?php } ?>
      <!-- login form wrapper starts -->
      <div class="loginFormWrapper">
       <div id="ajaxForm">
          <fieldset>
            <div class="formFieldWrapper">
              <label for="loginNameField">手机号码:</label>
              <input type="text" value="" id="telephone" class="loginNameField fieldWithIcon userFieldIcon" name="telephone" />
            </div>
            
       <div class="columnWrapper oneHalf">
       <div class="formFieldWrapper">
            
              <label for="loginPasswordField">验证码:</label>
              <input type="text" value="" id="codeRand" class="loginPasswordField fieldWithIcon passwordFieldIcon" name="codeRand" />
              
            </div>
      </div>
      
      
      <div class="columnWrapper oneHalf lastColumn">
     <div id="btn" class="formFieldWrapper">
      <label for="kong"></label>
      <a id="cr" href="#" class="buttonWrapper buttonBlue cr">获取验证码</a>
      </div>
      </div>
            <div class="clear"></div>
            
           
            <div class="loginButtonsWrapper"> <a href="register.html" class="loginRegisterButton">有问题？</a>
              <input type="submit" value="登录" class="loginButton" id="login">
            </div>
          </fieldset>
       </div>
      </div>
      <!-- login form wrapper ends -->
      
    </div>
    <!-- page content wrapper ends -->

    
  </div>
  <!-- page wrapper ends --> 
</div>
<!-- website wrapper ends -->
</body>
</html>
<script>

  $$('#login').tap(function() { 
		  if($('input[name=\'telephone\']').val()==""){
			    $$("#progressBar").html('请填写电话');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
				 
			  return false; 
			  }
		   if($('input[name=\'codeRand\']').val()==""){
			  	$$("#progressBar").html('请填写电话');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500); 
			  return false; 
			  
			  }else{
				    $.ajax({ 
					type : "GET", 
					url  : "index.php?route=common/ajaxCaptcha/verification&codeRand="+$('input[name=\'codeRand\']').val()+"&m=<?php echo SNAME; ?>",  
					success : function(result){
						
						  if (result == "A") {
								 $$("#progressBar").html('正在登录...');
				                 $$("#progressBar").show();
								 setTimeout(function(){
  $.ajax({ 
											  type : "post", 
											  url  : "index.php?route=account/login&m=<?php echo SNAME; ?>",  
											  async: false ,
											  cache : false, 
											  data: 'telephone=' + $('input[name=\'telephone\']').val(),
											  success :function(){
												    $$("#progressBar").html('登录成功');
				                                    $$("#progressBar").show();
												   window.location.href="<?php echo $track_url; ?>"; 
												  }
										  }); 

								},1000); 
							 

					     	return true;
						  }
						  else {
							 $$("#progressBar").html('验证码错误');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
							  return false;
						  }
						 
					}, 
				}); 
				// return false; 
	      }
		
        }); 	
		
  $$('#cr').tap(function() { 
   $$("#progressBar").html('正在获取...');
   $$("#progressBar").show();
			if($('input[name=\'telephone\']').val()){
			//$('input[name=\'telephone\']').attr("disabled","disabled");
				 $.ajax({ 
					type : "POST", 
					url  : "index.php?route=common/ajaxCaptcha&telephone="+$('input[name=\'telephone\']').val()+"&m=<?php echo SNAME; ?>",  
					success : function(result){
						  if (result == "Y") {
							  $$("#progressBar").html('已发送！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
							  //$('input[name=\'codeRand\']').removeClass("ui-state-disabled");
							   //$('input[name=\'telephone\']').addClass("ui-state-disabled");
							 startCount();//开始倒计时
							
						  }
						  else {
							 $$("#progressBar").html('错误！请重新获取！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
						  }
					}, 
					error: function() { alert("error"); }
				}); 
			} else{
				$$("#progressBar").html('请填写手机号码');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
				}

        }); 
		
//验证码有效期倒计时
  var Time=300;//5分钟
  //定义定时器的id
  var listenid;
  var count=Time;
  
  function startCount(){
	  $('.cr').attr('id','noid');
	 //$("#cr").addClass("ui-state-disabled");
	 //$("#cr1").addClass("ui-state-disabled");
	 $('#noid').html("(" +count + ")秒后获取");
	  $('#noid').html("(" +count + ")秒后获取");
	  count=count-1;
	  listenid=setTimeout("startCount()",1000)
	   if(count<0){
		  count=Time;
		  stopCount();
	 //$('input[name=\'codeRand\']').addClass("ui-state-disabled");
	//$('#cr').removeClass("ui-state-disabled");
	//$('#cr1').removeClass("ui-state-disabled");
	  $('.cr').attr('id','cr');
	   $('#cr').html("获取验证码");
	  // $('#cr1').html("获取验证码");
	  }
  }
//停止计时
  function stopCount(){
	clearTimeout(listenid);
  }
</script>


