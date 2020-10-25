<?php

use Illuminate\Database\Seeder;

use App\Model\Category;
class CategoriesSetValue extends Seeder
{
    public function run()
    {
        $category = Category::where('id',1)->first();
        if(!$category) {
            $category = new Category;
            $category->title = '技术漫谈';
            $category->sort = 0;
            $category->info = '';
            $category->cover = '';
            $category->thumb = '';
            $category->parent_id = 0;
            $category->root_id = 0;
            $category->is_nav_show = 1;
            $category->keywords = '';
            $category->description = '';
            $category->templet_all = '';
            $category->templet_nosub = '';
            $category->templet_article = '';
            $category->save();
        }

        $category = Category::where('id',2)->first();
        if(!$category) {
            $category = new Category;
            $category->title = '说天道地';
            $category->sort = 0;
            $category->info = '';
            $category->cover = '';
            $category->thumb = '';
            $category->parent_id = 0;
            $category->root_id = 0;
            $category->is_nav_show = 1;
            $category->keywords = '';
            $category->description = '';
            $category->templet_all = '';
            $category->templet_nosub = '';
            $category->templet_article = '';
            $category->save();
        }
    }
}
