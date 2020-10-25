<div class="layui-tab-item layui-show">
      <!--留言区域-->
      <form class="layui-form" style="width:80%;" id="editchickform">
    <br/>
    <div class="layui-form-item">
      <div class="layui-inline" style="width: 40%">   
        <label class="layui-form-label">标题</label>
        <div class="layui-input-block" >
          <input type="text" name="title" class="layui-input linksTime " autocomplete="off" lay-verify="required" value="<?php echo $title;?>" >
        </div>
      </div>
      <div class="layui-inline">
        <label class="layui-form-label">反馈类型</label>
        <div class="layui-input-inline">
          <select name="type" class="newsLook" lay-filter="browseLook" lay-verify="required" lay-filter="type">
            <option value="">请选择类型</option>
            <?php 
            foreach(gettypelist() as $value){ 
            echo '<option value="'.$value["id"].'" ';
            echo ($type==$value["id"]) ? "selected" : ""; 
            echo '>'.$value["name"].'</option>';
            } 
            ?>
            </select>
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-form-item">
        <label class="layui-form-label">反馈信息</label>
        <div class="layui-input-block">
          <textarea placeholder="请输入您要反馈的内容" name="content" class="layui-textarea linksDesc" lay-verify="required" id="content"><?php echo $content;?></textarea>
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-inline" >   
        <label class="layui-form-label">姓名</label>
        <div class="layui-input-block" >
          <input type="text" name="name" class="layui-input linksTime " lay-verify="required" autocomplete="off" value="<?php echo $name?>"  >
        </div>
      </div>
      <div class="layui-inline" >   
        <label class="layui-form-label">联系电话</label>
        <div class="layui-input-block" >
          <input type="text" name="phone" class="layui-input linksTime "  autocomplete="off" lay-verify="required|phone" value="<?php echo $phone?>">
        </div>
      </div>
      <div class="layui-inline" >   
        <label class="layui-form-label">发布时间</label>
        <div class="layui-input-block" >
          <input type="text" name="phone" class="layui-input linksTime "  autocomplete="off" lay-verify="" value="<?php $date=date('Y年m月d日 H:m:s',$date); echo $date?>" disabled>
        </div>
      </div>
     </div> 
    <div class="layui-form-item">
       <div class="layui-inline" id="email">    
        <label class="layui-form-label">邮件地址</label>
        <div class="layui-input-inline">
          <input type="text" name="email" id="emailinput" class="layui-input linksTime" autocomplete="off" value="<?php echo $email?>">
        </div>
      </div>
      <div class="layui-inline">
        <label class="layui-form-label">首页显示</label>
        <div class="layui-input-inline">
          <select name="view" class="newsLook" lay-filter="browseLook" lay-verify="required" lay-filter="type">
            <option value="1" <?php  echo ($view==1) ? "selected" : ""; ?> >显示</option>
            <option value="0" <?php  echo ($view==0) ? "selected" : ""; ?> >不显示</option>
            </select>
        </div>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">留言城市</label>
        <div class="layui-input-block" >
         <div class="layui-form-mid layui-word-aux">
        <?php
        if($system_viewcity=="on"){
        $city = getCity($ip);
        echo $city["country"]."-".$city["region"]."-".$city["city"];
        }else{
          echo "未开启显示位置";
        }
        ?>
        </div>
        </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-input-block">
        <a class="layui-btn" lay-submit="" lay-filter="editchick" id="submit">立即提交</a>
        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
  </form>
</div>