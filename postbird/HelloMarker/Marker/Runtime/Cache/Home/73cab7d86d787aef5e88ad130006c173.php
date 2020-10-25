<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="hellomarker,postbird,李瞻文,ptbird">
    <meta name="description" content="hellomarker,一本正经的吃喝玩乐！Powered by postbird!">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>HelloMarker | 一本正经地吃喝玩乐</title>
    <link rel="stylesheet" href="/hellomarkertest/Public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Public/css/font-awesome.min.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/marker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/jquery-clock.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="/hellomarkertest/Marker/Home/View/Public/css/weatherIcon.css">
    <script src="/hellomarkertest/Public/js/jquery-1.10.2.js"></script>
    <script src="/hellomarkertest/Public/js/bootstrap.min.js"></script>
    <script>
      var appUrl="/hellomarkertest/marker.php";
    </script>
    <script src="/hellomarkertest/Marker/Home/View/Public/js/marker.js"></script>
    <script src="/hellomarkertest/Public/js/kindeditor/kindeditor-min.js"></script>
</head>
<body style="background-color:#F4F8FA;letter-spacing:0.2px;">
    <div style="background-color:#fff;">
        <div class="container">
              <div class="log-nav">
              <div style="float:left;">
                   <a href="/hellomarkertest" class="navbar-band logo-a"></a>
                   <span class="slogan visible-lg"> | 一 本 正 经 地 吃 喝 玩 乐 </span>
              </div>
                <ul class="nav navbar-right">
                 <?php if($userLoginFlag == 0): ?><li ><a href="/hellomarkertest/marker.php/Home/User/index">登录</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/User/userSign">注册</a></li>
                <?php else: ?>
                    <li title='我的Marker'><a href="/hellomarkertest/marker.php/Home/Index/myNote/"><i class='fa fa-bookmark-o' ></i></a></li>
                    <li title='我的应用' class="visible-lg"><a href="/hellomarkertest/marker.php/Home/Work/myWork/"><i class='fa fa-cubes' ></i></a></li>
                    <li class="visible-lg dropdown user-header ">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:"><i class='fa fa-user-secret'></i> <i class="fa fa-caret-down"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>">我的主页</a></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/myChange/">编辑个人资料</a></li>
                            <li class="divider"></li>
                            <li><a href="/hellomarkertest/marker.php/Home/User/userLogout">退出</a></li>
                        </ul>
                    </li>
                    <li title='主页' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/Index/myHome/user/<?php echo ($usersessionname); ?>"><i class='fa fa-user-secret' ></i></a></li>
                    <li title='退出' class="hidden-lg"><a href="/hellomarkertest/marker.php/Home/User/userLogout"><i class='fa fa-power-off' ></i></a></li><?php endif; ?>
               </ul> 
               </div>
        </div>
    </div>
    <nav class="navbar navbar-inverse">
        <div class="container">
            <div class="navbar-header ">
              <button type="button" class="navbar-toggle collapsed " data-toggle="collapse" data-target="#nav-header" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand visible-xs" href="#">专 注 生 活 </a>
            </div>
            <div class="collapse navbar-collapse" id="nav-header">
                <ul class="nav navbar-nav ">
                    <li ><a href="/hellomarkertest/marker.php/Home/Index/">首页</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Article/">吾说八道</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all">吃喝玩乐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Work/">一「本」正经</a></li>
                    <li><a href="/hellomarkertest/marker.php/Home/Account/">柴米油盐</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/Weather/">未雨绸缪</a></li>
                    <li ><a href="/hellomarkertest/marker.php/Home/App/">也有APP</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-ul">
                <li><a href="/hellomarkertest/marker.php/Home/Bug/"><i class="fa fa-map-o"></i>&nbsp;&nbsp; 「 略懂七八 」</a></li>
                </ul>
            </div>
        </div>
    </nav>

	<?php if($backFlag == 1): ?><div class="width100-div text-center">
		   <div class="alert alert-danger alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <strong>Error：</strong><font><?php echo ($backInfo); ?></font>
		   </div>
          </div>
    <?php else: endif; ?>
	<div class="width100-div ">
	    <img class="img-responsive center-block" alt="hellomarker" src="/hellomarkertest/Public/images/bug/bugbanner.png" >
	</div>
    <div class="container marketing">
      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="col-lg-4 text-center">
          <img class="img-circle" src="/hellomarkertest/Public/images/bug/bug1.jpg" alt="hellomarker" width="140" height="140">
          <div class="text-left">
	          <h2>简洁</h2>
	          <p class="p-indent">似乎一直以来，我都醒悟得比较晚，比如说大人嘴里面说的懂事，我大概读高中时才对这个有点懵懂吧。</p>
	          <p class="p-indent">再比如存钱，也就是近几年才有这种想法。现在想想，我的青春似乎都浪费在了网络和等待中。</p>
	          <p class="p-indent">一些事，往往只是想想，却从来没有去实现，要么去实现了，却坚持不了一段时间，慢慢的就不想理睬了</p>
	          <p><a class="btn btn-default" href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all" role="button">发现 <i class="fa fa-angle-double-right"></i></a></p>
	      </div>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4 text-center">
          <img class="img-circle" src="/hellomarkertest/Public/images/bug/bug2.jpg" alt="hellomarker" width="140" height="140">
          <div class="text-left">
          	<h2>清爽</h2>
	          <p class="p-indent">我们之间的开始仅仅只是个闹剧。遇到你时，我尚是一张白纸。</p>
	          <p class="p-indent">你不过在纸上写了第一个字，我不过给了一生的情动，心底有了波澜</p>
	          <p class="p-indent">人就得像电池要成双成对的才叫完美，清爽的我们才是完美！</p>
	          <p><a class="btn   btn-warning" href="/hellomarkertest/marker.php/Home/Account/" role="button">最美好的 <i class="fa fa-angle-double-right"></i></a></p>
          </div>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4 text-center">
          <img class="img-circle" src="/hellomarkertest/Public/images/bug/bug3.jpg" alt="hellomarker" width="140" height="140">
          <div class="text-left">
	          <h2>想你所想</h2>
	          <p class="p-indent">不值得回忆的事情之所以常常萦绕脑中，不能忘去，是由于生活太闲散和太单调所致。</p>
	          <p class="p-indent">在这种情形下，你应该为自己找点事情做，或者出去走走，使生活有变化。</p>
	          <p class="p-indent">生活变化了，那原来令你困扰的事就不会再显得那样突出和重要了。</p>
	          <p><a class="btn btn-default" href="/hellomarkertest/marker.php/Home/Weather/" role="button">生活 <i class="fa fa-angle-double-right"></i></a></p>
	      </div>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->


      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">吃喝玩乐，<span class="text-muted">分享只为更多的收获</span></h2>
          <p class="lead"><small>在吃货「横行」的年代里，我向你伸出友好的手，随时随地，发现属于我们的共同娱乐。</small></p>
          <p class="lead"><small>我愿陪你拔掉一棵棵「草」，陪你笑，陪你吃这遍锦绣河山，大江南北。</small></p>
           <p class="lead"><small>沉静而善良，安宁度日，静守流年里简约的幸福，是我能许给你的唯一。</small></p>
        </div>
        <div class="col-md-5">
          <a href="/hellomarkertest/marker.php/Home/Index/markerShare/sid/all"><img class="featurette-image img-responsive center-block" src="/hellomarkertest/Public/images/bug/share.jpg" alt="hellomarker"></a>
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7 col-md-push-5">
          <h2 class="featurette-heading">一「本」正经，<span class="text-muted">记录生活细微的感动</span></h2>
          <p class="lead"><small>在吃喝玩乐的氛围中，来些许假正经，不偏不倚，恰到好处的认真，生活中每件重要的事情不在错过。</small></p>
          <p class="lead"><small>你认真的时候，眼眸瞥过你那沉思的脸庞，就定格在哪里，再也无法离开。</small></p>
          <p class="lead"><small>当然这是属于我们之间的小秘密，我只告诉你，静静地守护着生活中的每个时刻。</small></p>
        </div>
        <div class="col-md-5 col-md-pull-7">
        <a href="/hellomarkertest/marker.php/Home/Work/"><img class="featurette-image img-responsive center-block" src="/hellomarkertest/Public/images/bug/work.jpg" alt="hellomarker"></a>
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">柴米油盐，<span class="text-muted">精打细算的入微关怀</span></h2>
          <p class="lead"><small>不做胆小逃避者，大胆地面对自己一切的抉择，我站在你的身后，默默地为你搭理小生活。</small></p>
          <p class="lead"><small>我不在乎你的“斤斤计较”，只期盼能做永远的陪伴，厘米之毫，理出你的天下。</small></p>
          <p class="lead"><small>担心你厌倦一排排的文字，即使化身色彩，为你描绘生动琐碎，也是一种自豪。</small></p>
        </div>
        <div class="col-md-5">
        <a href="/hellomarkertest/marker.php/Home/Account/"><img class="featurette-image img-responsive center-block" src="/hellomarkertest/Public/images/bug/account.jpg" alt="hellomarker"></a>
        </div>
      </div>
      <hr class="featurette-divider">
      <div class="row featurette">
        <div class="col-md-7 col-md-push-5">
          <h2 class="featurette-heading">未雨绸缪，<span class="text-muted">愿你的每天阳光明媚</span></h2>
          <p class="lead"><small>终有那个时刻，天冷，有人为你披衣，而我，愿为你生活的快乐除掉阴霾，不再「风云难测」。</small></p>
          <p class="lead"><small>努力工作的空余，一些简单的出行小建议，是我和你愉悦的生活的见证。</small></p>
          <p class="lead"><small>我懂你，更愿意聆听你的心声，奔跑吧，阳光在哪，我就在哪。</small></p>
        </div>
        <div class="col-md-5 col-md-pull-7">
        <a href="/hellomarkertest/marker.php/Home/Weather/"><img class="featurette-image img-responsive center-block" src="/hellomarkertest/Public/images/bug/weather.jpg" alt="hellomarker"></a>
        </div>
      </div>

     
    </div><!-- /.container -->
     <hr class="featurette-divider">
       <div class="text-center">
          <h1><small>听我「胡说八道」 ， 我们一起探索未知</small></h1>
          <br>
          <div class="container ">
          	<div class="row">
          		<div class="col-md-2  visible-lg"></div>
		          <div class="col-md-8">
		          	<a href="/hellomarkertest/marker.php/Home/Article/"><img class="img-responsive center-block" alt="hellomarker" src="/hellomarkertest/Public/images/bug/article.png" ></a>
		          </div>
		          <div class="col-md-2  visible-lg"></div>
          	</div>
          </div>
       </div>
       <hr class="featurette-divider">
       <div class="text-center">
          <h1><small>当然，我更愿意聆听你的心声</small></h1>
          <br>
          <div class="container text-left">
          	<div class="row">
	          	<div class="col-md-4">
	          		<h2><small>bug反馈</small></h2>
	          		<h2><small>& 意见建议</small></h2>
	          		<h2><small>& 合作</small></h2>
	          	</div>
	            <div class="col-md-8">
	              	<form action="/hellomarkertest/marker.php/Home/Bug/bugWork/" method="post" accept-charset="utf-8">
	              	    <div class="form-group">
	              	    	<label for="bugname"><h4><small>标题：</small></h4></label>
	              	    	<input id="bugname" type="text" name="bugname" value="" id="bugname" class="form-control">
	              	    </div>
	              	    <div class="form-group">
	              	    	<label for="bugtext"><h4><small>内容：</small></h4></label>
	              	    	<textarea id="bugtext" type="text" name="bugtext" value="" id="bugtext" class="form-control" rows="6"></textarea>
	              	    </div>
	              	    <div class="form-group">
	              	    	<label for="bugcontact"><h4><small>联系方式： | email | qq | wechat</small></h4></label>
	              	    	<input id="bugcontact" type="text" name="bugcontact" value="" id="bugcontact" class="form-control" placeholder="选填……">
	              	    </div>
	              	    <div class="form-group text-right">
	              	        <button type="submit" class="btn btn-primary">提交</button>
	              	    </div>
	              	</form>
	            </div>
          	</div>
          </div>
       </div>
        <hr class="featurette-divider">

<div class="container">
	<footer>
       <p class="pull-right "><a href="#" class="scrollToTop">Back to top</a></p>
    </footer>
</div>
<footer class="footer"style="margin-top:20px">

		<div class=" text-center ">
	        <h3><small>powered by <a href="http://www.ptbird.cn" target="_blank">postbird</a></small></h3>
        </div>
</footer>
</body>
</html>