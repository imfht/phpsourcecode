<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Article;
use App\User;
use App\Model\Person;
use App\Model\Project;
use Theme;

class DashController extends Controller
{
    public function index()
    {
        $article_num = Article::count();
        $user_num = User::count();
        $person_num = Person::count();
        $project_num = Project::count();
        $users = User::sortByDesc('id')->take(5)->get();
        return Theme::view('dash.index',compact('article_num','user_num','person_num','project_num','users'));
    }
}
