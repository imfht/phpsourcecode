<?php
/**
 * @package     Paginator.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月24日
 */

namespace SlimCustom\Libs\Paginator;

use SlimCustom\Libs\Support\Collection;
use SlimCustom\Libs\App;

/**
 * 分页处理器
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Paginator
{
    /**
     * 默认单页内容条数
     * 
     * @var integer
     */
    const COUNT = 10;
    
    /**
     * 分页模式 list：列表分页，content：内容分页
     * 
     * @var string
     */
    protected $mode = 'list';
    
    /**
     * 是否显示全部内容
     * 
     * @var string
     */
    protected $isAll = false;
    
    /**
     * All of the items being paginated.
     *
     * @var Collection
     */
    protected $items;
    
    /**
     * The total.
     *
     * @var int
     */
    protected $total;
    
    /**
     * The numbers of total page.
     *
     * @var int
     */
    protected $totalPage;
    
    /**
     * The number of items to be shown per page.
     *
     * @var int
     */
    protected $perPage;
    
    /**
     * The current page being "viewed".
     *
     * @var int
     */
    protected $currentPage;
    
    /**
     * The base path to assign to all URLs.
     *
     * @var string
     */
    protected $path;
    
    /**
     * The query parameters to add to all URLs.
     *
     * @var array
     */
    protected $query = [];
    
    /**
     * The URL fragment to add to all URLs.
     *
     * @var string|null
     */
    protected $fragment = null;
    
    /**
     * The query string variable used to store the page.
     *
     * @var string
     */
    protected $pageName = 'page';
    
    /**
     * The current page resolver callback.
     *
     * @var \Closure
     */
    protected static $currentPathResolver;
    
    /**
     * The current page resolver callback.
     *
     * @var \Closure
     */
    protected static $currentPageResolver;
    
    /**
     * The default presenter resolver.
     *
     * @var \Closure
     */
    protected static $presenterResolver;
    
    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    protected $hasMore;
    
    /**
     * 初始化分页器
     * 
     * @param array $items
     * @param integer $perPage
     * @param integer $currentPage
     * @param array $options
     */
    public function __construct($items, $perPage, $currentPage = null, array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->{$key} = $value;
        }
        
        $this->items = $items instanceof Collection ? $items : new Collection($items);
        $this->path = ! $this->path ? rtrim(request()->getUri()->getPath(), '/') : $this->path;
        $this->perPage = $perPage;
        $this->currentPage = $this->setCurrentPage($currentPage);
        $this->total = $this->total();
        $this->totalPage = ceil($this->total / $perPage);
        if ($this->isAll && ! $this->isList()) {
            $this->perPage = $this->total;
            $this->totalPage = 1;
            $this->currentPage = 1;
        }
        $this->isList() ?: $this->items = $this->items->slice(($this->currentPage - 1) * $this->perPage, $this->perPage);
        
        $this->checkForMorePages();
    }
    
    /**
     * 创建一个新的实例
     * 
     * @param array $items
     * @param integer $perPage
     * @param integer $currentPage
     * @param array $options
     * @return \SlimCustom\Libs\Paginator\Paginator
     */
    public static function make($items, $perPage, $currentPage = null, array $options = [])
    {
        return new static($items, $perPage, $currentPage, $options);
    }
    
    /**
     * Return Total number
     * 
     * @throws \Exception
     * @return number
     */
    protected function total()
    {
        switch ($this->mode) {
            case 'list':
                $total = App::$instance->getContainer()['statement']->unsetLimit()->count('*', 'total')->execute()->fetch()['total'];
                break;
            case 'content':
                $total = $this->items->count();
                break;
            default:
                throw new \Exception('Not Support Page Mode');
                break;
        }
        return intval($total);
    }
    
    /**
     * 是否是列表模式
     * 
     * @return boolean
     */
    protected function isList()
    {
        return ($this->mode == 'list') ? true : false;
    }
    
    /**
     * Get the current page for the request.
     *
     * @param  int  $currentPage
     * @return int
     */
    protected function setCurrentPage($currentPage)
    {
        $currentPage = $currentPage ?: static::resolveCurrentPage();
    
        return $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    }
    
    /**
     * Check for more pages. The last item will be sliced off.
     *
     * @return void
     */
    protected function checkForMorePages()
    {
        $this->hasMore = $this->currentPage < $this->totalPage;
    }
    
    /**
     * Get the URL for the next page.
     *
     * @return string|null
     */
    public function nextPageUrl()
    {
        if ($this->hasMore) {
            return $this->url($this->currentPage() + 1);
        }
    }
    
    /**
     * Determine if there are more items in the data source.
     *
     * @return bool
     */
    public function hasMorePages()
    {
        return $this->hasMore;
    }
    
    /**
     * Render the paginator using the given presenter.
     *
     * @param  \Illuminate\Contracts\Pagination\Presenter|null  $presenter
     * @return string
     */
    public function render(Presenter $presenter = null)
    {
        if (is_null($presenter) && static::$presenterResolver) {
            $presenter = call_user_func(static::$presenterResolver, $this);
        }
    
        $presenter = $presenter ?: new SimpleBootstrapThreePresenter($this);
    
        return $presenter->render();
    }
    
    /**
     * Determine if the given value is a valid page number.
     *
     * @param  int  $page
     * @return bool
     */
    protected function isValidPageNumber($page)
    {
        return $page >= 1 && filter_var($page, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Create a range of pagination URLs.
     *
     * @param  int  $start
     * @param  int  $end
     * @return string
     */
    public function getUrlRange($start, $end)
    {
        $urls = [];
    
        for ($page = $start; $page <= $end; $page++) {
            $urls[$page] = $this->url($page);
        }
    
        return $urls;
    }
    
    /**
     * Get a URL for a given page number.
     *
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }
    
        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = [$this->pageName => $page];
    
        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }
    
        return $this->path.'?'
            .urldecode(http_build_query($parameters, null, '&'))
            .$this->buildFragment();
    }
    
    /**
     * Get the URL for the previous page.
     *
     * @return string|null
     */
    public function previousPageUrl()
    {
        if ($this->currentPage() > 1) {
            return $this->url($this->currentPage() - 1);
        }
    }
    
    /**
     * Get / set the URL fragment to be appended to URLs.
     *
     * @param  string|null  $fragment
     * @return $this|string|null
     */
    public function fragment($fragment = null)
    {
        if (is_null($fragment)) {
            return $this->fragment;
        }
    
        $this->fragment = $fragment;
    
        return $this;
    }
    
    /**
     * Add a set of query string values to the paginator.
     *
     * @param  array|string  $key
     * @param  string|null  $value
     * @return $this
     */
    public function appends($key, $value = null)
    {
        if (is_array($key)) {
            return $this->appendArray($key);
        }
    
        return $this->addQuery($key, $value);
    }
    
    /**
     * Add an array of query string values.
     *
     * @param  array  $keys
     * @return $this
     */
    protected function appendArray(array $keys)
    {
        foreach ($keys as $key => $value) {
            $this->addQuery($key, $value);
        }
    
        return $this;
    }
    
    /**
     * Add a query string value to the paginator.
     *
     * @param  string  $key
     * @param  string  $value
     * @return $this
     */
    public function addQuery($key, $value)
    {
        if ($key !== $this->pageName) {
            $this->query[$key] = $value;
        }
    
        return $this;
    }
    
    /**
     * Build the full fragment portion of a URL.
     *
     * @return string
     */
    protected function buildFragment()
    {
        return $this->fragment ? '#'.$this->fragment : '';
    }
    
    /**
     * Get the slice of items being paginated.
     *
     * @return array
     */
    public function items()
    {
        return $this->items->all();
    }
    
    /**
     * Get the number of the first item in the slice.
     *
     * @return int
     */
    public function firstItem()
    {
        return ($this->currentPage - 1) * $this->perPage + 1;
    }
    
    /**
     * Get the number of the last item in the slice.
     *
     * @return int
     */
    public function lastItem()
    {
        return $this->firstItem() + $this->count() - 1;
    }
    
    /**
     * Get the number of items shown per page.
     *
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }
    
    /**
     * Get the current page.
     *
     * @return int
     */
    public function currentPage()
    {
        return $this->currentPage;
    }
    
    /**
     * Determine if there are enough items to split into multiple pages.
     *
     * @return bool
     */
    public function hasPages()
    {
        return ! ($this->currentPage() == 1 && ! $this->hasMorePages());
    }
    
    /**
     * Resolve the current request path or return the default value.
     *
     * @param  string  $default
     * @return string
     */
    public static function resolveCurrentPath($default = '/')
    {
        if (isset(static::$currentPathResolver)) {
            return call_user_func(static::$currentPathResolver);
        }
    
        return $default;
    }
    
    /**
     * Set the current request path resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function currentPathResolver(Closure $resolver)
    {
        static::$currentPathResolver = $resolver;
    }
    
    /**
     * Resolve the current page or return the default value.
     *
     * @param  string  $pageName
     * @param  int  $default
     * @return int
     */
    public static function resolveCurrentPage($pageName = 'page', $default = 1)
    {
        if (isset(static::$currentPageResolver)) {
            return call_user_func(static::$currentPageResolver, $pageName);
        }
    
        return $default;
    }
    
    /**
     * Set the current page resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function currentPageResolver(Closure $resolver)
    {
        static::$currentPageResolver = $resolver;
    }
    
    /**
     * Set the default Presenter resolver.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function presenter(Closure $resolver)
    {
        static::$presenterResolver = $resolver;
    }
    
    /**
     * Get the query string variable used to store the page.
     *
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }
    
    /**
     * Set the query string variable used to store the page.
     *
     * @param  string  $name
     * @return $this
     */
    public function setPageName($name)
    {
        $this->pageName = $name;
    
        return $this;
    }
    
    /**
     * Set the base path to assign to all URLs.
     *
     * @param  string  $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }
    
    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items->all());
    }
    
    /**
     * Determine if the list of items is empty or not.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->items->isEmpty();
    }
    
    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count()
    {
        return $this->items->count();
    }
    
    /**
     * Get the paginator's underlying collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCollection()
    {
        return $this->items;
    }
    
    /**
     * Determine if the given item exists.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->items->has($key);
    }
    
    /**
     * Get the item at the given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items->get($key);
    }
    
    /**
     * Set the item at the given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->items->put($key, $value);
    }
    
    /**
     * Unset the item at the given key.
     *
     * @param  mixed  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->items->remove($key);
    }
    
    /**
     * Make dynamic calls into the collection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getCollection(), $method], $parameters);
    }
    
    /**
     * Render the contents of the paginator when casting to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
    
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'page' => [
                'total' => $this->total,
                'total_page' => $this->totalPage,
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'data' => $this->items->all()
        ];
    }
    
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}