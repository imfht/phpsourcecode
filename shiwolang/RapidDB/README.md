# RapidDB 轻量级数据库操作组件

* 支持事务嵌套
* PDO支持
* JSON支持
* 类的实例集合支持
* 轻量级
* 可以轻易和其他框架整合
* 多数据库多连接支持


##使用方式

###通过composer安装
```
$ composer require shiwolang/db
```
###初始化连接
**暂仅支持mysql和sqlite**
#####单个连接情景
```php
DB::init([
    'database_type' => 'mysql',
    'database_name' => 'dbname',
    'server'        => 'localhost',
    'username'      => 'username',
    'password'      => 'yourpass',
    'charset'       => 'utf8'
]);
```
#####多个连接情景
```php
DB::init([
    'database_type' => 'mysql',
    'database_name' => 'dbname',
    'server'        => 'localhost',
    'username'      => 'username',
    'password'      => 'yourpass',
    'charset'       => 'utf8'
], "connection1");
```
#####PDO设置初始化 单个连接情景
```php
DB::init($PDO);
```
#####PDO设置初始化 多个连接情景
```php
DB::init($PDO, "connection1");
```
###获取数据库连接
---------------------------------------
#####单个连接情景
```php
DB::connection();
```
#####多个连接情景
```php
DB::connection("connection1");
```
###添加数据(单表)
---------------------------------------
```php
$lastInsertId = DB::connection()->insert("content", [
    "title"       => "title1",
    "content"     => "content1",
    "time"        =>  time()
]);
```
###删除数据(单表)
---------------------------------------
```php
$lastInsertId = DB::connection()->delete("content", "id = :id", [":id" => 1]);
```
###修改数据(单表)
---------------------------------------
```php
$lastInsertId = DB::connection()->update("content", [
    "title"       => "title1",
    "content"     => "content1",
    "time"        =>  time()
],"id = :id", [":id" => 1]);
```
###查询数据
---------------------------------------
**请注意在使用limit的时候的数值务必为整数int型！！**
```php
DB::connection()->query("SELECT * FROM content where title = 'title1' LIMIT 10")->all();

DB::connection()->query("SELECT * FROM content WHERE title = :title LIMIT :limit", [
    ":title"     => "title1",
    ":limit"     =>  10
])->all();

DB::connection()->query("SELECT * FROM content WHERE id = ? LIMIT ?", ["title1", 10])->all();
```
####设置获取模式
#####设置为数组的获取方式（默认方式）
```php
DB::connection()->query("SELECT * FROM content where title = 'title1' LIMIT 10")->all();
```
#####设置为类的实例集合的获取方式
```php
DB::connection()->query("SELECT * FROM content where title = 'title1' LIMIT 10")->bindToClass(Content::class)->all();
DB::connection()->query("SELECT * FROM content where title = 'title1' LIMIT 10")->all(Content::class);
```
#####将每行的列作为参数传递给指定的函数，并返回调用函数后的结果的获取方式
```php
DB::connection()->query("SELECT * FROM content where title = 'title1' LIMIT 10")->all(function($title, $content, $time){
    return [
        "title"     => $title,
        "content"   => $content,
        "time"      => date("Y-m-d H:i:s", $time)
    ];
});
```
#####按相关的结果集中获取下一行
数组获取方式
```php
DB::connection()->query("SELECT * FROM content LIMIT 10")->each(function ($row) {
    print_r($row);
});
```
类的实例获取方式
```php
DB::connection()->query("SELECT * FROM content LIMIT 10")->each(function ($row) {
    print_r($row);
}, Content::class);
```
#####JSON格式数据
数组获取方式的json
```php
DB::connection()->query("SELECT * FROM content LIMIT 10")->json();
```
类的实例集合获取方式的json  

**！！请注意！！  
DB::json(&$fetchResult = null, $className = null, $args = []) 第一个形参为返回的结果集，并不是绑定的类名！！**
```php
DB::connection()->query("SELECT * FROM content LIMIT 10")->json($data, Content::className());
```
**！！注！！**json数据获取中当获取方式为对象集合的方式时，支持数据自动格式化，可以使用@json注解来注解类中的一个公共方法，对应的json键名为这个方法的首字符小写的[去掉get字符后(如果含有)]方法名称；
同样可以实现ObjectContainerInterface和\JsonSerializable 接口并使用Statement::setJsonObjectContainerClassName($jsonObjectContainerClassName)进行对象集合容器的自定义设置

###事务的支持
-----------------------
**此功能依赖数据事务功能**
#####事务使用声明方式
```php
$db = DB::connection();
$db->beginTransaction();
try {
    $lastInsertId = $db->insert("content", [
        "title"       => "title1",
        "content"     => "content1",
        "time"        =>  time()
    ]);
    $db->commit();
} catch (\Exception $e) {
    $db->rollBack();
    throw $e;
}
```
#####事务使用声明方式支持无限级嵌套
```php
$db = DB::connection();
$db->beginTransaction();
try {

    try {
        $lastInsertId = $db->insert("content", [
            "title"       => "title1",
            "content"     => "content1",
            "time"        =>  time()
        ]);
        
        $db->commit();
    } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
    $lastInsertId = $db->insert("content", [
        "title"       => "title2",
        "content"     => "content2",
        "time"        =>  time()
    ]);
    
    $db->beginTransaction();
    try {
        $lastInsertId = $db->insert("content", [
            "title"       => "title3",
            "content"     => "content3",
            "time"        =>  time()
        ]);
        $db->commit();
    } catch (\Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
    $db->commit();
} catch (\Exception $e) {
    $db->rollBack();
    throw $e;
}
```
#####事务使用回调函数方式，支持无限级嵌套
```php
DB::connection()->transaction(function () {
        DB::connection()->transaction(function () {
                DB::connection()->insert("content", [
                    "title"       => "title1",
                    "content"     => "content1",
                    "time"        =>  time()
                ]);
            }
        });
        
        DB::connection()->insert("content", [
            "title"       => "title2",
            "content"     => "content2",
            "time"        =>  time()
        ]);
        
        DB::connection()->transaction(function () {
                DB::connection()->insert("content", [
                    "title"       => "title3",
                    "content"     => "content3",
                    "time"        =>  time()
                ]);
            }
        });
    }
});
```

###执行记录查询
-----------------------
#####获取所有执行记录
```php
DB::connection()->query("SELECT * FROM content LIMIT 1")->all();
DB::connection()->query("SELECT * FROM content WHERE title = :title LIMIT :limit", [
    ":title" => "title1",
    ":limit" => 10
])->all();;
print_r(DB::connection()->getLog());
```
#####单条未执行的sql
```php
$sql = DB::connection()->query("SELECT * FROM content WHERE title = :title LIMIT :limit", [
    ":title" => "title1",
    ":limit" => 10
], true);
```
##附录
#####Content类
```php
class Content
{
    private $id;
    private $title;
    private $content;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * For json formater
     * @json
     * @return mixed
     */
    public function getContent()
    {
        return $this->content . "_nihao";
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function __get($name)
    {
        $getter = 'get' . self::camelName($name);

        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $name);
    }
    public function __set($name, $value)
    {
        $setter = 'set' . self::camelName($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }

        throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $name);
    }
    protected static function camelName($name, $ucfirst = true)
    {
        if (strpos($name, "_") !== false) {
            $name = str_replace("_", " ", strtolower($name));
            $name = ucwords($name);
            $name = str_replace(" ", "", $name);
        }

        return $ucfirst ? ucfirst($name) : $name;
    }
}
```
#####数据库创建语句
```sql
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
```
