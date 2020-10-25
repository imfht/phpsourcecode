<?php

namespace App\Http\Controllers\Circle;

use App\Biji;
use App\Comment;
use App\Tag;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CircleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::check()) {
            $share_biji = Biji::where('share', 1)->paginate(9);
            $thumbObj = User::select('thumb')->where('id', \Auth::id())->first();   //获得用户头像集合资源句柄
            $tags = Tag::get(); //获得所有标签句柄
            return view('circle.index', compact('share_biji', 'thumbObj', 'tags'));
        }else{
            return redirect('/auth/login');
        }
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
     * @param  Requests\CreateTagsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateTagsRequest $request)
    {
        //将用户输入的标签按空格分割成数组
        $tags = explode(' ',$request->tag);
        //列出所有tags的名称
        $tagsName = Tag::lists('tag')->toArray();
        $biji = Biji::where('id',$request->biji_id)->first();
        //遍历标签数组，根据标签名判断是否已在数据库中存在
        foreach(array_diff($tags, $tagsName) as $tag) {
            Tag::create([
                'tag' => $tag,
            ]);
        }
        //获得两个数组中相同的元素
        $tag_same = array_intersect($tags,$tagsName);
        for($i = 0 ;$i < count($tag_same) ;$i++){
            $tag_list = Tag::select('id')->where('tag',$tag_same[$i])->first();
            $biji->tags()->attach($tag_list->id);
        }
        //更新笔记的分享信息
        \DB::update('update bijis set share = ? where id = ?',[1,$request->biji_id]);

        return redirect('/circle/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(\Auth::check()) {
            $biji = Biji::where('id', $id)->first();
            return view('circle.store', compact('biji'));
        }else{
            return redirect('/auth/login');
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
        if(\Auth::check()) {
            $biji = Biji::where('id', $id)->first();
            $user = User::where('id', Biji::where('id', $id)->first()->user_id)->first();
            $currentUser = User::where('id',\Auth::id())->first();
            $parent_comments = Comment::where('biji_id', $id)->where('parent_id', 0)->get(); //获得对应笔记所有评论的资源句柄
            return view('circle.edit', compact('biji', 'user','currentUser', 'parent_comments'));
        }else{
            return redirect('/auth/login');
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
