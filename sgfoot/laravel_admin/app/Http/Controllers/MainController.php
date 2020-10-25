<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function config()
    {
        $flag = $this->request->input('flag');
        $rs   = config('nav.' . $flag);
        if (!$rs) {
            return [];
        }
        return $this->setContentJson($rs);
    }
    public function home()
    {
        return view('mainFrame');
    }
}
