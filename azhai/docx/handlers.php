<?php

use Docx\Common;
use Docx\Base\Handler;
use Docx\Web\Response;
use Docx\Cache\FileCache;
use Docx\Log\FileLogger;
use Docx\Utility\FileSystem;
use Docx\Utility\Markdoc;
use Docx\Utility\Repository;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);


trait THandler
{
    public function parseURL($curr_url)
    {
        $curr_url = trim($curr_url, '/');
        if (empty($curr_url)) {
            $curr_url = 'index';
            $this->page_type = 'home';
        }
        $this->app->globals['curr_url'] = $curr_url;
        $this->app->globals['page_type'] = $this->page_type;
        $assets_url = $this->app->getConfig('public_dir') . '/'
                    . $this->app->getConfig('assets_dir');
        $this->app->globals['urlpre'] = $this->app->url->getPrefix();
        $depth = $this->app->url->getDepth();
        if ($depth <= 0) {
            $this->app->globals['assets_url'] = $assets_url;
        } else {
            $this->app->globals['assets_url'] = '../' . $this->app->globals['urlpre'] . $assets_url;
        }
        return $curr_url;
    }

    public function getHomeURL()
    {
        if ($route_key = $this->app->url->getRouteKey()) {
            $home_url = sprintf('?%s=/', $route_key);
        } else {
            $home_url = '../' . $this->app->globals['urlpre'];
        }
        return $home_url . 'index/';
    }

    public function getHtmlURL()
    {
        if ($route_key = $this->app->url->getRouteKey()) {
            $home_url = '';
        } else {
            $home_url = '../../' . $this->app->globals['urlpre'];
        }
        $public_dir = $this->app->getConfig('public_dir');
        return $home_url . $public_dir . '/';
    }
}


class DocScaner
{
    protected $app = null;
    protected $organiz = null;
    protected $archives_dir = '';

    public function __construct(&$app)
    {
        $this->app = $app;
        $this->prepare();
    }
    
    public function prepare()
    {
        $public_dir = APP_DIR . DS . $this->app->getConfig('public_dir');
        $this->archives_dir = $public_dir . DS . $this->app->getConfig('archives_dir');
    }
    
    public function scanDocs($remove_empty = false)
    {
        if ($remove_empty) {
            FileSystem::removeEmptyDirs($this->archives_dir, 1);
        }
        $fs = new FileSystem('.md');
        $cache_dir = APP_DIR . DS . $this->app->getConfig('cache_dir');
        $cache_file = $cache_dir . DS . 'docs' . $this->app->getConfig('cache_ext');
        $cache = new FileCache($cache_file);
        $this->organiz = $fs->getOrganiz($this->archives_dir, $cache->getAgent());
        $logger = new FileLogger($cache_dir);
        $logger->getLogging('access')->debug('Scan done.');
        return $this->organiz;
    }
    
    public function locate($url)
    {
        $node = $this->organiz;
        $pieces = explode('/', $url);
        foreach ($pieces as $slug) {
            if (!isset($node['nodes'][$slug])) {
                return;
            }
            $node = $node['nodes'][$slug];
        }
        return $this->archives_dir . DS . $node['path'];
    }
    
    public function parseDoc(Markdoc& $doc)
    {
        $layout = $doc->getMetaData('layout');
        if (empty($layout)) {
            $layout = $this->app->getConfig('layout');
        }
        $page_data = $doc->getPageData();
        $page_data['layout'] = $layout;
        return $page_data;
    }
    
    public function updateDoc(Markdoc& $doc)
    {
        $request = $this->app->request;
        $metatext = $request->getPost('metatext');
        $metatext = htmlspecialchars_decode($metatext, ENT_QUOTES);
        $markdown = $request->getPost('markdown');
        $markdown = htmlspecialchars_decode(trim($markdown), ENT_QUOTES);
        $doc->update($metatext, $markdown);
    }
}


/**
 * 显示页面.
 */
class ViewHandler extends Handler
{
    use THandler;
    
    protected $page_type = 'view';
    protected $curr_url = '';
    
    public function prepare()
    {
        $this->app->globals['options'] = $this->app->getConfig();
        $this->app->globals['theme_dir'] = PROJECT_DIR . DS . $this->app->getConfig('theme_dir');
        $this->app->globals['urlext'] = '/';
        $this->scaner = new DocScaner($this->app);
    }
    
    public function finish()
    {
        $template = $this->app->globals['theme_dir'] . DS . $this->app->globals['layout'] . '.php';
        $this->app->response->addFrameFile($template);
    }

    public function doAction($path, $update = false)
    {
        $this->app->globals['organiz'] = $this->scaner->scanDocs();
        $this->curr_url = $this->parseURL($path);
        $nodepath = $this->scaner->locate($this->curr_url);
        $doc = new Markdoc($nodepath);
        if ($update) {
            $this->scaner->updateDoc($doc);
        }
        $page_data = $this->scaner->parseDoc($doc);
        $this->app->context['page'] = $page_data;
        return $page_data;
    }

    public function getAction($path = '')
    {
        $this->prepare();
        $page_data = $this->doAction($path, false);
        $this->app->globals['layout'] = $page_data['layout'];
        $this->finish();
    }
}


/**
 * 编辑页面.
 */
class EditHandler extends ViewHandler
{
    use THandler;
    
