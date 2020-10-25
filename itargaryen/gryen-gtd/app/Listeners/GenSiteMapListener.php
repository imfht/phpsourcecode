<?php

namespace App\Listeners;

use App\Article;
use App\Events\PublishArticle;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenSiteMapListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PublishArticle  $event
     * @return void
     */
    public function handle(PublishArticle $event)
    {
        $publicPath = public_path('sitemap.xml');

        $siteMap = Sitemap::create();

        $siteMap
            ->add(Url::create(action('HomeController@index'))
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create(action('ArticlesController@index'))
                ->setLastModificationDate(Carbon::yesterday())
                ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_MONTHLY));

        Article::all()->each(function ($article) use ($siteMap) {
            if ($article->status === 1) {
                $siteMap->add(Url::create(action('ArticlesController@show', [$article->id]))
                    ->setLastModificationDate($article->updated_at)
                    ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_MONTHLY));
            }
        });

        $siteMap->writeToFile($publicPath);

        $siteMap = null;
    }
}
