<?php

use yii\widgets\LinkPager;
use common\widgets\Carousel;

/* @var $this yii\web\View */

$this->title = '主页';

//var_dump($posts_mffds);
?>

<style>
    .carousel{
        margin-left: 15px;
        margin-right: 15px;
    }
</style>

<div class="site-index">

    <!--
    <//?php if (Yii::$app->getRequest()->getQueryParam('page') <= 1) : ?>
        <div class="jumbotronXXX">
        <h1>Congratulations!</h1>

          <p class="lead">You have successfully created your Yii-powered application.</p>

          <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>

        </div>
    <//?php endif; ?>
    -->

    <div class="body-content">

		 <div class="container-fluid">

             <div class="row">
                 <?php if ($slider == 1):  ?>
                     <!-- 幻灯片 -->
                    <?= Carousel::widget(['data' => $carousel]) ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <img src="<?= Yii::$app->params['tag_img'] ?>" width="100%">
                    </div>
                <?php endif; ?>
             </div>

			<div class="row">
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">免费辅导</div>
					  <div class="panel-body">
                          <?php foreach ($posts_mffds as $posts_mffd) {
                                if(mb_strlen($posts_mffd->title) >16)
                                    $title = mb_substr($posts_mffd->title,0,16,'utf-8'). '...';
                                else
                                    $title = $posts_mffd->title;
                          ?>
					    	<p><?= $title ?></p>
                          <?php } ?>
					  </div>
					</div>
			  </div>
			  <div class="col-md-6">
					<div class="panel panel-default">
					  <div class="panel-heading">你点我送</div>
					  <div class="panel-body">
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					  </div>
					</div>
			  </div>

			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">视频中心
						&nbsp;&nbsp;
		                <small>
		                  <a href="#"></a>更多 >></a>
		                </small>
					  </div>
					  <div class="panel-body">
					    	<p>视频中心列表</p>
					    	<p>视频中心列表</p>
					    	<p>视频中心列表</p>
					    	<p>视频中心列表</p>
					    	<p>视频中心列表</p>
					  </div>
					</div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">文件公告</div>
					  <div class="panel-body">
					    	<p>文件公告列表</p>
					    	<p>文件公告列表</p>
					    	<p>文件公告列表</p>
					    	<p>文件公告列表</p>
					    	<p>文件公告列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">活动预告</div>
					  <div class="panel-body">
					    	<p>活动预告列表</p>
					    	<p>活动预告列表</p>
					    	<p>活动预告列表</p>
					    	<p>活动预告列表</p>
					    	<p>活动预告列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">新闻资讯</div>
					  <div class="panel-body">
					    	<p>新闻资讯列表</p>
					    	<p>新闻资讯列表</p>
					    	<p>新闻资讯列表</p>
					    	<p>新闻资讯列表</p>
					    	<p>新闻资讯列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">网上展厅</div>
					  <div class="panel-body">
					    	<p>网上展厅列表</p>
					    	<p>网上展厅列表</p>
					    	<p>网上展厅列表</p>
					    	<p>网上展厅列表</p>
					    	<p>网上展厅列表</p>
					  </div>
					</div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-md-12">
				<img src="http://cms.mitoto.cn/uploads/2017/06/qq20170601131604.png" width="100%">
			  </div>
			</div>

			<div class="row">
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">讲座信息</div>
					  <div class="panel-body">
					    	<p>讲座信息列表</p>
					    	<p>讲座信息列表</p>
					    	<p>讲座信息列表</p>
					    	<p>讲座信息列表</p>
					    	<p>讲座信息列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">演出信息</div>
					  <div class="panel-body">
					    	<p>演出信息列表</p>
					    	<p>演出信息列表</p>
					    	<p>演出信息列表</p>
					    	<p>演出信息列表</p>
					    	<p>演出信息列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">展览信息</div>
					  <div class="panel-body">
					    	<p>展览信息列表</p>
					    	<p>展览信息列表</p>
					    	<p>展览信息列表</p>
					    	<p>展览信息列表</p>
					    	<p>展览信息列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">场馆搜索</div>
					  <div class="panel-body">
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					  </div>
					</div>
			  </div>
			</div>

			<div class="row">
			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">今日电影</div>
					  <div class="panel-body">
					    	<p>电影信息列表</p>
					    	<p>电影信息列表</p>
					    	<p>电影信息列表</p>
					    	<p>电影信息列表</p>
					    	<p>电影信息列表</p>
					  </div>
					</div>
			  </div>
			  <div class="col-md-6">
					<div class="panel panel-default">
					  <div class="panel-heading">电影信息</div>
					  <div class="panel-body">
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					  </div>
					</div>
			  </div>

			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">平台登录
					  </div>
					  <div class="panel-body">
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					  </div>
					</div>
			  </div>
			</div>



			<div class="row">

			  <div class="col-md-9">
			  	<div class="row">
				  <div class="col-md-6">
						<div class="panel panel-default">
						  <div class="panel-heading">我行我秀</div>
						  <div class="panel-body">
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						  </div>
						</div>
				  </div>

				  <div class="col-md-6">
						<div class="panel panel-default">
						  <div class="panel-heading">文化地图
						  </div>
						  <div class="panel-body">
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						    	<p>&nbsp;</p>
						  </div>
						</div>
				  </div>
				  </div>
			  </div>

			  <div class="col-md-3">
					<div class="panel panel-default">
					  <div class="panel-heading">广场活动
					  </div>
					  <div class="panel-body">
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					    	<p>&nbsp;</p>
					  </div>
					</div>
			  </div>
			</div>

		</div>

    </div>
</div>
