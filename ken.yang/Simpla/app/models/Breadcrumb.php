<?php

/*
 * 面包屑
 */

class Breadcrumb extends Eloquent {

    /**
     * 获取面包屑
     * @return array
     */
    public static function get() {
        $breadcrumb = array();
        $segments = Request::segments();
        //如果是内容页面，将要依次显示首页-分类-内容
        if (in_array('node', $segments)) {
            $node = Node::find($segments[1]);
            $category_data = Categorydata::where('nid', '=', $node['id'])->get()->toArray();

            if (!empty($category_data)) {
                $category = Category::find($category_data[0]['cid']);

                //分类
                $breadcrumb[] = array('url' => '/category/' . $category['id'], 'title' => $category['title']);
            }

            //内容
            $breadcrumb[] = array('url' => '/node/' . $node['id'], 'title' => $node['title']);
        } elseif (in_array('category', $segments)) {
            $category = Category::find($segments[1]);
            $breadcrumb[] = array('url' => '/category/' . $category['id'], 'title' => $category['title']);
        } elseif (in_array('search', $segments)) {
            $title = $_GET['key'];
            $breadcrumb[] = array('url' => '/search?key=' . $title, 'title' => $title);
        }elseif (in_array('user', $segments)) {
            $current_user = User::find($segments[1]);
            $breadcrumb[] = array('url' => '/user/' . $segments[1], 'title' => $current_user['username']);
        } else {
            
        }

        //$filename = 'Theme::templates/breadcrumb';
        $filename = Theme::template('breadcrumb');
        return View::make($filename, array('breadcrumb' => $breadcrumb));
    }

}
