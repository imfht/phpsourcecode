<?php
// +----------------------------------------------------------------------
// | CoreThink [ Simple Efficient Excellent ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.corethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: jry <598821125@qq.com> <http://www.corethink.cn>
// +----------------------------------------------------------------------
namespace Common\Builder;
use Think\View;
use Think\Controller;
/**
 * 数据列表自动生成器
 * @author jry <598821125@qq.com>
 */
class ListBuilder extends Controller{
    private $_meta_title;                  //页面标题
    private $_top_button_list = array();   //顶部工具栏按钮组
    private $_search  = array();           //搜索参数配置
    private $_tab_nav = array();           //页面Tab导航
    private $_table_column_list = array(); //表格标题字段
    private $_table_data_list   = array(); //表格数据列表
    private $_table_data_list_key = 'id';  //表格数据列表主键字段名
    private $_table_data_page;             //表格数据分页
    private $_right_button_list = array(); //表格右侧操作按钮组
    private $_alter_data_list = array();   //表格数据列表重新修改的项目
    private $_extra_html;                  //额外功能代码
    private $_template = '_Builder/listbuilder'; //模版

    /**
     * 设置页面标题
     * @param $title 标题文本
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setMetaTitle($meta_title){
        $this->meta_title = $this->_meta_title = $meta_title;
        return $this;
    }

    /**
     * 加入一个列表顶部工具栏按钮
     * 在使用预置的几种按钮时，比如我想改变新增按钮的名称
     * 那么只需要$builder->addTopButton('add', array('title' => '换个马甲'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * @param string $type 按钮类型，主要有add/resume/forbid/recycle/restore/delete/self七几种取值
     * @param array  $attr 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function addTopButton($type, $attribute = null){
        switch($type){
            case 'addnew': //添加新增按钮
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '新增';
                $my_attribute['class'] = 'btn btn-primary';
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/add');

                /**
                * 如果定义了属性数组则与默认的进行合并
                * 用户定义的同名数组元素会覆盖默认的值
                * 比如$builder->addTopButton('add', array('title' => '换个马甲'))
                * '换个马甲'这个碧池就会使用山东龙潭寺的十二路谭腿第十一式“风摆荷叶腿”
                * 把'新增'踢走自己霸占title这个位置，其它的属性同样道理
                */
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'resume': //添加启用按钮(禁用的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '启用';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-success ajax-post confirm';
                $my_attribute['data-model'] = CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'resume', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'forbid': //添加禁用按钮(启用的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '禁用';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-warning ajax-post confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'forbid', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'recycle': //添加回收按钮(还原的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '回收';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-danger ajax-post confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'recycle', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'restore': //添加还原按钮(回收的反操作)
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '还原';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-success ajax-post confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'restore', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'delete': //添加删除按钮(我没有反操作，删除了就没有了，就真的找不回来了)
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '删除';
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-danger ajax-post confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'delete', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的新增按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
            case 'self': //添加自定义按钮(第一原则使用上面预设的按钮，如果有特殊需求不能满足则使用此自定义按钮方法)
                //预定义按钮属性以简化使用
                $my_attribute['target-form'] = 'ids';
                $my_attribute['class'] = 'btn btn-danger';

