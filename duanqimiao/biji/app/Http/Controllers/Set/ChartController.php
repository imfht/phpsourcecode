<?php

namespace App\Http\Controllers\Set;

use App\Http\Controllers\Controller;

class ChartController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chart(){
        if(\Auth::check()) {
            return view('setting.chart');
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(){
        $January = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="January"');
        $February = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="February"');
        $March = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="March"');
        $April = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="April"');
        $May = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="May"');
        $June = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="June"');
        $July = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="July"');
        $August = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="August"');
        $September = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="September"');
        $October  = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="October "');
        $November = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="November"');
        $December = \DB::select('select COUNT(id) from charts WHERE user_id='.\Auth::id().' AND DATE_FORMAT(login_at, \'%Y\')=year(now()) AND DATE_FORMAT(login_at, \'%M\')="December"');
        $date = [$January,$February,$March,$April,$May,$June,$July,$August,$September,$October,$November,$December];
        return response()->json(array(
            //返回当前用户的所有登录时间记录
            'time' =>$date
        ));
    }
}
