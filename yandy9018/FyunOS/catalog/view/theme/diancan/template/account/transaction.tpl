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
 <?php if ($transactions) { ?>
                      
 
     
    
      
        <table>
        <thead>
          <tr>
            <th><?php echo $column_date_added; ?></th>
            <th><?php echo $column_description; ?></th>
            <th>合计</th>
          </tr>
          
        </thead>
        <tbody>
           <?php foreach ($transactions  as $transaction) { ?>
          <tr>
            <td><?php echo $transaction['date_added']; ?></td>
            <td><?php echo $transaction['description']; ?></td>
            <td><?php echo $transaction['amount']; ?></td>
          </tr>
               <?php } ?> 
     
        </tbody>
      </table>
  
	    <?php } else { ?>
        还没有充值记录哦！
         <?php } ?>
          <div class="textBreakBottom"></div>
         <?php echo $pagination; ?>
    </div>
    
    </div>
    
    </div>


	</body>
</html>