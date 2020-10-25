<?php

namespace App\Controllers;

use Nimble\Validator\Validator;

class IndexController extends Controller
{
    public function main()
    {
        $validReg = [
            [ 'name', 'admin', 'require|length[1,2]'],
        ];
        $validMsg = [
            'length[1,2]' => ':attribute 长度不合法',    
        ];
        
        $validContainer = Validator::make($validReg, $validMsg);

        //return $this->view('hello');
    }
}

