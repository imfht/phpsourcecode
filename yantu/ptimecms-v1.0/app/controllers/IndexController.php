<?php
use Phalcon\Http\Response;

class IndexController extends ControllerBase
{
    public function initialize()
    {

    }

    public function indexAction()
    {
        $result = Setting::findFirst("keyword='index_topic_id'");
        $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => 'show',
                'params' => ['topic',$result->value]
        ));
        return false;
    }

    public function listAction($category_id)
    {
        //分类信息
        $model = new Category();
        $categoryResult = $model->findFirst("id=$category_id AND is_visible = 1 AND is_delete = 0");
        if(!$categoryResult)
            $this->route404();
        $table = $categoryResult->module;
        if(!in_array($table, ["category","article","album","link","topic"]))
            $this->route404();
        $this->view->setVar("category",$categoryResult);

        //全局设置
        $this->getGlobal();
        
        
        //兄弟分类信息，面包屑信息
        if($categoryResult->father_id != 0){
            $father_id = $categoryResult->father_id;
            $brotherResult = $model->find([
                "father_id=$father_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"weight DESC"
                ]);
            $this->view->setVar("brother",$brotherResult);
            $father_path = explode(",", $categoryResult->father_path);
            $parentResult = $model->find(" ( id = ".implode(" OR id=", $father_path)." ) AND is_visible = 1 AND is_delete = 0");
            $this->view->setVar("parent",$parentResult);
        }

        //实体列表信息
        $Table = ucfirst($table);
        if($table == "category"){
            $model = new Category();
            $result = $model->find([
                "father_id = $category_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"weight DESC, created_at DESC"
                ]);
        }else{
            $model = new $Table();
            $result = $model->find([
                "category_id = $category_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"weight DESC, created_at DESC",
                "limit"=>15
                ]);
            //热门
            $hotResult = $model->find([
                "category_id = $category_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"view_num DESC, created_at DESC",
                "limit"=>15
                ]);
            $this->view->setVar("hotResult", $hotResult);
        }

        //返回
        $this->view->setVar("result", $result);
        $this->view->pick($table."/index");
    }

    public function showAction($object,$object_id)
    {
        if(!in_array($object, ["category","article","album","link","topic","picture"]))
            $this->route404();

        //全局设置
        $this->getGlobal();

        //内容信息
        $Table = ucfirst($object);
        $model = new $Table();
        $result = $model->findFirst("id=$object_id AND is_visible = 1 AND is_delete = 0");
        if(!$result)
            $this->route404();
        //访问量++
        $result->view_num++;
        $result->save();

        $category_id = $result->category_id;
        
        //热门
        $hotResult = $model->find([
            "category_id = $category_id AND is_visible = 1 AND is_delete = 0",
            "order"=>"view_num DESC, created_at DESC"
            ]);
        $this->view->setVar("hotResult", $hotResult);

        //其他
        if($object == "album"){
            $pictures = Picture::find("album_id=$object_id AND is_visible = 1 AND is_delete = 0");
            $this->view->setVar("pictures", $pictures);
        }
        if($object == "topic"){
            $topicCategoryResult = TopicCategory::find(["topic_id = $object_id","order"=>"weight DESC, id ASC"]);
            
            //临时首页模版
            $SettingResult = Setting::findFirst("keyword='index_topic_id'");
            if($object_id==$SettingResult->value){
                $template = "index/index";
                $indexHotResult = Article::find([
                    "is_hot = 1 AND is_visible = 1 AND is_delete = 0",
                    "order"=>"weight DESC, created_at DESC",
                    "limit"=>6
                ]);
                $this->view->setVar("index_hot", $indexHotResult);
            }
            $categories = [];
            foreach ($topicCategoryResult as $value) {
                $topicTable = ucfirst($value->category_module);
                $topicModel = new $topicTable();
                $topicCategory_id = $value->category_id;

                $columnsOthers = ($topicTable == "Link")?",url":"";

                $categories[] =(object)[
                    "basic" => Category::findFirst("id = $topicCategory_id"),
                    "list"  => $topicModel->find([
                        "category_id=$topicCategory_id AND is_visible = 1 AND is_delete = 0",
                        "order"=>"weight DESC, created_at DESC",
                        "columns"=>"id,title,description,img_dir,created_at".$columnsOthers,
                        "limit"=>8
                    ])
                ];
            }
            $this->view->setVar("categories",$categories);
        }

        //分类信息
        $model = new Category();
        $categoryResult = $model->findFirst("id=$category_id AND is_visible = 1 AND is_delete = 0");
        if(!$categoryResult)
            $this->route404();

        $this->view->setVar("category",$categoryResult);

        //兄弟分类信息，面包屑信息
        if($categoryResult->father_id != 0){
            $father_id = $categoryResult->father_id;
            $brotherResult = $model->find([
                "father_id=$father_id AND is_visible = 1 AND is_delete = 0",
                "order"=>"weight DESC"
                ]);
            $this->view->setVar("brother",$brotherResult);
            $father_path = explode(",", $categoryResult->father_path);
            $parentResult = $model->find(" ( id = ".implode(" OR id=", $father_path)." ) AND is_visible = 1 AND is_delete = 0");
            $this->view->setVar("parent",$parentResult);
        }

        //返回
        $this->view->setVar("result", $result);
        $this->view->pick(isset($template)?$template:$object."/show");              
    }

    
    public function aliasAction(){
        $model = new Alias();
        $name = $this->dispatcher->getParam("alias");
        $result = $model->findFirst("name='$name'");
        if($result){
            if($result->object == "category"){
                $action = "list";
                $params = [$result->object_id];
            }else{
                $action = "show";
                $params = [$result->object,$result->object_id];
            }

            $this->dispatcher->forward(array(
                'controller' => 'index',
                'action' => $action,
                'params' => $params
            ));
        }else{
            echo "not found!";
        }

        return false;
    }

    public function searchAction($content=""){
        //全局设置
        $this->getGlobal();

        $content = ($content!="")?$content:$_GET['content'];

        $articleResult = Article::find([
                    "(title LIKE ?1 OR description LIKE ?1 OR content LIKE ?1) AND is_visible = 1 AND is_delete = 0",
                    "bind" =>[1=>"%$content%"],
                    "order"=>"weight DESC, created_at DESC",
                    "limit"=>15
                ]);

        $linkResult = LINK::find([
                    "(title LIKE ?1 OR description LIKE ?1) AND is_visible = 1 AND is_delete = 0",
                    "bind" =>[1=>"%$content%"],
                    "order"=>"weight DESC, created_at DESC",
                    "limit"=>15
                ]);
        $albumResult = Album::find([
                    "(title LIKE ?1 OR description LIKE ?1) AND is_visible = 1 AND is_delete = 0",
                    "bind" =>[1=>"%$content%"],
                    "order"=>"weight DESC, created_at DESC",
                    "limit"=>15
                ]);
        $topicResult = Topic::find([
                    "(title LIKE ?1 OR description LIKE ?1 OR content LIKE ?1) AND is_visible = 1 AND is_delete = 0",
                    "order"=>"weight DESC, created_at DESC",
                    "bind" =>[1=>"%$content%"],
                    "limit"=>15
                ]);

        $this->view->setVar("content",$content);
        $this->view->setVar("articles",$articleResult);
        $this->view->setVar("links",$linkResult);  
        $this->view->setVar("albums",$albumResult);  
        $this->view->setVar("topics",$topicResult);    

    }

    public function moreAction($object){
        if(!in_array($object, ["category","article","album","link","topic","picture"]))
            $this->jsonResponse(true,[],"object is out");
        if(!$this->request->has("page"))
            $this->jsonResponse(true,[],"page is missing");

        $whereStr = "is_visible = 1 AND is_delete = 0";
        $whereBind = [];

        //关键词过滤
        if($this->request->has("content")){
            $content = $this->request->get("content");
            if(in_array($object, ["article","topic"])){
                $whereStr .= " AND (title LIKE ?1 OR content LIKE ?1)"; 
           }else{
                $whereStr .= " AND title LIKE ?1";
           }
           $whereBind[1] = "%$content%";   
        }

        //分类过滤
        if($this->request->has("category_id")){
            $category_id = $this->request->get("category_id","int");
            $whereStr .= " AND category_id = '$category_id'";
        }

        //分页
        $page = $this->request->get("page","int");

        //查找
        $Table = ucfirst($object);
        $model = new $Table();

        $result = $model::find([
                    $whereStr,
                    "order"=>"weight DESC, created_at DESC",
                    "bind" =>$whereBind,
                    "limit"=>["number" => 15, "offset" => 15*($page-1)]
                ])->toArray();
 
        $this->jsonResponse(false,$result);
    }

    public function tagAction()
    {

    }

    public function initAction()
    {
       
        $dataAll = [
            'Menu'=>[
                ["title"=>"棋院新闻","object"=>"category",  "object_id"=>4,"father_id"=>0],
                ["title"=>"棋手风采","object"=>"category",  "object_id"=>5,"father_id"=>0],
                ["title"=>"师资介绍","object"=>"category",  "object_id"=>6,"father_id"=>0],
                ["title"=>"招生信息","object"=>"category",  "object_id"=>7,"father_id"=>0],
                ["title"=>"成绩公布","object"=>"category",  "object_id"=>8,"father_id"=>0],
                ["title"=>"竞赛规程","object"=>"category",  "object_id"=>9,"father_id"=>0],
                ["title"=>"资料下载","object"=>"category",  "object_id"=>10,"father_id"=>0],

                ["title"=>"国际象棋","object"=>"category",  "object_id"=>16,"father_id"=>1],
                ["title"=>"象棋","object"=>"category",  "object_id"=>17,"father_id"=>1],
                ["title"=>"围棋","object"=>"category",  "object_id"=>18,"father_id"=>1],

                ["title"=>"国际象棋","object"=>"category",  "object_id"=>19,"father_id"=>2],
                ["title"=>"象棋","object"=>"category",  "object_id"=>20,"father_id"=>2],
                ["title"=>"围棋","object"=>"category",  "object_id"=>21,"father_id"=>2],
                ["title"=>"国际象棋","object"=>"category",  "object_id"=>22,"father_id"=>3],
                ["title"=>"象棋","object"=>"category",  "object_id"=>23,"father_id"=>3],
                ["title"=>"围棋","object"=>"category",  "object_id"=>24,"father_id"=>3],
                ["title"=>"国际象棋","object"=>"category",  "object_id"=>25,"father_id"=>4],
                ["title"=>"象棋","object"=>"category",  "object_id"=>26,"father_id"=>4],
                ["title"=>"围棋","object"=>"category",  "object_id"=>27,"father_id"=>4],
                ["title"=>"国际象棋","object"=>"category",  "object_id"=>28,"father_id"=>5],
                ["title"=>"象棋","object"=>"category",  "object_id"=>29,"father_id"=>5],
                ["title"=>"围棋","object"=>"category",  "object_id"=>30,"father_id"=>5],
                ["title"=>"国际象棋","object"=>"category",  "object_id"=>31,"father_id"=>6],
                ["title"=>"象棋","object"=>"category",  "object_id"=>32,"father_id"=>6],
                ["title"=>"围棋","object"=>"category",  "object_id"=>33,"father_id"=>6]                                                               
            ],
        ];

        foreach ($dataAll as $table => $datas) {
            $model = new $table();

            if($table == "TopicCategory") $table_str = "topic_category";
            else $table_str = $table;

            //$model->getReadConnection()->query("TRUNCATE $table_str;");
            foreach ($datas as $data) {
                $model = new $table();
                $model->save($data);
            }
        }
         exit;

        $dataAll = [
            'User'=>[
                ["name"=>"admin","password"=>$this->security->hash("123123"),"role_id"=>1],
            ],
            'Category'=>[
                ["name"=>"代码",        "module"=>"article","father_id"=>"0","father_path"=>"0"],
                ["name"=>"综合资讯",    "module"=>"article","father_id"=>"0","father_path"=>"0"],
                ["name"=>"最新分享代码","module"=>"article","father_id"=>"1","father_path"=>"0,1"],
                ["name"=>"本周热门代码","module"=>"article","father_id"=>"1","father_path"=>"0,1"],
                ["name"=>"链接分类1",   "module"=>"link","father_id"=>"0","father_path"=>"0"],
                ["name"=>"相册分类1",   "module"=>"album","father_id"=>"0","father_path"=>"0"],
                ["name"=>"专题分类1",   "module"=>"topic","father_id"=>"0","father_path"=>"0"],
                ["name"=>"代码3","module"=>"article","father_id"=>"3","father_path"=>"0,1,3"],
                ["name"=>"代码4","module"=>"article","father_id"=>"3","father_path"=>"0,1,3"],
            ],
            'Article'=>[
                [
                    "title"=>"JFinal 2.0 发布，JAVA 极速 WEB+ORM 框架",
                    "description"=>"JFinal 是本星球第一个提出极速开发理念，也是唯一个极速开发框架。自开源以来迅速获得广大开发者的喜爱...",
                    "img_dir"=>"http://www.oschina.net/img/logo/jfinal.gif?t=1399607809000",
                    "content"=>"JFinal 是本星球第一个提出极速开发理念，也是唯一个极速开发框架。自开源以来迅速获得广大开发者的喜爱，极速开发的优势逐步深入人心。由于极速开发威力巨大，所以有了以下在 OSChina 的惊人数据：

1：问答数2600个，在OSChina 在收录的37153个项目中总排名第五位，排前四位的项目分别是：java、android、php、mysql，前四个项目在 OSChina 的收录时间比 JFinal 要早一到四年，问答数量充分表明JFinal是OSChina最活跃的项目

相关链接：http://www.oschina.net/question/tags?catalog=1",
                    "category_id"=>1,
                    "created_by"=>1
                ],
                [
                    "title"=>"Linux Kernel 4.1 发布",
                    "description"=>"",
                    "content"=>"",
                    "img_dir"=>"",
                    "category_id"=>1,
                    "created_by"=>1
                ],
                [
                    "title"=>"FreeMarker 2.3.23 RC1 发布，Java 模板引擎",
                    "description"=>"",
                    "content"=>"",
                    "img_dir"=>"",
                    "category_id"=>1,
                    "created_by"=>1
                ],
                [
                    "title"=>"【每日一博】深入理解学习 Git 常用工作流",
                    "description"=>"",
                    "content"=>"",
                    "img_dir"=>"",
                    "category_id"=>8,
                    "created_by"=>1
                ],
                [
                    "title"=>"让你的 PHP 7 更快 (GCC PGO)",
                    "description"=>"",
                    "content"=>"",
                    "img_dir"=>"",
                    "category_id"=>8,
                    "created_by"=>1
                ],
                [
                    "title"=>"解决远程连接mysql很慢的方法(mysql_connect 打开连接慢)",
                    "description"=>"有次同事提出开发使用的mysql数据库连接很慢，因为我们的mysql开发数据库是单独一台机器部署的，所以认为可能是网络连接问题导致的。",
                    "img_dir"=>"http://chinalove99.net/mall/uploads/2015/01/30/20150130084155EGCQc51e9d44.jpg",
                    "content"=>"在进行 ping和route后发现网络通信都是正常的，而且在mysql机器上进行本地连接发现是很快的，所以网络问题基本上被排除了。以前也遇到过一次这样的问题，可后来就不知怎么突然好了，这次又遭遇这样的问题，所以想看看是不是mysql的配置问题。在查询mysql相关文档和网络搜索后，发现了一个配置似乎可以解决这样的问题，就是在mysql的配置文件中增加如下配置参数：",
                    "category_id"=>3,
                    "created_by"=>1
                ]

            ],
            'Link'=>[
                [
                    "title"=>"谷歌",
                    "url"=>"https://google.com",
                    "category_id"=>5,
                    "created_by"=>1
                ],
                [
                    "title"=>"百度",
                    "url"=>"http://baidu.com",
                    "category_id"=>5,
                    "created_by"=>1
                ]
            ],
            'Album'=>[
                [
                    "title"=>"越往越南的列车",
                    "category_id"=>6,
                    "img_dir"=>"http://img3.douban.com/view/note/large/public/p27433935.jpg",
                    "created_by"=>1
                ]
            ],
            'Picture'=>[
                [
                    "title"=>"列车1",
                    "album_id"=>1,
                    "dir"=>"http://img3.douban.com/view/note/large/public/p27433935.jpg",
                    "created_by"=>1
                ],
                [
                    "title"=>"列车2",
                    "album_id"=>1,
                    "dir"=>"http://img4.douban.com/view/note/large/public/p27433936.jpg",
                    "created_by"=>1
                ]
            ],
            'Topic'=>[
                [
                    "title"=>"首页",
                    "category_id"=>7,
                    "created_by"=>1
                ],
                [
                    "title"=>"燕山大学2015毕业专题",
                    "category_id"=>7,
                    "created_by"=>1
                ]
            ],
            'TopicCategory'=>[
                [
                    "topic_id"=>1,
                    "category_module"=>'article',
                    "category_id"=>1
                ],
                [
                    "topic_id"=>1,
                    "category_module"=>'link',
                    "category_id"=>5
                ]
            ],
            'Alias'=>[
                [
                    "object"=>"category",
                    "object_id"=>1,
                    "name"=>"news"
                ],
                [
                    "object"=>"article",
                    "object_id"=>1,
                    "name"=>"bomb"
                ]
            ],
            'Setting'=>[
                [
                    "name"=>"站点名称",
                    "keyword"=>"site_title",
                    "value"=>"天道棋院",
                ],
                [
                    "name"=>"站点描述",
                    "keyword"=>"site_description",
                    "value"=>"秦皇岛最好的",
                ],
                [
                    "name"=>"站点关键字",
                    "keyword"=>"site_keywords",
                    "value"=>"秦皇岛,棋院",
                ],
                [
                    "name"=>"LOGO",
                    "keyword"=>"logo_dir",
                    "value"=>"#",
                ],
                [
                    "name"=>"地址",
                    "keyword"=>"address",
                    "value"=>"海港区",
                ],
                [
                    "name"=>"邮箱",
                    "keyword"=>"email",
                    "value"=>"123@123.com",
                ],
                [
                    "name"=>"电话",
                    "keyword"=>"phone",
                    "value"=>"11111111111",
                ],
                [
                    "name"=>"备案号",
                    "keyword"=>"icp_number",
                    "value"=>"888",
                ],
                [
                    "name"=>"首页专题",
                    "keyword"=>"index_topic_id",
                    "value"=>"1",
                ],
                [
                    "name"=>"导航分类",
                    "keyword"=>"nav_category_id",
                    "value"=>"5",
                ],
                [
                    "name"=>"友情链接分类",
                    "keyword"=>"favolink_category_id",
                    "value"=>"5",
                ]
            ]
        ];
        foreach ($dataAll as $table => $datas) {
            $model = new $table();

            if($table == "TopicCategory") $table_str = "topic_category";
            else $table_str = $table;

            $model->getReadConnection()->query("TRUNCATE $table_str;");
            foreach ($datas as $data) {
                $model = new $table();
                $model->save($data);
            }
        }
        
        

        
       
    }

}

