
## ORM  关系数据库

\Cute\ORM\Database的子类，如\Cute\ORM\Schema\Mysql

只能实现简单的查询，主要是

* execute() 增删改一类写查询Insert/Replace/Update/Delete/Truncate/Alter/Drop

* query() 只读查询Select/Show，返回PDOStatement对象

* queryCol() 返回单个数据

* queryPairs() 查询多行的两个字段，组成关联数组

* transact() 数据库事务

Manager的genModelFile()方法，能为每张表生成model文件，支持外键查询

DBHandler将调用Manager的loadModel()方法，为handler生成单例的查询

```php
//以WordPress查询博客为例
//wp_posts表对应\Blog\Model\Post这个Model

//在生成的Post这个Model中添加外键关系
namespace Blog\Model;
use \Cute\ORM\Model;
use \Cute\ORM\Behavior\BelongsTo;
use \Cute\ORM\Behavior\HasMany;
use \Cute\ORM\Behavior\ManyToMany;

class Post extends Model
{
    //其他生成的属性和方法
    //......

    public function getBehaviors()
    {
        return [
            'metas'      => new HasMany(__NAMESPACE__ . '\\PostMeta'),
            'comments'   => new HasMany(__NAMESPACE__ . '\\Comment', 'comment_post_ID'),
            'author'     => new BelongsTo(__NAMESPACE__ . '\\User', 'post_author'),
            'taxonomies' => new ManyToMany(__NAMESPACE__ . '\\TermTaxonomy', 'object_id',
                'term_taxonomy_id', 'term_relationships'),
        ];
    }
}

//Handler中的实现，最后记录SQL到日志中
class BlogHandler extends \Cute\Web\Handler
{
    use \Cute\Contrib\Handler\DBHandler;
    protected $dbkey = 'wordpress';     //数据库的配置名
    protected $modns = 'Blog\\Model';   //Model的Namespace

    public function get($slug = false)
    {
        //$this->posts就是对posts表的查询，每行结果将生成一个Post对象
        //join是根据Post中定义的外键关系进行关联查询，但实现不是在SQL查询中，而是在PHP中
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
```