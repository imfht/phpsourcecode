<?php

namespace App\Http\Controllers\Biji;

use App\Biji;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mail($id){
        if(\Auth::check()) {
            $biji = Biji::where('id', $id)->first();
            return view('biji.share.emails', compact('biji'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @return $this
     */
    public function send(){
        if(!(empty($_GET['sendTo']))){
           $flag = Mail::send('biji.share.send',['name'=>\Auth::user()->name,'content'=>$_GET['content'],'mess'=>$_GET['message']],function($message){
                $to = $_GET['sendTo'];
                $message ->to($to)->subject($to.'向你发送了一篇笔记.');

            });
            if($flag){
                return redirect('/biji/')
                    ->withSuccess('邮件已成功发送.');
            }else{
                return redirect('/biji/')
                    ->withErrors('邮件发送失败.');
            }
        }else{
            return \Redirect::back()->withErrors('请填写收件人邮箱!');
        }
    }
}
