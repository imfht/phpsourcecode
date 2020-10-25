<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use Illuminate\Http\Request;
use App\Models\User;

class CategoriesController extends Controller
{
    public function show(Category $category, Request $request, Topic $topic, User $user, Link $link)
    {
        // 读取分类 ID 关联的话题，并按每 50 条分页
        $topics = $topic->withOrder($request->order)
            ->where('category_id', $category->id)
            ->paginate(50);
        // 活跃用户列表
        $active_users = $user->getActiveUsers();
        // 资源链接
        $links = $link->getAllCached();
        // 传参变量到模板中
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}