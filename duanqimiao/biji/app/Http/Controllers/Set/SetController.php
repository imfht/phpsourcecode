<?php

namespace App\Http\Controllers\Set;

use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::check()) {
            $user_id = \Auth::id();
            return view('setting.personal', compact('user_id'));
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
        //
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
            $user = User::findOrFail($id);
            return view('setting.person', compact('user'));
        }else{
            return redirect('/auth/login');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Requests\PersonSetRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\PersonSetRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user -> update($request -> all());
        return redirect('/biji');
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
