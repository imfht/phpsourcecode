<?php

use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Article::class, 30)
            ->create()
            ->each(function ($article) {
                $article->withContent()->save(factory(\App\ArticleData::class)->make());
                if ($article->id % 5 === 0) {
                    factory(\App\Banner::class)->create([
                        'article_id' => $article->id,
                        'weight' => $article->id,
                    ]);
                }

                \App\Tag::createArticleTagProcess($article->tags, $article->id);
            });
    }
}
