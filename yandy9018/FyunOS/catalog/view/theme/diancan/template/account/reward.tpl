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
      <h3 class="pageTitle"><?php echo $text_total; ?><?php echo $total; ?></h3>
      <!-- page title ends -->
 <?php if ($rewards) { ?>
                      
 
     
    
      
        <table>
        <thead>
          <tr>
            <th><?php echo $column_date_added; ?></th>
            <th><?php echo $column_description; ?></th>
            <th>积分</th>
          </tr>
          
        </thead>
        <tbody>
           <?php foreach ($rewards  as $reward) { ?>
          <tr>
            <td><?php echo $reward['date_added']; ?></td>
            <td><?php echo $reward['description']; ?></td>
            <td><?php echo $reward['points']; ?></td>
          </tr>
               <?php } ?> 
     
        </tbody>
      </table>
  
	    <?php } else { ?>
        还没有积分记录哦！
         <?php } ?>
          <div class="textBreakBottom"></div>
         <?php echo $pagination; ?>
    </div>
    
    </div>
    
    </div>


	</body>
</html>