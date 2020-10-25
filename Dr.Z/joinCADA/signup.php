<!DOCTYPE html>
<html lang="zh_cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CADA Registering">
    <meta name="author" content="Z4Tech">

    <title>CADA报名名单</title>

    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index">CADA报名</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index">报名</a></li>
            <li><a href="show">列表</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="main">
         <form class="form-horizontal" method="POST" action="new.php">
<fieldset>

<!-- Form Name -->
<legend>CADA报名表</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">姓名</label>  
  <div class="col-md-4">
  <input id="name" name="name" autocomplete="off" type="text" placeholder="你的真实姓名" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="grade">年级</label>  
  <div class="col-md-4">
  <input id="grade" name="grade" autocomplete="off" type="text" placeholder="确定是在校学生" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="id">学号</label>  
  <div class="col-md-5">
  <input id="id" name="id" type="text" autocomplete="off" placeholder="主要用来验证身份……" class="form-control input-md" required="">
    
  </div>
</div>

<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="school">院系</label>
  <div class="col-md-4">
    <select id="school" name="school" class="form-control">
      <option value="信息科学技术学院">信息科学技术学院</option>
      <option value="数学科学学院">数学科学学院</option>
      <option value="物理学院">物理学院</option>
      <option value="化学与分子工程学院">化学与分子工程学院</option>
      <option value="生命科学学院">生命科学学院</option>
      <option value="工学院">工学院</option>
      <option value="城市与环境学院">城市与环境学院</option>
      <option value="地球与空间科学学院">地球与空间科学学院</option>
      <option value="心理学系">心理学系</option>
      <option value="信息管理系">信息管理系</option>
      <option value="新闻与传播学院">新闻与传播学院</option>
      <option value="经济学院">经济学院</option>
      <option value="光华管理学院">光华管理学院</option>
      <option value="社会学系">社会学系</option>
      <option value="中国语言文学系">中国语言文学系</option>
      <option value="历史学系">历史学系</option>
      <option value="考古文博学院">考古文博学院</option>
      <option value="哲学系（宗教学系）">哲学系（宗教学系）</option>
      <option value="外国语学院">外国语学院</option>
      <option value="国际关系学院">国际关系学院</option>
      <option value="法学院">法学院</option>
      <option value="政府管理学院">政府管理学院</option>
      <option value="教育学院">教育学院</option>
      <option value="艺术学院">艺术学院</option>
      <option value="马克思主义学院">马克思主义学院</option>
      <option value="软件与微电子学院">软件与微电子学院</option>
      <option value="其他">其他</option>
    </select>
  </div>
</div>

<!-- Multiple Radios (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="gender">性别</label>
  <div class="col-md-4"> 
    <label class="radio-inline" for="gender-0">
      <input type="radio" name="gender" id="gender-0" value="男" checked="checked">
      男
    </label> 
    <label class="radio-inline" for="gender-1">
      <input type="radio" name="gender" id="gender-1" value="女">
      女
    </label>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="phone">电话</label>  
  <div class="col-md-5">
  <input id="phone" name="phone" autocomplete="off" type="text" placeholder="手机号码" class="form-control input-md" required="">
  <span class="help-block">不填联系不上啊&gt;_&lt;</span>  
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="mail">邮箱</label>  
  <div class="col-md-5">
  <input id="mail" name="mail" autocomplete="off" type="text" placeholder="请填写常用邮箱" class="form-control input-md">
    
  </div>
</div>

<!-- Multiple Checkboxes (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="favor">兴趣</label>
  <div class="col-md-4">
    <label class="checkbox-inline" for="favor-0">
      <input type="checkbox" name="favor[]" id="favor-0" value="移动应用开发">
      移动应用开发
    </label>
    <label class="checkbox-inline" for="favor-1">
      <input type="checkbox" name="favor[]" id="favor-1" value="桌面应用开发">
      桌面应用开发
    </label>
    <label class="checkbox-inline" for="favor-2">
      <input type="checkbox" name="favor[]" id="favor-2" value="Web应用开发">
      Web应用开发
    </label>
    <label class="checkbox-inline" for="favor-3">
      <input type="checkbox" name="favor[]" id="favor-3" value="服务端开发">
      服务端开发
    </label>
    <label class="checkbox-inline" for="favor-4">
      <input type="checkbox" name="favor[]" id="favor-4" value="UI设计">
      UI设计
    </label>
    <label class="checkbox-inline" for="favor-5">
      <input type="checkbox" name="favor[]" id="favor-5" value="Web设计">
      Web设计
    </label>
    <label class="checkbox-inline" for="favor-6">
      <input type="checkbox" name="favor[]" id="favor-6" value="程序测试">
      程序测试
    </label>
    <label class="checkbox-inline" for="favor-7">
      <input type="checkbox" name="favor[]" id="favor-7" value="活动策划">
      活动策划
    </label>
    <label class="checkbox-inline" for="favor-8">
      <input type="checkbox" name="favor[]" id="favor-8" value="宣传推广">
      宣传推广
    </label>
  </div>
</div>

<!-- Multiple Radios (inline) -->
<div class="form-group">
  <label class="col-md-4 control-label" for="work">是否加入骨干</label>
  <div class="col-md-4"> 
    <label class="radio-inline" for="work-0">
      <input type="radio" name="work" id="work-0" value="是">
      是
    </label> 
    <label class="radio-inline" for="work-1">
      <input type="radio" name="work" id="work-1" value="否" checked="checked">
      否
    </label>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4">
    <button id="submit" name="submit" class="btn btn-primary">加入</button>
  </div>
</div>

</fieldset>
</form>


          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/docs.min.js"></script>
  </body>
</html>
