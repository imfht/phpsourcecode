<?php
/**
 * Created by Joy.
 * User: Joy
 */
namespace App\Libs;

use App\Model\System;
use App\Model\Project;
use App\Model\Article;
use App\Model\Category;
use App\Model\Person;
use App\Model\Menu;
use App\Model\Adspace;
use App\Model\Adimage;
use Cache;
use Carbon\Carbon;
use Request;

define('findAll', 0);   //查找全部
define('findCategory', 1);   //按分类查找，人物无该参数
define('findTop', 2);   //按置顶查找，人物无该参数
define('findRecommend', 3); //按推荐查找
define('byId', 0); //按id排序
define('bySort', 1); //按sort排序(排序字段)
define('byViews', 2); //按浏览量排序，人物无该参数
define('byCost', 3); //按花费排序，仅项目有该参数
define('byPoint', 4); //按贡献排序，仅人物有该参数
define('noShowHide', 1); //不显示隐藏分类
define('showHide', 0); //显示隐藏分类
define('findText', 1); //读取文字友情链接
define('findImg', 2); //读取图片友情链接

/**模板功能
 * 实现快速切换主题模板功能
 * 实现数据缓存功能
 * $theme：主题所在目录
 * $system：系统参数，缓存
 * $types：根分类信息，缓存
 * article_data：按规则读取文章，缓存
 * project_data：按规则读取项目，缓存
 * person_data：按规则读取人物，缓存
 */

class Theme
{
    public static function view($view, $data = array(),$theme = '')
    {
        $key_system = 'system_info';
        if (Cache::has($key_system)) {
            $system = Cache::get($key_system);
        } else {
            $system = System::getValue();
            $expiresAt = Carbon::now()->addMinutes(60*24);
            Cache::add($key_system, $system, $expiresAt);
        }

        $key_type = 'categories_info';
        if (Cache::store('category')->has($key_type)) {
            $types = Cache::store('category')->get($key_type);
        } else {
            $types = Category::where('parent_id',0)->isNavShow()->sortByDesc('sort')->get();
            $expiresAt = Carbon::now()->addMinutes(60);
            Cache::store('category')->put($key_type, $types, $expiresAt);
        }

        //后台模板独立
        $path = Request::path();
        $path = explode('/',$path);
        if($path[0]=='admin') $theme = 'manage';
        //查询网站模板
        if(!isset($system['theme'])) $system['theme'] = 'time';
        $theme = $theme != '' ? $theme : $system['theme'];
        $data['theme'] = $theme;
        $data['system'] = $system;
        $data['types'] = $types;
        return view($theme . '/' . $view, $data);
    }

