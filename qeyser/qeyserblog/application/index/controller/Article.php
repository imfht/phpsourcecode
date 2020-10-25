<?php
namespace app\index\controller;
use think\Validate;
use app\index\controller\Base;

/**.-------------------------------------------------------------------
 * |    Software: [QeyserBlog]
 * |    Site: www.qeyser.net
 * |-------------------------------------------------------------------
 * |    Author: 凯撒 <125790757@qq.com>
 * |    WeChat: 15999230034
 * |    Copyright (c) 2017-2018, www.qeyser.net . All Rights Reserved.
 * '-------------------------------------------------------------------*/

class Article extends Base{
    /**
     * 栏目文章列表
     */
	public function category(){
    	// 显示栏目文章列表
    	$article = db('article')->alias('a')->join('category','a.cid=category.cid')->where('a.cid','=',input('cid'))->field('aid,a.cid,title,content,pic,time,cname')->paginate(Config('cate_page'));
    	$this->assign('article',$article);
    	// 显示tag信息
    	$this->tags();
    	// 显示友情链接
    	$this->freandlink();
		return $this->fetch();
	}

    /**
     * 文章内容页
     */
	public function view(){
        $article = db('article')->where('aid','=',input('aid'))->find();
        if(!$article){
            $this->error('سىز كۆرمەكچى بولغان يازما مەۋجۇد ئەمەس!');;
        } else {
            // 显示文章内容
            db('article')->where('aid','=',input('aid'))->setInc('click');
            $this->assign('article',$article);
            // 显示留言
            $map['aid']= input('aid');
            $map['status'] = 1;
            $comment = db('comment')->where($map)->select();
            $this->assign('comment',$comment);
            // 显示tag信息
            $this->tags();
            return $this->fetch();
        }
	}

    /**
     * 文章留言
     */
    public function comment(){
        if(request()->isPost()){
            $data = [
                'aid' => input('aid'),
                'username' => input('username'),
                'email' => input('email'),
                'website' => input('website'),
                'content' =>input('content'),
                'add_time' => time(),
                'ip' => get_client_ip(),
                '__token__' => input('__token__')
            ];
            // p($data);die;
            // 验证输入内容
            $rule = [
                ['username','require|token','ئەزالىق نامىنى كىرگۈزۈڭ !|قانۇنسىز ئۇچۇر يوللىماڭ!'],
                ['email','require|email','خەت ساندۇقىنى كىرگۈزۈڭ!|خەت ساندۇقى خاتا بۇلۇپ قالدى!'],
                ['website','require','تور نامىنى كىرگۈزۈڭ!'],
                ['content','require|min:20','مەزمۇننى كىرگۈزۈڭ!|مەزمۇن ئۇزۇنلۇقى 20 ھەرىپتىن چوڭ بولسۇن!']
            ];
            $validate = new Validate($rule);
            $result   = $validate->check($data);
            // 判断是否通过
            if($result == true){
                db('comment')->insert($data);
                $this->success('سۆز قالدۇرۇش مۇۋاپىقيەتلىك بولدى！');
            }else {
                // 验证失败 输出错误信息
                $this->error($validate->getError());
            }
            return;     
        }
    }    
}