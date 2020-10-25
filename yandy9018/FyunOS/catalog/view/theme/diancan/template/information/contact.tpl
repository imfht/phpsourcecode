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
      <h3 class="pageTitle"><?php echo $heading_title; ?></h3>
      <!-- page title ends -->
 <?php foreach ($msgs as $msg) { ?>   
   <div class="smallPostWrapper noThumbnailPost">
        <div class="postExcerptWrapper">
          <h4 class="smallPostTitle"><?php echo $msg['author']; ?></h4>
          <p><?php echo $msg['message']; ?></p>
        <?php if($msg['reply']) { ?>  
          <div class="postExcerptWrapper">
          <p class="smallPostQuote"><font color="#FF0000">"<?php echo $msg['reply']; ?>"</font><br>
           </p>
        </div>
         <?php } ?>   
        
        </div>
        <div class="smallPostInfoWrapper"><span class="singleIconWrapper singleIconText iconCalendarDark postInfo postDate postInfoNoMargin"><?php echo $msg['date_added']; ?></span></div>
      </div>
    <?php } ?>    
		    <!-- contact form starts -->
        <form action="<?php echo $action; ?>" method="post" class="contactForm" id="contactForm" enctype="multipart/form-data">
          <fieldset>
            <div class="formFieldWrapper">
              <label for="contactNameField">姓名:</label>
              <input type="text" name="name" value="" class="contactField fieldWithIcon userFieldIcon requiredField" id="contactNameField">
            </div>
            <div class="formFieldWrapper">
              <label for="contactEmailField">邮箱:</label>
              <input type="text" name="email" value="" class="contactField fieldWithIcon emailFieldIcon requiredField requiredEmailField" id="contactEmailField">
            </div>
            <div class="formTextareaWrapper">
              <label for="contactMessageTextarea">建议/反馈:</label>
              <textarea name="enquiry" class="contactTextarea  textareaWithIcon messageFieldIcon requiredField" id="contactMessageTextarea"></textarea>
            </div>
            <div class="formSubmitButtonErrorsWrapper"> 
             
              <input type="submit" class="buttonWrapper contactSubmitButton" id="contactSubmitButton" value="提交留言" data-formId="contactForm">
            </div>
          </fieldset>
        </form>
        <!-- contact form ends --> 

      
    </div>
    
    </div>
    
    </div>


	</body>
</html>