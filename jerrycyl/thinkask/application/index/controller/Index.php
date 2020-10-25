<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\index\controller;

use app\index\model\Index as Indexss;
use think\Controller;
use think\Request;
use think\Session;
use think\Cookie;
use think\Route;
use think\Hook ;
// use app\common\Hook;
class index extends Controller
{

	public function _initialize()
    {
       
       
        
    }

    public function index()
    {

        $setting = cache('system_setting');
        $seo['title'] = "首页-".unserialize($setting[1]['value']);
        $seo['description'] = unserialize($setting[2]['value']);
        $seo['keywords'] = unserialize($setting[3]['value']);
        $this->assign('seo',$seo);
        if($_SERVER['HTTP_HOST']=="www.wl.com"||$_SERVER['HTTP_HOST']=="wl.com"){
            $this->redirect('/wl/index');
        }
        if($_SERVER['HTTP_HOST']=="thinkask.cn"||$_SERVER['HTTP_HOST']=="www.thinkask.cn"||$_SERVER['HTTP_HOST']=="thinkask.com"||$_SERVER['HTTP_HOST']=="www.thinkask.com"||$_SERVER['HTTP_HOST']=="www.cooldreamer..com"){
            $this->redirect('/jofficial/index');
            // return $this->fetch();
        }else{
           $this->redirect('/question/index');
        }
		
    }
    public function app(){
        return $this->fetch();
    }
 /**
   * [test description]
   * @Author   Jerry
   * @DateTime 2017-04-11T10:45:28+0800
   * @Example  eg:
   * @return   [type]                   [description]
   */
  public function test(){
    echo turl('blog/index/index');
    echo "<br/>";
    echo url('blog/index/index','',true,"www.baidu.com");
    die;
    // Route::setDomain('114.80.203.45');
//    Route::domain([
//     '114.80.203.45' => function(){
//         // 动态注册域名的路由规则
//         // Route::get(':id', 'blog/Index/read');
//         // Route::get('user/:name', 'user/User/info');

//     },

    
// ],['ext'=>'html'],['id'=>'\d+','name'=>'\w+']);
    Route::get('user/:name', 'blog/Index/read',['domain'=>'blog']);
    echo url('blog/Index/read','id=10');
    die;
    // build('input')->test('sfa')->test('==>')->test('sfa');
    build()->addinput(
      [
        ##父级标题.
        'title'=>[
            'name'=>'test',
            'style'=>'color:red',
            'class'=>'test'
              ],
        ##表单的元素
        'element'=>[
            ]
      ]
      )->addheader()->addinput(
        [
          'title'=>[
            'name'=>'123456'
          ],
          'element'=>[
            'type'=>'password',

          ],
        ]



      )->addcheckbox(
       


        )
      ->fetch();
    // build('input')->setheader()->
    // settitle('test',['text'=>'test','style'=>'color:red;','class'=>'test'])->setelement(['style'=>'color:red;','class'=>'sres'])
    // ->make('checkbox')->settitle(['text'=>'test'])->setvalue(['system'=>['1'=>'text1','2'=>'text2']])
    // ->fetch();

    // builder::make('form')->s();
    // echo "string";
  }




}
