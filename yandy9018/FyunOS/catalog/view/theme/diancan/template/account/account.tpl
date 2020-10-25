<?php echo $header; ?>

<body> 
<!-- website wrapper starts -->
<div class="websiteWrapper"> 
  <!-- page wrapper starts -->
  <div class="pageWrapper loginPageWrapper"> 
 <?php echo $nav; ?>
 
 <div class="nmtop">
<div class="tname"><?php echo $name; ?><span><?php echo $group; ?></span></div>
 <div class="newsmembox" style="margin:10px;">
 
 <div class="columnWrapper oneThird">
       <div class="box"><div class="nmuen1">No.<?php echo $id; ?></div><div class="nmuen2">会员ID</div></div>
      </div>
      
       <div class="columnWrapper oneThird">
       <div class="box"><a href="<?php echo $transaction; ?>"><div class="nmuen1"><?php echo $total; ?>元</div><div class="nmuen2">储值卡</div></a></div>
      </div>

      
      <div class="columnWrapper oneThird lastColumn">
        <div class="box"><a href="<?php echo $reward; ?>"><div class="nmuen1"><?php echo $points; ?></div><div class="nmuen2">积分</div></a></div>
      </div>
 
 </div>
 </div>
  
  <div class="pageContentWrapper">
 <div class="columnWrapper oneThird">
 
<div class="bgstyle">
        <a class="jsurl" rel="<?php echo $order; ?>" href="#"><div class="nmemlist1"><i class="n2"></i></div><div class="nmemlist2"><?php echo $text_my_orders; ?></div></a></div>
      </div>
      
       <div class="columnWrapper oneThird">
       <div class="bgstyle"><a rel="<?php echo $edit; ?>" class="jsurl" href="#"><div class="nmemlist1"><i class="n3"></i></div><div class="nmemlist2"><?php echo $text_edit; ?></div></a></div>
      </div>

      
      <div class="columnWrapper oneThird lastColumn">
     <div class="bgstyle"><a rel="<?php echo $transaction; ?>" class="jsurl" href="#"><div class="nmemlist1"><i class="n4"></i></div><div class="nmemlist2"><?php echo $text_transaction_total; ?></div></a></div>
      </div>
   
       <div class="columnWrapper oneThird">
 
<div class="bgstyle"><a rel="<?php echo $reward; ?>" class="jsurl" href="#"><div class="nmemlist1"><i class="n6"></i></div><div class="nmemlist2"><?php echo $text_reward; ?></div></a></div>
      </div>
      
       <div class="columnWrapper oneThird">
       <div class="bgstyle"><a class="jsurl" rel="index.php?route=information/contact"><div class="nmemlist1"><i class="n1"></i></div><div class="nmemlist2">我要留言</div></a>
        
        </div>
        
      </div>

      
      <div class="columnWrapper oneThird lastColumn">
        
     <div class="bgstyle"><a href="tel:<?php echo $telephone; ?>"><div class="nmemlist1"><i class="n5"></i></div><div class="nmemlist2">呼叫商家</div></a></div>
    
      </div>
    <div class="clear"></div><br>

      <a href="<?php echo $logout; ?>" class="singleProductPurchaseButton">退出登录</a>
</div>

    <!-- footer wrapper starts -->
   
    <!-- footer wrapper ends -->
    
  </div>
  <!-- page wrapper ends --> 
</div>
<!-- website wrapper ends -->
</body>
</html>