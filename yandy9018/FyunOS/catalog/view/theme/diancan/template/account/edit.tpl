<?php echo $header; ?>
<body> 
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper registerPageWrapper"> 
 <?php echo $nav; ?>
    <!-- page content wrapper starts -->
    <div class="pageContentWrapper"> 
      <script>
$(function() { 
//$('input[name=\'codeRand\']').attr("disabled","disabled");
  $$('#gx').tap(function() { 
		  if($('input[name=\'firstname\']').val()==""){
			    $$("#progressBar").html('请填写姓名');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
			   return false; }
		  if($('input[name=\'telephone\']').val()==""){
			  $$("#progressBar").html('请填写电话');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
			   return false; }
		   if($('select[name=\'zone_id\']').val()==""){
			   $$("#progressBar").html('请填写区域');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
			    return false; }
		    if($('select[name=\'city_id\']').val()==""){
				$$("#progressBar").html('请填写街道');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
				 return false; }
		  if($('input[name=\'address\']').val()==""){
			  $$("#progressBar").html('请填写地址');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
			   return false; }
		  if($('input[name=\'codeRand\']').val()==""){
			 $$("#progressBar").html('请填写验证码');
				$$("#progressBar").show();
		        $("#progressBar").delay(500);
				$("#progressBar").fadeOut(500);
			  return false; 
			  
			  }else{
				   $$("#progressBar").html('正在更新');
				   $$("#progressBar").show();
				  $.ajax({ 
					type : "GET", 
					url  : "index.php?route=common/ajaxCaptcha/verification&codeRand="+$('input[name=\'codeRand\']').val()+"&m=<?php echo SNAME; ?>",  
					success : function(result){
						  if (result == "A") {
							 var formData = $('#ajaxForm').serialize(); 
								//alert(1);
								//.serialize() 方法创建以标准 URL 编码表示的文本字符串 
								$.ajax({ 
									type : "post", 
								    async: false ,
									url  : "index.php?route=account/edit&m=<?php echo SNAME; ?>",  
									data : formData, 
									success : function(){
										$$("#progressBar").html('更新成功！');
										$$("#progressBar").show();
										$("#progressBar").delay(1000);
										$("#progressBar").fadeOut(500);
										},
								}); 
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

            return false; 
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
							  
							 // $('input[name=\'codeRand\']').removeClass("ui-state-disabled");
							  // $('input[name=\'telephone\']').addClass("ui-state-disabled");
							 startCount();//开始倒计时
							
						  }
						  else {
							 $$("#progressBar").html('获取失败！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(1000);
							  $("#progressBar").fadeOut(500); 
						  }
					}, 
					error: function() { alert("error"); }
				}); 
			} else{
				 $$("#progressBar").html('请填写手机号！');
							  $$("#progressBar").show();
							  $("#progressBar").delay(500);
							  $("#progressBar").fadeOut(500); 
				}

        }); 
    }); 
  
  function changeZone(zoneid){ 
  $('select[name=\'city_id\']').load("index.php?route=common/localisation/city&zone_id="+zoneid+"&m=<?php echo SNAME; ?>",function(){
	   document.all.city_id.options[0].selected=true;
	  
  });
  }
  
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
  
  
  function random() {
		var num = "";
		for (i = 0; i < 6; i++) {
			num = num + Math.floor(Math.random() * 10);
		}
		return num;
	}  
</script>
      
      <!-- page title starts -->
      <h3 class="pageTitle">个人资料</h3>
      <!-- page title ends -->
      

      
      <!-- register form wrapper starts -->
      <div class="registerFormWrapper">
        <form action="#" method="post" id="registerForm" class="registerForm">
          <fieldset>
            <div class="formFieldWrapper">
              <label for="registerFirstNameField">姓名:</label>
              <input type="text" id="firstname" class="registerFirstNameField fieldWithIcon userFieldIcon"	name="firstname" value="<?php echo $firstname; ?>" />
            </div>
            
            
            <div class="formFieldWrapper">
              <label for="registerLastNameField">电话:</label>
            <input  value="<?php echo $telephone; ?>" id="telephone" class="registerPhoneField fieldWithIcon phoneFieldIcon" name="telephone" type="text">
            
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
      
      
              <div class="columnWrapper oneHalf">
 
              <div class="formFieldWrapper">
              <label for="zone_id">区域</label>

     <select id="zone_id" name="zone_id" onChange="changeZone(this.options[this.options.selectedIndex].value)">
               </select>  
           
      </div>
         </div>
      
      <div class="columnWrapper oneHalf lastColumn">
  <div class="formFieldWrapper">
         
              
      
       <label for="city_id">街道</label>
     <select  id="city_id" name="city_id">
    
                  </select>         
          </div>
      </div>
        <div class="clear"></div>
      <script>
 $('select[name=\'zone_id\']').load('index.php?route=common/localisation/zone&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>&m=<?php echo SNAME; ?>',function(){
	
	  });

    $('select[name=\'city_id\']').load('index.php?route=common/localisation/city&zone_id=<?php echo $zone_id; ?>&city_id=<?php echo $city_id; ?>&m=<?php echo SNAME; ?>',function(){
		
		
		});
</script>
            <div class="formFieldWrapper">
              <label for="registerUserNameField">地址:</label>
              <input type="text"  value="<?php echo $address; ?>" id="address" class="registerUserNameField fieldWithIcon addressFieldIcon" name="address" />
            </div>
           
          </fieldset>
        </form>
        
      </div>
      <input value="更新" class="registerButton" id="gx" type="submit">
      <!-- register form wrapper ends -->
      
    </div>
    <!-- page content wrapper ends -->
    

    <!-- footer wrapper starts -->
 
    <!-- footer wrapper ends --> 
    
  </div>
  <!-- page wrapper ends --> 
</div>
<!-- website wrapper ends -->
</body>
</html>

