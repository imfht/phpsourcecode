<?php

namespace App\Http\Controllers;

use App\Banner;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $module = 'home';
        $banners = Banner::orderByDesc('weight')->get();

        $banners = $banners->map(function ($banner) {
            $banner->href = action('ArticlesController@show', ['id' => $banner->article_id]);
            $banner->articleTitle = $banner->article->title;

            return $banner;
        });

        return view('home.index', compact('module', 'banners'));
    }

    public function privacyPolicy()
    {
        $module = 'home';

        return view('home.privacy-policy', compact('module'));
    }
}
