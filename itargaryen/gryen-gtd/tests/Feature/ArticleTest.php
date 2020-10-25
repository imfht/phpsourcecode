<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private $articles;
    private $firstArticle;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->articles = factory(\App\Article::class, 5)
            ->create()
            ->each(function ($article) {
                $article->withContent()->save(factory(\App\ArticleData::class)->make());
                \App\Tag::createArticleTagProcess($article->tags, $article->id);
            });

        $this->firstArticle = \DB::table('articles')
            ->join('article_datas', 'articles.id', '=', 'article_datas.article_id')
            ->select('articles.id', 'articles.tags', 'articles.title', 'articles.description', 'article_datas.content')
            ->first();
        $this->firstArticle->tagArray = explode(',', $this->firstArticle->tags);
        $this->user = factory(\App\User::class)->create();
    }

    public function testArticlesPage()
    {
        $this->get('/articles')
            ->assertSuccessful()
            ->assertSeeText($this->firstArticle->title)
            ->assertSeeText($this->firstArticle->description);
    }

    public function testTagPage()
    {
        $this->get('/articles/tag/'.$this->firstArticle->tagArray[0])
            ->assertSuccessful()
            ->assertSeeText($this->firstArticle->title);
    }

    public function testArticleShowPage()
    {
        $this->get('/articles/show/'.$this->firstArticle->id.'.html')
            ->assertSuccessful()
            ->assertSeeText($this->firstArticle->title)
            ->assertSeeText($this->firstArticle->content);
    }

    public function testCreateArticlePage()
    {
        $this->actingAs($this->user)
            ->get('/articles/create')
            ->assertSuccessful();
    }

    public function testEditArticlePage()
    {
        $this->actingAs($this->user)
            ->get('/articles/edit/'.$this->firstArticle->id)
            ->assertSuccessful();
    }

    public function testStoreArticle()
    {
        $faker = \Faker\Factory::create();
        $postData = [
            'title' => $faker->text(),
            'content' => $faker->text(),
            'cover' => env('SITE_DEFAULT_IMAGE'),
            'description' => $faker->text(),
            'tags' => implode(',', $faker->words()),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/articles/store', $postData);

        $response->assertSuccessful();

        $tagArray = array_filter(explode(',', $postData['tags']));

        $this->get($response->original['href'])
            ->assertSeeText($postData['title'])
            ->assertSeeText($tagArray[0])
            ->assertSuccessful();
    }

    public function testUpdateArticle()
    {
        $faker = \Faker\Factory::create();
        $postData = [
            'title' => $faker->text(),
            'content' => $faker->text(),
            'cover' => env('SITE_DEFAULT_IMAGE'),
            'description' => $faker->text(),
            'tags' => implode(',', $faker->words()),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/articles/update/'.$this->firstArticle->id, $postData);

        $response->assertSuccessful();

        $tagArray = array_filter(explode(',', $postData['tags']));

        $this->get(action('ArticlesController@show', ['id' => $this->firstArticle->id]))
            ->assertSeeText($postData['title'])
            ->assertSeeText($postData['content'])
            ->assertSeeText($tagArray[0])
            ->assertSuccessful();

        $this->get(action('Api\ArticlesController@getArticleContent', ['articleId' => $this->firstArticle->id]))
            ->assertSeeText($postData['content'])
            ->assertSuccessful();
    }

    public function testUploadCover()
    {
        Storage::fake(env('DISK'));

        $response = $this->actingAs($this->user)
            ->post('/articles/cover/upload', [
                'cover' => UploadedFile::fake()->image('cover.jpg')->size(200),
            ]);

        $response->assertSuccessful()
            ->assertJson([
                'success' => true,
            ]);

        $filePathArr = explode(env('STATIC_URL'), $response->json('file_path'));

        Storage::disk(env('DISK'))->assertExists($filePathArr[1]);
    }
}