    protected $page_type = 'edit';
    protected $staticize_url = '';
    
    public function staticize($curr_url)
    {
        $public_dir = APP_DIR . DS . $this->app->getConfig('public_dir');
        $html_file = $public_dir . DS . $curr_url . '.html';
        @mkdir(dirname($html_file), 0755, true);
        file_put_contents($html_file, strval($this), LOCK_EX);
    }
    
    public function finish()
    {
        parent::finish();
        if ($this->staticize_url) {
            $this->staticize($this->staticize_url);
        }
    }

    public function getAction($path = '')
    {
        $this->prepare();
        $page_data = $this->doAction($path, false);
        $this->app->globals['layout'] = 'edit' . DS . $page_data['layout'];
        $this->finish();
    }
    
    public function postAction($path = '')
    {
        $this->prepare();
        $page_data = $this->doAction($path, true);
        $this->app->globals['layout'] = $page_data['layout'];
        $this->staticize_url = $this->curr_url;
        $this->finish();
    }
}


/**
 * 生成静态页.
 */
class HtmlHandler extends Handler
{
    use THandler;
    
    protected $page_type = 'html';
    
    public static function initRepo(Repository& $repo, $branch)
    {
        $local_branches = $repo->getBranches(Repository::BRANCHES_LOCAL);
        if (!in_array($branch, $local_branches, true)) {
            $remote_branches = $repo->getBranches(Repository::BRANCHES_REMOTE);
            if (empty($remote_branches)) {
                $repo->checkout('--orphan', $branch);
            }
        }
        if (!in_array('master', $local_branches, true)) {
            $repo->add();
            $repo->commitMutely('init'); //创建master分支
            $repo->checkout('-b', 'master');
        } else {
            $repo->checkout('master');
        }
        $repo->pull('origin', $branch);
    }
    
    public function parseHtmlURL($curr_url = '')
    {
        if (empty($curr_url)) {
            $curr_url = $this->getCurrURL();
        }
        $this->app->globals['curr_url'] = $curr_url;
        $this->app->globals['page_type'] = $this->page_type;
        
        $depth = substr_count(trim($curr_url, '/'), '/');
        $this->app->globals['urlpre'] = str_repeat('../', $depth);
        $this->app->globals['assets_url'] = $this->app->globals['urlpre']
                            . $this->app->getConfig('assets_dir');
        return $curr_url;
    }
    
    public function removeOldStaticFiles()
    {
        $public_dir = APP_DIR . DS . $this->app->getConfig('public_dir');
        $ignores = [
            $public_dir . DS . '.git',
            $public_dir . DS . $this->app->getConfig('archives_dir'),
            $public_dir . DS . $this->app->getConfig('assets_dir'),
        ];
        FileSystem::removeAllFiles($public_dir, $ignores);
    }
    
    public function prepare()
    {
        $this->app->globals['options'] = $this->app->getConfig();
        $this->app->globals['theme_dir'] = PROJECT_DIR . DS . $this->app->getConfig('theme_dir');
        $this->app->globals['urlext'] = '.html';
        $branch = $this->app->getConfig('repo_branch');
        $repo = $this->app->repo;
        self::initRepo($repo, $branch);
    }

    public function finish()
    {
        return Response::redirect($this->getHomeURL());
    }

    public function getAction()
    {
        $this->prepare();
        $organiz = $this->scaner->scanDocs(true);
        $this->removeOldStaticFiles();
        $handler = $this;
        $public_dir = APP_DIR . DS . $this->app->getConfig('public_dir');
        $archives_dir = $public_dir . DS . $this->app->getConfig('archives_dir');
        $staticize = function($node, $curr_url, $children = [])
                        use($handler, $organiz, $public_dir, $archives_dir)
        {
            if ($node['is_file'] === 0) {
                return;
            }
            $nodepath = $archives_dir . DS . $node['path'];
            $doc = new Markdoc($nodepath);
            $page_data = $handler->backend->parseDoc($doc);
            $handler->context['page'] = $page_data;
            $handler->globals['layout'] = $page_data['layout'];
            $handler->globals['organiz'] = $organiz;
            $handler->parseHtmlURL($curr_url);
            $handler->template = $handler->globals['theme_dir'] . DS . $page_data['layout'] . '.php';
            $html_file = $public_dir . DS . $curr_url . '.html';
            @mkdir(dirname($html_file), 0755, true);
            file_put_contents($html_file, strval($handler), LOCK_EX);
        };
        FileSystem::traverse($organiz['nodes'], $staticize);
        $this->finish();
    }
}


/**
 * Git发布.
 */
class RepoHandler extends Handler
{
    use THandler;
    
    public static function pushRepo(Repository& $repo, $branch, $comment)
    {
        $repo->checkout('master');
        $repo->add();
        $repo->commitMutely($comment);
        
        $repo->pull('origin', $branch);
        $repo->checkout($branch);
        $repo->merge('master');
        $repo->push('origin', $branch, '--tags');
    }

    public function finish()
    {
        return Response::redirect('../' . $this->getHomeURL());
    }
    
    public function getAction()
    {
        $request = $this->app->request;
        $comment = $request->getPost('comment', 'Nothing');
        $repo = $this->app->repo;
        $branch = $this->app->getConfig('repo_branch');
        self::pushRepo($repo, $branch, $comment);
        $this->finish();
    }
}
