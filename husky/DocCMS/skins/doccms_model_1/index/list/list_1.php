<div class="col-3">
    <div class="home-content">
        <h4><?php echo $data['title']; ?></h4>
        <img src="<?php echo ispic($data['originalPic']); ?>" style="float:left; margin-right:15px;" width="120" height="120"/>
        <p> <?php echo $data['description']; ?>[<a href="<?php echo sys_href($data['channelId'],'list',$data['id'])?>">详细>></a>]</p>
    </div>
</div>