<?php 
use SCH60\Kernel\App;
use SCH60\Kernel\KernelHelper;
use SCH60\Kernel\StrHelper;
?>
<div class="container">
    <p>欢迎登录。</p>
    
    <div class="alert alert-warning" role="alert">注意：长时间不操作（尤其大于10分钟），系统将自动退出。</div>
    
    <hr />
    
    <p><b>产品信息</b></p>
    <p>产品名称：<?=StrHelper::O(KernelHelper::config('product_name'));?></p>
    <p>版本号：<?=StrHelper::O(KernelHelper::config('product_release_ver'));?></p>
    <p>框架版本号：<?=APP::VERSION;?> Build <?=APP::BUILD_VERSION;?></p>
    
    <p></p>
</div>