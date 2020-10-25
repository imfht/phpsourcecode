<?php

namespace App\Http\Controllers\Admin;

use App\Biji;
use App\Book;
use App\Chart;
use App\Collect;
use App\Help;
use App\Sign;
use App\Star;
use App\Tip;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BijiManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tips = Tip::where('handle',0)->orderBy('id','desc')->get();
        return view('admin.bijiManage',compact('tips'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cause = $_POST['cause'];
        if($cause == ""){
            return response()->json(array(
                'info' => '请填写举报原因！'
            ));
        }else{
            \DB::table('tips')->insert([
                "reporter_name" =>$_POST['reporter_name'],
                "biji_id" => $_POST['biji_id'],
                "biji_title" => $_POST['biji_title'],
                "reported_id" => $_POST['reported_id'],
                "reported_name" => $_POST['reported_name'],
                "cause" => $_POST['cause'],
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]);
            return response()->json(array(
                'info' => '管理员将在3个工作日内审核举报内容！'
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $details = Biji::where('id',$id)->get();
        foreach($details as $detail){
            return response()->json(array(
                'content' => $detail->content
            ));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if($_GET['type'] == 'remove'){
            \DB::update('update bijis set share = ? where id = ?',[0,$id]);
            \DB::update('update tips set handle = ? where biji_id = ?',[1,$id]);
            return response()->json(array(
                'info' => '该笔记已被移除笔友圈'
            ));
        }else if($_GET['type'] == 'delete'){
            Biji::where('user_id',$id)->delete();
            Book::where('user_id',$id)->delete();
            Help::where('user_id',$id)->delete();
            Collect::where('user_id',$id)->delete();
            Sign::where('user_id',$id)->delete();
            Star::where('user_id',$id)->delete();
            Chart::where('user_id',$id)->delete();
            User::where('id',$id)->delete();
            return response()->json(array(
                'info' => '该用户已被拉黑！'
            ));
        }else if($_GET['type'] == 'ignore'){
            \DB::update('update tips set handle = ? where biji_id = ?',[1,$id]);
            return response()->json(array(
                'info' => '已成功移除举报信息！'
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
