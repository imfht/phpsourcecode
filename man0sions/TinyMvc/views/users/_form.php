
<form  method="post"  enctype="application/x-www-form-urlencoded">
    <div class="form-group">
        <label>名称</label>
        <input class="form-control" name="name" value="<?php echo @$user['name']?>">

    </div>



    <div class="form-group">
        <label>密码</label>
        <input type="password" name="password" value="<?php echo @$user['password']?>"/>
    </div>


    <button class="btn btn-primary" type="submit" >提交</button>
    <button class="btn btn-success" type="reset">重置</button>
</form>
