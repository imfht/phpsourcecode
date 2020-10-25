<?php include 'application/views/admin/public/head.php'?>
<body>
    <form  height="100%" width="100%" name="content" method="post" action="<?php echo site_url('admin/update');?>"/>
    <input type="hidden" name="id" value="<?php echo $set->id;?>">
    <table height="100%" width="100%" cellpadding="0" cellspacing="1" class="table_list">
        <caption>基本设置</caption>
        <tr>
            <td width="40%">网站名称</td>
            <td><input type="text" size="30" value="<?php echo $set->webname;?>" name="webname"><span class="blue"><span class="red">*</span>请输入网站的名称.</span></td>
        </tr> 
        <tr>
            <td>网页关键词</td>
            <td><input type="text" size="50" value="<?php echo $set->keywords;?>" name="keyword"><span class="blue">. 如有多个请用" ，"隔开</span></td>
        </tr> 
        <tr>
            <td>网页描述</td>
            <td><input type="text" size="60" value="<?php echo $set->description;?>" name="description"><span class="blue">. 如有多个请用" ，"隔开</span></td>
        </tr>
        <tr>
            <td>版权信息</td>
            <td><input type="text" size="50" value="<?php echo $set->copyright;?>" name="copyright"></td>
        </tr> 
    </table>
    <div style="text-align:center"><button name="sub" type="submit">提 交</button></div>
</form>
</body>
</html>

