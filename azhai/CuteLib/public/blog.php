<?php
use \Cute\Web\Handler;


class BlogHandler extends Handler
{
    use \Cute\Contrib\Handler\DBHandler;
    protected $dbkey = 'wordpress';
    protected $modns = 'Blog\\Model';

    public function get($slug = false)
    {
        $query = $this->posts->join('*', 'taxonomies.*');
        if ($slug === false) {
            $posts = $query->orderBy('post_date DESC')->setPage(5)->all();
            foreach ($posts as $post) {
                if (starts_with($post->post_content, '欢迎使用WordPress')) {
                    break; //找到以这段话开头的Post
                }
            }
        } else {
            $post = $query->findBy('post_name', $slug)->get(); //根据slug找Post
        }
        $this->logSQL();
        var_dump($post);
    }
}


class BlogUserHandler extends Handler
{
    use \Cute\Contrib\Handler\DBHandler;
    protected $dbkey = 'wordpress';
    protected $modns = 'Blog\\Model';

    public function get($username)
    {
        $query = $this->users->join('user_group');
        $user = $query->findBy('user_login', $username)->getOrCreate();
        $this->logSQL();
        var_dump($user);
    }
}

app()->route('/', 'BlogHandler');
app()->route('/<string>/', 'BlogHandler');
app()->route('/user/<string>/', 'BlogUserHandler');
