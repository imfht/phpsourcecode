<li>
    <a href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>">
       <div class="newstil">
           <h3 <?php echo $data['style']; ?>><?php echo $data['title']; ?></h3>
           <span><?php echo date('[Y-m-d]',strtotime($data['dtTime'])); ?></span>
       </div>
       <p><?php echo $data['content']; ?></p>
    </a>
</li>