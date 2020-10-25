<?php

namespace App\Http\Controllers\Book;

use App\Book;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //获得当前用户查找笔记本列表
        if(!empty($_GET['search_book'])){
            //显示当前用户要查找的笔记本
            return response()->json(array(
                'booksObj' => Book::select('title')->where('title','like','%'.$_GET['search_book'].'%')->where('user_id',\Auth::id())->get()
            ));
        }else{
            //默认显示当前用户所有笔记本
            return response()->json(array(
                'booksObj' =>Book::select('title')->where('user_id','=',\Auth::id())->get()
                ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::check()) {
            return view('book.create');
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Requests\AddBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddBookRequest $request)
    {
        $books = Book::where('user_id',\Auth::id())->get();
        foreach($books as $book){
            if($request->title == $book->title){
                return redirect('/book/create') ->withErrors('所输入的笔记本名称已存在。请换一个笔记本名称。');
            }
        }
        Book::create($request->all());
        return redirect('/biji');
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
            $book = Book::where('id', $id)->first();
            return view('book.update', compact('book'));
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
        //
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
        $book = Book::findOrFail($id)-> update($request -> all());
        return redirect('/biji/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $books = Book::where('id',$id)->delete();
        return redirect('/biji/')
            ->withSuccess('成功删除一个笔记本.');
    }
}
