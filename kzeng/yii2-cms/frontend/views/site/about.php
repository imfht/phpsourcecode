<?php

/* 下面是正文*/

use yii\helpers\Html;

$this->title = $title;
?>
<div class="site-about">
    <div class="col-md-12">
        <h2 class="block-center text-center">湖北省群艺馆简介</h2>
    </div>
    <div class="col-md-12 text-justify">
        广东省文化馆（广东省非物质文化遗产保护中心）成立于1956年，是广东省人民政府设立的专门从事群众文化工作和非物质文化传承保护工作的正处级公益一类事业单位。

       其主要职责任务有：组织开展具有导向性、示范性的群众文化艺术活动；辅导农村、社区、企业等开展群众文化活动，辅导、培训辖区内文化馆、站业余干部及文艺活动业务骨干，组织、指导、研究群众性文艺创作活动；组织开展群众文艺理论研究，搜集、整理、保护民族民间文化艺术遗产；负责广东省文化志愿者总队及全省文化志愿者队伍建设、管理、培训工作和文化志愿服务开展；执行全省非物质文化遗产保护的规划、计划和工作规范，组织实施全省非物质文化遗产的普查、认定、申报、保护和交流传播工作。

       馆内设有办公室、活动部、培训部、创作部、信息部、团队部、拓展部、省非物质文化遗产保护中心办公室共八个部室。

       作为我省现代公共文化服务体系建设和公共文化服务的重要参与者、提供者，广东省文化馆始终坚持在省委、省政府和省文化厅的领导下，围绕党和政府的中心工作，以满足群众文化需求为立足点，以改善群众文化生活为目标，充分发挥省馆龙头示范作用，不断完善和创新现代公共文化服务，努力实现好、维护好、保障好广大人民群众的基本公共文化权益；以高度的历史责任感和使命感，着力推进现代公共文化服务体系建设，为我省建设文化强省和幸福广东，实现“三个定位、两个率先”的总目标做出应有的贡献。

		<?php

			use Yii;
			use yii\web\Controller;
			$app = \yii::$app->wx->getApplication();

			$userService = $app->user;
			$users = $userService->lists();

			//var_dump($users->data["openid"]);

//["items":protected]=> array(13) { ["subscribe"]=> int(1) ["openid"]=> string(28) "o2WNQxMYHU0gVb6ZzRgqZ5DdN8gs" ["nickname"]=> string(7) "libtest" ["sex"]=> int(0) ["language"]=> string(5) "zh_CN" ["city"]=> string(0) "" ["province"]=> string(0) "" ["country"]=> string(0) "" ["headimgurl"]=> string(0) "" ["subscribe_time"]=> int(1489579084) ["remark"]=> string(0) "" ["groupid"]=> int(0) ["tagid_list"]=> array(0) { } } }
			for($i = 0; $i < count($users->data["openid"]); $i++ )
			{
				$openid = $users->data["openid"][$i];
				//echo "<br>". $openid;

				$user = $userService->get($openid);
				//echo "<br>".$user->nickname; 
				echo "<br><hr>";
				//var_dump($user);
				$sex = ($user->sex == 1)?'男':'女';

				echo "<ul>";
				echo "<li>" . $user->nickname . "</li>";
				echo "<li>" . $user->openid . "</li>";
				echo "<li>" . $sex . "</li>";
				echo "<li><img width=96 src='" . $user->headimgurl . "'></li>";
				echo "<li>" . $user->country . "</li>";
				echo "<li>" . $user->province . "</li>";
				echo "<li>" . date('Y-m-d H:i:s', $user->subscribe_time) . "</li>";
				echo "</ul>";
			}


			//发模板消息
			//------------------------------------------------------------------------------
			// {{first.DATA}}
			// 学校：{{keyword1.DATA}}
			// 通知人：{{keyword2.DATA}}
			// 时间：{{keyword3.DATA}}
			// 通知内容：{{keyword4.DATA}}
			// {{remark.DATA}}

			// $notice = $app->notice;

			// $data = array(
			// 	         "first"  => "官网头条投递",
			// 	         "keyword1"   => "黄石文新广局",
			// 	         "keyword2"  => "网站小编",
			// 	         "keyword3"  => "2017-7-21",
			// 	         "keyword4"  => "黄石市西塞山区2017年侵权盗版及非法出版物集中销毁活动",
			// 	         "remark" => "立即查看",
			//         );
			// $messageId = $notice->send([
			//         'touser' => 'o2WNQxCKpdsrRP6j5p028hFHdz8E',
			//         'template_id' => 'jLaipfvv0MAy42ddvfVIsI_Zi5_1u96CrMN_II7j9ow',
			//         'url' => 'http://cms.bookgo.com.cn/post/24776',
			//         'data' => $data,
			//     ]);
			// $messageId = $notice->send([
			//         'touser' => 'o2WNQxBLumeUiGPvkgmT2GzYtZic',
			//         'template_id' => 'jLaipfvv0MAy42ddvfVIsI_Zi5_1u96CrMN_II7j9ow',
			//         'url' => 'http://cms.bookgo.com.cn/post/24776',
			//         'data' => $data,
			//     ]);

		?>
    </div>

</div>
