<?php
/**
 * 内容发布器
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model;
class Publish extends Abs {

    /**
     * 发布一篇文章
     * 
     * @param array $article 文件内容
     * @param int   $publish 是否真正发布（如果为否，则仅为预览，输出至Buffer中）
     * 
     * @return array
     */
    static public function article(array $article, $publish = true) {
        \Model\User::validateAuth($article['uid']);
        
        $path = sprintf('article/%u.html', $article['id']);
        $message = sprintf('update article %u [%s]', $article['id'], date('Y-m-d H:i:s'));

        $blog = Blog::show();
        $category = Category::show($article['category_id']);
        
        $now = time();
        $publish_time = date('Y-m-d H:i:s', $now);
        
        $tpl_vars = array(
            'blog'         => $blog,
            'category'     => $category,
            'article'      => $article,
            'publish'      => $publish,
            'publish_date' => date('Y-m-d', $now),
        );
        
        $smarty = \Comm\Smarty::init();
        
        if($publish) {
            $content = $smarty->render('tpl:article', $tpl_vars);
            $result = self::publishUserRespos($path, $content, $message);
            
            //发布成功，更新发布时间与发布状态
            try {
                Article::update($article, ['state' => 1, 'publish_time' => $publish_time]);
            } catch(\Exception $e) {}
            
        } else {
            $result = $smarty->display('tpl:article', $tpl_vars);
        }

        return $result;
    }
    
    /**
     * 删除一篇文章
     * 
     * @param int $id
     * 
     * @return \stdClass
     */
    static public function articleDestroy($id) {
        $path = sprintf('article/%u.html', $id);
        $message = sprintf('delete article %u [%s]', $id, date('Y-m-d H:i:s'));
        
        return self::destroyUserRespos($path, $message);
    }
    
    /**
     * 发布域名，并更新数据库
     * 
     * @param string $domain
     * 
     * @return \stdClass
     * 
     * @throws \Exception\Msg
     */
    static public function domain($domain) {
        $user = User::show();
        $repo = Github::showDefaultBlogRepoName($user['metadata']['login']);
        
        if($domain) {
            $dns = dns_get_record($domain, DNS_CNAME);
            if(!$dns) {
                throw new \Exception\Msg('指定域名没有设置CNAME记录');
            }
            if(count($dns) > 1) {
                throw new \Exception\Msg('指定域名CNAME记录设置超过一个');
            }
            if($dns[0]['target'] !== $repo) {
                throw new \Exception\Msg(sprintf('指定域名CNAME错误(错误记录为：%s)', $dns[0]['target']));
            }
            
            $message = sprintf('Bind domain %s', $domain);
        } else {
            $message = sprintf('Remove domain');
        }

        $path = 'CNAME';
        $result = self::publishUserRespos($path, $domain, $message);
    
        //上传文件成功，更新数据库
        if(!empty($result->content) && !empty($result->commit)) {
            Blog::save(array(
                'domain' => $domain,
            ));
        }
        
        return $result;
    }
    
    /**
     * 发布主分类数据
     * 
     * @param boolean $use_master 是否使用主库
     * @param boolean $publish    是否是真的发布，如果不是，内容输出至Buffer
     * 
     * @return \stdClass
     */
    static public function sidebar($use_master = false, $publish = true) {
        $user = User::show();
        $blog = Blog::show();
        $categorys = Category::showUserAll(false, true, true);
        
        $tpl_vars = array(
            'user'      => $user,
            'blog'      => $blog,
            'categorys' => $categorys,
        );
        
        $smarty = \Comm\Smarty::init();
        
        if($publish) {
            $content = $smarty->render('tpl:sidebar', $tpl_vars);
            
            $path = 'block/sidebar.html';
            $message = sprintf('update sidebar [%s]', date('Y-m-d H:i:s'));
            $result = self::publishUserRespos($path, $content, $message);
        } else {
            $result = $smarty->display('tpl:sidebar', $tpl_vars);
        }

        
        return $result;
    }
    
    /**
     * 发布一个分类下的文章列表
     * @param unknown $category_id
     */
    static public function articleListByCategory($category_id) {
        
    }
    
    /**
     * 发布首页内容
     * 
     * @param array       $articles 文章列表
     * @param \Comm\Pager $pager    分页器
     * @param array       $blog     博客配置
     * @param string      $publish  是否真正发布
     * 
     * @return mixed
     */
    static public function home(array $articles, \Comm\Pager $pager, array $blog = null, $publish = true) {
        $blog || $blog = Blog::show();
        $smarty = \Comm\Smarty::init();
        
        //设置分页回调
        $pager->link_callback = function($page) use ($publish) {
            if($page <= 1) {
                $result = '/';
            } else {
                $result = "/index/{$page}.html";
            }
            
            if(!$publish) {
                $result = '#' . $result;
            }
            
            return $result;
        };
        
        $tpl_vars = array(
            'blog'     => $blog,
            'articles' => isset($articles['result']) ? $articles['result'] : array(),
            'pager'    => $pager,
            'publish'  => $publish,
        );
        
        if($publish) {
            if($pager->page == 1) {
                $path = 'index.html';
            } else {
                $path = "index/{$pager->page}.html";
            }
            $message = sprintf('update home (%u) [%s]', $pager->page, date('Y-m-d H:i:s'));
            $content = $smarty->render('tpl:home', $tpl_vars);
            $result = \Model\Publish::publishUserRespos($path, $content, $message);
        } else {
            $result = $smarty->display('tpl:home', $tpl_vars);
        }
        
        return $result;
    }
    
    /**
     * 发布一个分类下的文章列表
     * 
     * @param array       $category 分类信息
     * @param array       $articles 文章列表
     * @param \Comm\Pager $pager    分页
     * @param array       $blog     博客数据（可选）
     * @param boolean     $publish   是否正式发布，如果为否则仅是预览
     * 
     * @return mixed
     */
    static public function categoryArticleList(array $category, array $articles, \Comm\Pager $pager, array $blog = null, $publish = true) {
        $blog || $blog = \Model\Blog::show();
        
        
        //设置分页回调
        $pager->link_callback = function($page) use ($category, $publish) {
            $result = "/category/{$category['alias']}-{$page}.html";
        
            if(!$publish) {
                $result = '#' . $result;
            }
        
            return $result;
        };
        
        //渲染模板，发布或预览 
        $smarty = \Comm\Smarty::init();
        $tpl_vars = array(
            'blog'     => $blog,
            'category' => $category,
            'articles' => $articles,
            'pager'    => $pager,
            'publish'  => $publish,
        );
        if($publish) {
            $content = $smarty->render('tpl:article-list', $tpl_vars);
            
            $message = sprintf('update category %u(%u) [%s]', $category['id'], $pager->page, date('Y-m-d H:i:s'));
            $result = self::publishUserRespos("category/{$category['alias']}-{$pager->page}.html", $content, $message);
        } else {
            $result = $smarty->display('tpl:article-list', $tpl_vars);
        }

        return $result;
    }
    
    /**
     * 直接向当前用户的博客源发布一个数据
     * 
     * @param sting $path    路径
     * @param sting $content 文件内容
     * @param sting $message 注释
     * 
     * @return \stdClass
     */
    static public function publishUserRespos($path, $content, $message) {
        $user = User::show();
        $login = $user['metadata']['login'];
        $repo = Github::showDefaultBlogRepoName($user['metadata']['login']);
        
        $respositories = new \Api\Github\Respositories();
        return $respositories->replace($login, $repo, $path, $content, $message);
    }
    
    /**
     * 直接向当前用户的博客源删除一个数据
     * 
     * @param sting $path    路径
     * @param sting $message 注释
     * 
     * @return \stdClass
     */
    static public function destroyUserRespos($path, $message) {
        $user = User::show();
        $login = $user['metadata']['login'];
        $repo = Github::showDefaultBlogRepoName($user['metadata']['login']);
        
        $respositories = new \Api\Github\Respositories();
        return $respositories->delete($login, $repo, $path, $message);
    }


}
