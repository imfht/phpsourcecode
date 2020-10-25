<?php

namespace App\Models\Traits;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

trait ArticleFilterTrait
{

    /**
     * @param $filter  所有文章、公开、私密
     * @param int $user_id 用户 id
     * @param string $title 文章标题
     * @param int $year 创建 年份
     * @param int $month ....
     * @param int $limit
     * @return mixed
     */
    public function getArticlesWithFilter($filter, $user_id = 0, $title = '', $tag_id = 0, $category_id = 0, $recommend = '', $top = '', $enable = '', $year = '', $month = '', $order = 'created_at', $order_type = 'desc', $limit = 15)
    {
        $filter = $this->getArticleFilter($filter);

        return $this->applyFilter($filter, $user_id, $title, $tag_id, $category_id, $recommend, $top, $enable, $year, $month, $order, $order_type)
            ->with('user', 'category', 'tags')
            ->paginate($limit);
    }

    public function getArticlesWithWhoFilter($filter, $limit = 5, $user_id = 0)
    {

        $filter = $this->getArticleFilter($filter);

        return $this->applyFilter($filter, 0)
            ->where('user_id', '=', $user_id)
            ->paginate($limit);
    }

    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'model', 'model_has_tags', 'model_id');
    }

    public function syncTag($tags = '')
    {
        return $this->tags()->sync($tags);
    }

    protected function applyFilter($filter, $user_id, $title, $tag_id, $category_id, $recommend, $top, $enable, $year, $month, $order, $order_type)
    {
        $query = $this;
        if ($user_id) {
            $query = $query->useridSearch($user_id);
        }

        if ($category_id) {
            $query = $query->categorySearch($category_id);
        }

        if ($recommend) {
            $query = $query->recommendSearch($recommend);
        }

        if ($top) {
            $query = $query->topSearch($top);
        }

        if ($enable) {
            $query = $query->enableSearch($enable);
        }

        if ($tag_id) {
            $article_ids = DB::table('model_has_tags')
                ->where('tag_id', $tag_id)
                ->where('model_type', 'App\Models\Article')
                ->distinct()
                ->pluck('model_id');
            $query = $query->articleIdSearch($article_ids);
        }

        if ($title) {
            $query = $query->titleSearch($title);
        }
        if ($year) {
            $query = $query->yearSearch($year);
        }
        if ($month) {
            $query = $query->monthSearch($month);
        }

        switch ($filter) {
            case 'public' :
                $query = $query->withAccessType('PUB');
                break;
            case 'private':
                $query = $query->withAccessType('PRI');
                break;
            case 'password':
                $query = $query->withAccessType('PWD');
            case 'default':
                break;
        }
        return $query->orderBy($order, $order_type);
    }

    public function scopeUseridSearch($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id);
    }

    public function scopeTitleSearch($query, $title)
    {
        return $query->where('title', 'like', '%' . $title . '%');
    }

    public function scopeYearSearch($query, $year)
    {
        return $query->where('created_year', '=', $year);
    }

    public function scopeMonthSearch($query, $month)
    {
        return $query->where('created_month', '=', $month);
    }

    public function scopeCategorySearch($query, $category_id)
    {
        return $query->where('category_id', '=', $category_id);
    }

    public function scopeRecommendSearch($query, $recommend)
    {
        return $query->where('recommend', '=', $recommend);
    }

    public function scopeTopSearch($query, $top)
    {
        return $query->where('top', '=', $top);
    }

    public function scopeWithAccessType($query, $access_type)
    {
        return $query->where('access_type', '=', $access_type);
    }

    public function scopeArticleIdSearch($query, $articleid)
    {
        return $query->whereIn('id', $articleid);
    }

    protected function getArticleFilter($request_filter)
    {
        $filters = ['public', 'private', 'password'];
        if (in_array($request_filter, $filters)) {
            return $request_filter;
        }
        return 'default';
    }

}
