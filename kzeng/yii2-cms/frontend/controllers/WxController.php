<?php
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;


//引入我们的主项目的入口类。
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\Raw;



class WxController extends Controller
{

    public $enableCsrfValidation = false;    

    public function actionIndex() {

		$app = \yii::$app->wx->getApplication();
		// $menu = $app->menu;
		// $menus = $menu->all();
		// $menus = $menus['menu']['button'];
		// var_dump($menus);

		// $userService = $app->user;
		// $userlist = $userService->lists();
		// var_dump($userlist);


		//从项目实例得到服务端应用实例。
		$server = $app->server;

		//用户实例，可以通过类似$user->nickname这样的方法拿到用户昵称，openid等等
		$user = $app->user;

		//接收用户发送的消息
		// $server->setMessageHandler(function ($message) {
		//     return "您好！欢迎关注我!";
		// });
		$server->setMessageHandler(function ($message) use ($user){ 
		//对用户发送的消息根据不同类型进行区分处理
		    switch ($message->MsgType) {
		            //事件类型消息（点击菜单、关注、扫码），略
		            case 'event':
		            switch ($message->Event) {
		                        case 'subscribe':
		                            // code...
		                            break;

		                        default:
		                            // code...
		                            break;
		                    }
		                break;
		                   //文本信息处理
		                case 'text':
		                   //获取到用户发送的文本内容
		        $content = $message->Content;
		                   //发送到图灵机器人接口
		        $url = "http://www.tuling123.com/openapi/api?key=aaa837675ffe4e65835bd9638751153d&info=".$content;
		                   //获取图灵机器人返回的内容
		        $content = file_get_contents($url);
		                   //对内容json解码
		        $content = json_decode($content);
		                   //把内容发给用户
		        return new Text(['content' => $content->text]); 
		                break;
		        //图片信息处理，略
		                case 'image':
		        $mediaId  = $message->MediaId;
		        return new Image(['media_id' => $mediaId]);
		                break;
		        //声音信息处理，略
		                case 'voice':
		        $mediaId  = $message->MediaId;
		        return new Voice(['media_id' => $mediaId]);
		                    break;
		        //视频信息处理，略
		                case 'video':
		        $mediaId  = $message->MediaId;
		        return new Video(['media_id' => $mediaId]);
		                    break;
		        //坐标信息处理，略
		                case 'location':
		        return new Text(['content' => $message->Label]);
		                    break;

		        //链接信息处理，略
		                case 'link':
		        return new Text(['content' => $message->Description]);
		                break;

		                default:
		                break;
		         }
		});

		//响应输出
		$response = $server->serve();
		$response->send(); // Laravel 里请使用：return $response;


    }  


}