    /**调用文章数据
     * @param $num 查询数量
     * @param $order 排序规则
     * @param $where 查询条件
     * @param $type 当查询有参数时，该项为参数
     * @param $offset 从第几个开始查询
     * 返回项目数组
     */
    public static function article_data($num,$order = null,$where = null,$type = 0,$offset = 0)
    {
        $num = intval($num);
        $offset = intval($offset);
        $key = 'article_'.$num.'_'.$order.'_'.$where.'_'.$type.'_'.$offset;
        if (Cache::store('article')->has($key)) {
            $date = Cache::store('article')->get($key);
            return $date;
        } else {
            switch ($order) {
                case byId:
                    $order_str = 'id';
                    break;
                case bySort:
                    $order_str = 'sort';
                    break;
                case byViews:
                    $order_str = 'views';
                    break;
                default:
                    $order_str = 'id';
                    break;
            }

            if(strpos($type,',')>0) {
                $type = explode(',', $type);
                foreach($type as $key => $value){
                    $type[$key] = intval($value);
                }
            }
            else $type = intval($type);

            $whereArr = explode(',', $where);
            $whereStr = [];
            foreach($whereArr as $value){
                $value = intval($value);
                switch ($value) {
                    case findAll:
                        break;
                    case findRecommend:
                        $whereStr[] = ['is_recommend', '>', 0];
                        break;
                    case findTop:
                        $whereStr[] = ['is_top', '>', 0];
                        break;
                    case findCategory:
                        $whereStr[] = ['category_id', $type];
                        break;
                    default:
                        break;
                }
            }

            $date = Article::where('is_show','>',0);
            foreach($whereStr as $value){
                if($value != ''){
                    switch (count($value)) {
                        case 3:
                            $date = $date->where("$value[0]","$value[1]","$value[2]");
                            break;
                        case 2:
                            if(is_array($value[1])) $date = $date->whereIn("$value[0]",$value[1]);
                            else $date = $date->where("$value[0]","$value[1]");
                            break;
                        default:
                            break;
                    }
                }
            }
            $date = $date->sortByDesc($order_str)->take($num)->Offset($offset)->get();

            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('article')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**调用项目数据
     * @param $num 查询数量
     * @param $order 排序规则
     * @param $where 查询条件
     * @param $type 当查询有参数时，该项为参数
     * @param $offset 从第几个开始查询
     * 返回项目数组
     */
    public static function project_data($num,$order = null,$where = null,$type = 0,$offset = 0)
    {
        $num = intval($num);
        $offset = intval($offset);
        $key = 'project_'.$num.'_'.$order.'_'.$where.'_'.$type.'_'.$offset;
        if (Cache::store('project')->has($key)) {
            $date = Cache::store('project')->get($key);
            return $date;
        } else {
            switch ($order) {
                case byId:
                    $order_str = 'id';
                    break;
                case bySort:
                    $order_str = 'sort';
                    break;
                case byViews:
                    $order_str = 'views';
                    break;
                case byCost:
                    $order_str = 'cost';
                    break;
                default:
                    $order_str = 'id';
                    break;
            }
            $type = intval($type);
            switch ($where) {
                case findAll:
                    $date = Project::where('is_show','>',0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
                case findRecommend:
                    $date = Project::where('is_show','>',0)->where('is_recommend', '>', 0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
                case findCategory:
                    $date = Project::where('is_show','>',0)->where('category_id', $type)->orderBy($order_str, 'desc')->take($num)->Offset($offset)->get();
                    break;
                default:
                    $date = Project::where('is_show','>',0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
            }
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('project')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**调用人物数据
     * @param $num 查询数量
     * @param $order 排序规则
     * @param $where 查询条件
     * @param $type 该参数暂时无用
     * @param $offset 从第几个开始查询
     * 返回项目数组
     */
    public static function person_data($num,$order = null,$where = null,$type = 0,$offset = 0)
    {
        $num = intval($num);
        $offset = intval($offset);
        $key = 'person_'.$num.'_'.$order.'_'.$where.'_'.$type.'_'.$offset;
        if (Cache::store('person')->has($key)) {
            $date = Cache::store('person')->get($key);
            return $date;
        } else {
            switch ($order) {
                case byId:
                    $order_str = 'id';
                    break;
                case bySort:
                    $order_str = 'sort';
                    break;
                case byPoint:
                    $order_str = 'point';
                    break;
                default:
                    $order_str = 'id';
                    break;
            }
            switch ($where) {
                case findAll:
                    $date = Person::where('is_show','>',0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
                case findRecommend:
                    $date = Person::where('is_show','>',0)->where('is_recommend', '>', 0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
                default:
                    $date = Person::where('is_show','>',0)->sortByDesc($order_str)->take($num)->Offset($offset)->get();
                    break;
            }
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('person')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**获取子分类*/
    public static function categories($parent_id = 0, $show_type = noShowHide){
        $parent_id = intval($parent_id);
        $show_type = intval($show_type);
        $key = 'categories_'.$parent_id.'_'.$show_type;
        if (Cache::store('category')->has($key)) {
            $date = Cache::store('category')->get($key);
            return $date;
        } else {
            $date = Category::where('is_nav_show','>=',$show_type )->where('parent_id',$parent_id)->sortByDesc('sort')->get();
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('category')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**分类树*/
    public static function categoryTree($id = 0, $step = 0){
        $key = 'categoryTree';
        $id = intval($id);
        $step = intval($step);
        if (Cache::store('category')->has($key) && $id == 0 && $step == 0) {
            $date = Cache::store('category')->get($key);
            return $date;
        } else {
            $categories = Category::where('parent_id', $id)->get();
            if ($step == 0) {
                $date = '';
                $prefix = '';
            } else {
                $date = '';
                $prefix = '';
                for ($i = 0; $i < $step; $i++) {
                    $prefix .= '　';
                }
                $prefix .= '┖';
            }
            foreach ($categories as $category) {
                $date .= "<option value='" . $category->id . "'>" . $prefix . $category->title . "</option>";
                $subs = Category::where('parent_id', $category->id)->get();
                if ($subs->count() > 0) {
                    $date .=  Theme::categoryTree($category->id, $step + 1);
                }
            }
            if ($id == 0 && $step == 0) {
                $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
                Cache::store('category')->put($key, $date, $expiresAt);
            }
            return $date;
        }
    }

    /**调用友情链接
     * @param $num 查询数量
     * @param $order 排序规则
     * @param $where 查询条件
     * 返回项目数组
     */
    public static function friend_data($num,$order = null,$where = null)
    {
        $num = intval($num);
        $key = 'friend_'.$num.'_'.$order.'_'.$where;
        if (Cache::store('friend')->has($key)) {
            $date = Cache::store('friend')->get($key);
            return $date;
        } else {
            switch ($order) {
                case byId:
                    $order_str = 'id';
                    break;
                case bySort:
                    $order_str = 'sort';
                    break;
                default:
                    $order_str = 'id';
                    break;
            }
            switch ($where) {
                case findText:
                    $date = FriendLink::where('is_open','>',0)->where('cover', '')->sortByDesc($order_str)->take($num)->get();
                    break;
                case findImg:
                    $date = FriendLink::where('is_open','>',0)->where('cover', '!=', '')->sortByDesc($order_str)->take($num)->get();
                    break;
                case findAll:
                default:
                    $date = FriendLink::where('is_open','>',0)->sortByDesc($order_str)->take($num)->get();
                    break;
            }
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('friend')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**调用菜单
     * @param $num 查询数量
     * @param $order 排序规则
     * @param $where 查询条件
     * 返回项目数组
     */
    public static function menu_data($num,$order = null,$position = null)
    {
        $num = intval($num);
        $position = intval($position);
        $key = 'menu_'.$num.'_'.$order.'_'.$position;
        if (Cache::store('menu')->has($key)) {
            $date = Cache::store('menu')->get($key);
            return $date;
        } else {
            switch ($order) {
                case byId:
                    $order_str = 'id';
                    break;
                case bySort:
                    $order_str = 'sort';
                    break;
                default:
                    $order_str = 'sort';
                    break;
            }
            if($position){
                $date = Menu::where('is_open','>',0)->where('position', $position)->sortByDesc($order_str)->take($num)->get();
            } else {
                $date = Menu::where('is_open','>',0)->sortByDesc($order_str)->take($num)->get();
            }
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('menu')->put($key, $date, $expiresAt);
            return $date;
        }
    }

    /**广告位**/
    public static function adspaceTree(){
        $key = 'adspaceTree';
        if (Cache::store('adspaces')->has($key)) {
            $date = Cache::store('adspaces')->get($key);
            return $date;
        } else {
            $adspaces = Adspace::where('is_open', '>', 0)->sortByDesc('id')->get();
            $date = '';
            foreach ($adspaces as $adspace) {
                $date .= "<option value='" . $adspace->id . "'>" . $adspace->name . "</option>";
            }
            return $date;
        }
    }

    /**获取广告位*/
    public static function adspaces($show_type = noShowHide){
        $key = 'adspaces';
        if (Cache::store('adspaces')->has($key)) {
            $date = Cache::store('adspaces')->get($key);
            return $date;
        } else {
            $date = Adspace::where('is_open','>=',$show_type )->sortByDesc('id')->get();
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('adspaces')->put($key, $date, $expiresAt);
            return $date;
        }
    }
    
    /**获取广告*/
    public static function getads($space , $num = 1){
        $key = 'adimages_' . $space . '_' . $num;
        if (Cache::store('ads')->has($key)) {
            $date = Cache::store('ads')->get($key);
            return $date;
        } else {
            $date = Adimage::where('adspace_id',$space)->where('is_open','>=', 1)->sortByDesc('id')->take($num)->get();
            $expiresAt = Carbon::now()->addMinutes(60);//设置缓存时间
            Cache::store('ads')->put($key, $date, $expiresAt);
            return $date;
        }
    }
}