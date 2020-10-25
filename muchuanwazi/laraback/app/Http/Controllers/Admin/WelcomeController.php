<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function index()
	{
		$data['tasks'] = [
                [
                        'name' => 'Design New Dashboard',
                        'progress' => '87',
                        'color' => 'danger'
                ],
                [
                        'name' => 'Create Home Page',
                        'progress' => '76',
                        'color' => 'warning'
                ],
                [
                        'name' => 'Some Other Task',
                        'progress' => '32',
                        'color' => 'success'
                ],
                [
                        'name' => 'Start Building Website',
                        'progress' => '56',
                        'color' => 'info'
                ],
                [
                        'name' => 'Develop an Awesome Algorithm',
                        'progress' => '10',
                        'color' => 'success'
                ]
        ];
        return view('admin.welcome')->with($data);
	}


    public function butNoPermission()
    {
        return view('admin.no_permission')->with('page_title','系统信息');
	}
}