                //如果定义了属性数组则与默认的进行合并
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }else{
                    $my_attribute['title'] = '该自定义按钮未配置属性';
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_top_button_list[] = $my_attribute;
                break;
        }
        return $this;
    }

    /**
     * 设置搜索参数
     * @param $title
     * @param $url
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setSearch($title, $url){
        $this->_search = array('title' => $title, 'url' => $url);
        return $this;
    }

    /**
     * 设置Tab按钮列表
     * @param $tab_list    Tab列表  array('title' => '标题', 'href' => 'http://www.corethink.cn')
     * @param $current_tab 当前tab
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setTabNav($tab_list, $current_tab){
        $this->_tab_nav = array('tab_list' => $tab_list, 'current_tab' => $current_tab);
        return $this;
    }

    /**
     * 加一个表格标题字段
     * @author jry <598821125@qq.com>
     */
    public function addTableColumn($name, $title, $type = null){
        $column = array('name' => $name, 'title' => $title, 'type' => $type);
        $this->_table_column_list[] = $column;
        return $this;
    }

    /**
     * 表格数据列表
     * @author jry <598821125@qq.com>
     */
    public function setTableDataList($table_data_list){
        $this->_table_data_list = $table_data_list;
        return $this;
    }

    /**
     * 表格数据列表的主键名称
     * @author jry <598821125@qq.com>
     */
    public function setTableDataListKey($table_data_list_key){
        $this->_table_data_list_key = $table_data_list_key;
        return $this;
    }

    /**
     * 加入一个数据列表右侧按钮
     * 在使用预置的几种按钮时，比如我想改变编辑按钮的名称
     * 那么只需要$builder->addRightpButton('edit', array('title' => '换个马甲'))
     * 如果想改变地址甚至新增一个属性用上面类似的定义方法
     * 因为添加右侧按钮的时候你并没有办法知道数据ID，于是我们采用__data_id__作为约定的标记
     * __data_id__会在display方法里自动替换成数据的真实ID
     * @param string $type 按钮类型，edit/forbid/recycle/restore/delete/self六种取值
     * @param array  $attr 按钮属性，一个定了标题/链接/CSS类名等的属性描述数组
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function addRightButton($type, $attribute = null){
        switch($type){
            case 'edit': //编辑按钮
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '编辑';
                $my_attribute['class'] = 'label label-primary';
                $my_attribute['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/edit', array($this->_table_data_list_key => '__data_id__'));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                /**
                * 如果定义了属性数组则与默认的进行合并
                * 用户定义的同名数组元素会覆盖默认的值
                * 比如$builder->addRightButton('edit', array('title' => '换个马甲'))
                * '换个马甲'这个碧池就会使用山东龙潭寺的十二路谭腿第十一式“风摆荷叶腿”
                * 把'新增'踢走自己霸占title这个位置，其它的属性同样道理
                */
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'forbid': //改变记录状态按钮，会更具数据当前的状态自动选择应该显示启用/禁用
                //预定义按钮属
                $my_attribute['type'] = 'forbid';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['0']['title'] = '启用';
                $my_attribute['0']['class'] = 'label label-success ajax-get confirm';
                $my_attribute['0']['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'resume', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));
                $my_attribute['1']['title'] = '禁用';
                $my_attribute['1']['class'] = 'label label-warning ajax-get confirm';
                $my_attribute['1']['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'forbid', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'hide': //改变记录状态按钮，会更具数据当前的状态自动选择应该显示隐藏/显示
                //预定义按钮属
                $my_attribute['type'] = 'hide';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['2']['title'] = '显示';
                $my_attribute['2']['class'] = 'label label-success ajax-get confirm';
                $my_attribute['2']['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'show', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));
                $my_attribute['1']['title'] = '隐藏';
                $my_attribute['1']['class'] = 'label label-info ajax-get confirm';
                $my_attribute['1']['href']  = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'hide', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'recycle':
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '回收';
                $my_attribute['class'] = 'label label-danger ajax-get confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href'] = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'recycle', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'restore':
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '还原';
                $my_attribute['class'] = 'label label-success ajax-get confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href'] = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'restore', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'delete':
                //预定义按钮属性以简化使用
                $my_attribute['title'] = '删除';
                $my_attribute['class'] = 'label label-danger ajax-get confirm';
                $my_attribute['data-model'] = $attribute['model'] ? : CONTROLLER_NAME; //要操作的数据模型
                $my_attribute['href'] = U(MODULE_NAME.'/'.CONTROLLER_NAME.'/setStatus', array('status' => 'delete', 'ids' => '__data_id__', 'model' => $my_attribute['data-model']));

                //如果定义了属性数组则与默认的进行合并，详细使用方法参考上面的顶部按钮
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
            case 'self':
                //预定义按钮属性以简化使用
                $my_attribute['class'] = 'label label-default';

                //如果定义了属性数组则与默认的进行合并
                if($attribute){
                    $my_attribute = array_merge($my_attribute, $attribute);
                }else{
                    $my_attribute['title'] = '该自定义按钮未配置属性';
                }

                //这个按钮定义好了把它丢进按钮池里
                $this->_right_button_list[] = $my_attribute;
                break;
        }
        return $this;
    }

    /**
     * 设置分页
     * @param $page
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setTableDataPage($table_data_page){
        $this->_table_data_page = $table_data_page;
        return $this;
    }

    /**
     * 修改列表数据
     * 有时候列表数据需要在最终输出前做一次小的修改
     * 比如管理员列表ID为1的超级管理员右侧编辑按钮不显示删除
     * @param $page
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function alterTableData($condition, $alter_data){
        $this->_alter_data_list[] = array('condition' => $condition, 'alter_data' => $alter_data);
        return $this;
    }

    /**
     * 设置额外功能代码
     * @param $extra_html 额外功能代码
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setExtraHtml($extra_html){
        $this->_extra_html = $extra_html;
        return $this;
    }

    /**
     * 设置页面模版
     * @param $template 模版
     * @return $this
     * @author jry <598821125@qq.com>
     */
    public function setTemplate($template){
        $this->_template = $template;
        return $this;
    }

    /**
     * 显示页面
     * @author jry <598821125@qq.com>
     */
    public function display(){
        //编译data_list中的值
        foreach($this->_table_data_list as &$data){
            //编译表格右侧按钮
            if($this->_right_button_list){
                foreach($this->_right_button_list as $right_button){
                    //禁用按钮与隐藏比较特殊，它需要根据数据当前状态判断是显示禁用还是启用
                    if($right_button['type'] === 'forbid' || $right_button['type'] === 'hide'){
                        $right_button = $right_button[$data['status']];
                    }

                    //将约定的标记__data_id__替换成真实的数据ID
                    $right_button['href'] = preg_replace('/__data_id__/i', $data[$this->_table_data_list_key], $right_button['href']);

                    //编译按钮属性
                    $right_button['attribute'] = $this->compileHtmlAttr($right_button);
                    $data['right_button'] .= '<a '.$right_button['attribute'] .'>'.$right_button['title'].'</a> ';
                }
            }

            //根据表格标题字段指定类型编译列表数据
            foreach($this->_table_column_list as &$column){
                switch($column['type']){
                    case 'status':
                        switch($data[$column['name']]){
                            case '-1':
                                $data[$column['name']] = '<i class="fa fa-trash text-danger"></i>';
                                break;
                            case '0':
                                $data[$column['name']] = '<i class="fa fa-ban text-danger"></i>';
                                break;
                            case '1':
                                $data[$column['name']] = '<i class="fa fa-check text-success"></i>';
                                break;
                            case '2':
                                $data[$column['name']] = '<i class="fa fa-eye-slash text-warning"></i>';
                                break;
                        }
                        break;
                    case 'icon':
                        $data[$column['name']] = '<i class="'.$data[$column['name']].'"></i>';
                        break;
                    case 'date':
                        $data[$column['name']] = time_format($data[$column['name']], 'Y-m-d');
                        break;
                    case 'time':
                        $data[$column['name']] = time_format($data[$column['name']]);
                        break;
                    case 'picture':
                        $data[$column['name']] = '<img src="'.get_cover($data[$column['name']]).'">';
                        break;
                    case 'type':
                        $form_item_type = C('FORM_ITEM_TYPE');
                        $data[$column['name']] = $form_item_type[$data[$column['name']]][0];
                        break;
                }
            }

            /**
             * 修改列表数据
             * 有时候列表数据需要在最终输出前做一次小的修改
             * 比如管理员列表ID为1的超级管理员右侧编辑按钮不显示删除
             */
            if($this->_alter_data_list){
                foreach($this->_alter_data_list as $alter){
                    if($data[$alter['condition']['key']] === $alter['condition']['value']){
                        $data = array_merge($data, $alter['alter_data']);
                    }
                }
            }
        }

        //编译top_button_list中的HTML属性
        if($this->_top_button_list){
            foreach($this->_top_button_list as &$button){
                $button['attribute'] = $this->compileHtmlAttr($button);
            }
        }

        $this->assign('meta_title',          $this->_meta_title);          //页面标题
        $this->assign('top_button_list',     $this->_top_button_list);     //顶部工具栏按钮
        $this->assign('search',              $this->_search);              //搜索配置
        $this->assign('tab_nav',             $this->_tab_nav);             //页面Tab导航
        $this->assign('table_column_list',   $this->_table_column_list);   //表格的列
        $this->assign('table_data_list',     $this->_table_data_list);     //表格数据
        $this->assign('table_data_list_key', $this->_table_data_list_key); //表格数据主键字段名称
        $this->assign('table_data_page',     $this->_table_data_page);     //表示个数据分页
        $this->assign('right_button_list',   $this->_right_button_list);   //表格右侧操作按钮
        $this->assign('alter_data_list',     $this->_alter_data_list);     //表格数据列表重新修改的项目
        $this->assign('extra_html',          $this->_extra_html);          //额外HTML代码
        parent::display($this->_template);
    }

    //编译HTML属性
    protected function compileHtmlAttr($attr){
        $result = array();
        foreach($attr as $key => $value){
            $value = htmlspecialchars($value);
            $result[] = "$key=\"$value\"";
        }
        $result = implode(' ', $result);
        return $result;
    }
}
