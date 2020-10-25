<?php

namespace App\Http\Controllers\Admin;

use App\Biji;
use App\Book;
use App\Chart;
use App\Collect;
use App\Help;
use App\Sign;
use App\Star;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UserManageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::where('auth',0)->get();
        return view('admin.userManage',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userName = $_GET['name'];
        $email = $_GET['email'];
        $password = $_GET['password'];
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if($userName=="" || $email=="" ||$password==""){
            return response()->json(array(
                'info' => '必填项不能为空！'
            ));
        }
        else if( strlen($password) < 6){
            return response()->json(array(
                'info' => '密码不能小于6位！'
            ));
        }
        else if ( !preg_match( $pattern, $email ) ){
            return response()->json(array(
                'info' => '电子邮箱格式错误！'
            ));
        }
        $users = User::where('auth',0)->get();
        foreach($users as $user){
            if($user->email == $email){
                return response()->json(array(
                    'info' => '该邮箱已被注册！'
                ));
            }
        }
        \DB::table('users')->insert([
            "name" =>$userName,
            "email" => $email,
            "password" => \Hash::make($password),
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now()
        ]);
        return response()->json(array(
            'info' => '添加用户成功！',
        ));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userName = $_GET['userName'];
        $email = $_GET['email'];
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        $users = User::where('auth',0)->get();
        foreach($users as $user){
            if($user->email == $email){
                return response()->json(array(
                    'info' => '该邮箱已被注册！'
                ));
            }
        }
        if($userName=="" || $email==""){
            return response()->json(array(
                'info' => '必填项不能为空！'
            ));
        }
        else if ( !preg_match( $pattern, $email ) ){
            return response()->json(array(
                'info' => '电子邮箱格式错误！'
            ));
        }
        \DB::update('update users set name = ?,email = ? where id = ?',[$userName,$email,$id]);
        return response()->json(array(
            'info' => '修改信息成功！'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json(array(
            'user' => User::where('id',$id)->get(),
        ));
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
        Biji::where('user_id',$id)->delete();
        Book::where('user_id',$id)->delete();
        Help::where('user_id',$id)->delete();
        Collect::where('user_id',$id)->delete();
        Sign::where('user_id',$id)->delete();
        Star::where('user_id',$id)->delete();
        Chart::where('user_id',$id)->delete();
        User::where('id',$id)->delete();
        return response()->json(array(
            'info' => '删除成功！'
        ));
    }
}
