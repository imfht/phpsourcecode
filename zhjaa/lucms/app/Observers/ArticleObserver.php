<?php

namespace App\Observers;

use App\Models\Article;
use App\Jobs\TranslateSlug;


class ArticleObserver
{
    public function saved(Article $article)
    {
        if ( ! $article->slug) {

            dispatch(new TranslateSlug($article));
        }
    }
}
