<?php

namespace App\Http\Controllers\Biji;

use App\Biji;
use App\Book;

use App\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;


class BijiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::check()){
            $thumbObj = User::select('thumb')->where('id', \Auth::id())->first();   //获得用户头像集合资源句柄
            $count = Biji::where('user_id',\Auth::id())->where('wastebasket',0)->count();   //获得用户笔记记录数
            $books = Book::with('bijis')->where('user_id',\Auth::id())->get();
            //根据当前用户查找的笔记本title获得相应的笔记本和笔记资源，显示当前的笔记本列表
            if($count == 0){
//                $hasbiji =false;
//                return view('biji.index', compact('hasbiji'));
            }else{
                if(!empty($_GET['search_title'])){
                    $book_titles = Book::select('title')->where('user_id',\Auth::id())->get();
                    foreach($book_titles as $titleObj){
                        if($titleObj->title === $_GET['search_title']){
                            $search_book = Book::select('id')->where('title' , $_GET['search_title'])->first();
                            $bijis = Biji::where('book_id',$search_book->id)->where('wastebasket',0)->get();
                            $bookPage = true;
                            $title = $_GET['search_title'];
                            $bookBijiCount = $bijis->count();
                            foreach($bijis as $biji){
                                if(preg_match_all('/<img.*?src="(.*?)".*?>/is',$biji->content,$src_all)){
                                    foreach($src_all as $src){
                                        $biji->content = str_replace($src,'',$biji->content);
                                    }
                                }
                                $biji->content=str_replace('&nbsp;','',$biji->content);
                            }
                        }
                    }
                }else{
                    $bookPage = false;
                    //根据用户选择的笔记本在其范围内模糊搜索笔记
                    if(!empty($_GET['search_biji'])){
                        $bijis = Biji::where('book_id',$_GET['book_id'])
                            ->where('user_id',\Auth::id())
                            ->where('wastebasket',0)
                            ->where('content','like','%'.$_GET['search_biji'].'%')
                            ->get();
                        foreach($bijis as $biji){
                            if(preg_match_all('/<img.*?src="(.*?)".*?>/is',$biji->content,$src_all)){
                                foreach($src_all as $src){
                                    $biji->content = str_replace($src,'',$biji->content);
                                }
                            }
                            $biji->content=str_replace('&nbsp;','',$biji->content);
                        }
                    }else{
                        //获得过滤图像的笔记列表内容
                        $bijis = Biji::where('user_id',\Auth::id())->where('wastebasket',0)->get();     //获得用户的所有笔记
                        foreach($bijis as $biji){
                            if(preg_match_all('/<img.*?src="(.*?)".*?>/is',$biji->content,$src_all)){
                                foreach($src_all as $src){
                                    $biji->content = str_replace($src,'',$biji->content);
                                }
                            }
                            $biji->content=str_replace('&nbsp;','',$biji->content);
                        }
                    }
                }
                //根据笔记列表的id(默认获得ID=1)获得笔记本资源句柄
                if(!empty($_GET['biji_id'])){
                    $list = Biji::where('id','=',$_GET['biji_id'])->where('wastebasket',0)->first();
                    $book_id = $list->book_id;
                    $book = Book::where('id','=',$book_id)->first();
                }else{
                    $One = Biji::select(\DB::raw('id'))->where('user_id', \Auth::id())->where('wastebasket',0)->first();
                    $list = Biji::where('id','=',$One->id)->first();
                    $book_id = $list->book_id;
                    $book = Book::where('id','=',$book_id)->first();
                }
                return \View::make('biji.index', compact('begin','thumbObj','bijis','src','list','book','books','booksObj','hasBiji','title','search_book','bookPage','bookBijiCount'));
            }
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
        $books = Book::with('bijis')->where('user_id',\Auth::id())->get();
        return \View::make('biji.actions.create', compact('books'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\BijiCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\BijiCreateRequest $request)
    {
        Biji::create($request->all());
        return redirect('/biji')
            ->withSuccess('成功创建一条笔记.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(\Auth::check()) {
            $biji = Biji::where('id', $id)->first();
            return view('biji.actions.show', compact('biji'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $biji = Biji::findOrFail($id)-> update($request -> all());
        return redirect('/biji/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //删除笔记与标签的关联
        $biji_tag = \DB::table('biji_tag')->where('biji_id',$id);
        $biji_tag->delete();
        //存入废纸篓
        Biji::where('id',$id)->update(['wastebasket'=>1]);
        return redirect('/biji/')
            ->withSuccess('成功删除一条笔记.');
    }

}
