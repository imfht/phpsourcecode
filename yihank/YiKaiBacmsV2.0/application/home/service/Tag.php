<?php
namespace app\home\service;
use think\template\TagLib;
use think\Db;
class Tag extends TagLib
{
    /**
     * 定义标签列表
     */
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'close' => ['attr' => 'time,format', 'close' => 0], //闭合标签，默认为不闭合

        //闭合标签
        'navmenulist' => ['attr' => 'name,key,nav_id,limit,where'],//栏目列表
        'catlist' => ['attr' => 'name,key,parent_id,class_id,type,limit,where'],//栏目列表
        'contentlist' => ['attr' => 'name,key'],//内容列表
        'formlist' => ['attr' => 'name,key,table'],

        //非闭合标签
        'frag' => ['attr' => 'time,format', 'close' => 0], //碎片

        'articlelist' => ['attr' => 'cid,field,orderby,limit,pagesize,empty'],
//        'archived' => ['close' => 1]
        'fragment' => ['attr' => 'mark', 'close' => 0],
        'categorylist' => ['attr' => 'name,key,parent_id,class_id,type,limit,where'],//栏目列表

        'goodscategorylist' => ['attr' => 'name,key'],//商品分类列表
        'goodslist'         => ['attr' => 'name,key'],//商品列表
        'brandlist'         => ['attr' => 'name,key'],//品牌列表
        'sql'               => ['attr' => 'sql,name,key'],

    ];


    /**
     * 这是一个闭合标签的简单演示
     */
    public function tagClose($tag){
        $format = empty($tag['format']) ? 'Y-m-d H:i:s' : $tag['format'];
        $time = empty($tag['time']) ? time() : $tag['time'];
        $parse = '<?php ';
        $parse .= 'echo date("' . $format . '",' . $time . ');';
        $parse .= ' ?>';
        return $parse;
    }

    /**
     * 这是一个非闭合标签的简单演示
     */
    public function tagOpen($tag, $content){
        $type = empty($tag['type']) ? 0 : 1; // 这个type目的是为了区分类型，一般来源是数据库
        $name = $tag['name']; // name是必填项，这里不做判断了

        $parse = '<?php ';
        $parse .= '$test_arr=[[1,3,5,7,9],[2,4,6,8,10]];'; // 这里是模拟数据
        $parse .= '$list = think\Db::name(\'product\')->where(\'cid\',1)->select();';
        $parse .= '$__LIST__ = $list;';
        $parse .= '?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
    /**
     * cms模块
     */
    //导航列表
    public function tagnavmenulist($tag,$content){
        $name   = $tag['name'];
        /********************条件开始***************************/
        $nav_id=isset($tag['nav_id']) ? $tag['nav_id'] : '';
        $parent_id=isset($tag['parent_id']) ? $tag['parent_id'] : '';
        /********************条件结束***************************/
        $limit  = isset($tag['limit']) ? $tag['limit'] : '';
        $order    = isset($tag['order']) ? $tag['order'] : '';
        $where    = isset($tag['where']) ? $tag['where'] : '';
        $key    = isset($tag['key']) ? $tag['key'] : '';
        $tag_str='';
        $tag_str .=!empty($nav_id)?'nav_id:'.$nav_id:'';
        $tag_str .=!empty($parent_id)?';parent_id:'.$parent_id:'';
        $tag_str .=!empty($limit)?';limit:'.$limit:'';
        $tag_str .=!empty($order)?';order:'.$order:'';
        $tag_str .=!empty($where)?';where:'.$where:'';
        if(substr($tag_str, 0, 1)==';') $tag_str=substr($tag_str,1);
        $parseStr = '<?php ';
        $parseStr .='$__LIST__=get_nav_menu('.'"'.$tag_str.'");';
        $parseStr .="?>";
        $parseStr .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }
    //栏目列表
    public function tagcatlist($tag,$content){
        $name   = $tag['name'];
        /********************条件开始***************************/
        $class_id=isset($tag['class_id']) ? $tag['class_id'] : '';
        $class_ids=isset($tag['class_ids']) ? $tag['class_ids'] : '';
        $parent_id=isset($tag['parent_id']) ? $tag['parent_id'] : '';
        $type=isset($tag['type']) ? $tag['type'] : '';
        /********************条件结束***************************/
        $limit  = isset($tag['limit']) ? $tag['limit'] : '';
        $order    = isset($tag['order']) ? $tag['order'] : '';
        $where    = isset($tag['where']) ? $tag['where'] : '';
        $key    = isset($tag['key']) ? $tag['key'] : '';
        $tag_str='';
        $tag_str .=!empty($class_id)?'class_id:'.$class_id:'';
        $tag_str .=!empty($class_ids)?'class_ids:'.$class_ids:'';
        $tag_str .=!empty($parent_id)?';parent_id:'.$parent_id:'';
        $tag_str .=!empty($type)?';type:'.$type:'';
        $tag_str .=!empty($limit)?';limit:'.$limit:'';
        $tag_str .=!empty($order)?';order:'.$order:'';
        $tag_str .=!empty($where)?';where:'.$where:'';
        if(substr($tag_str, 0, 1)==';') $tag_str=substr($tag_str,1);
        $parseStr = '<?php ';
        $parseStr .='$__LIST__=get_cat('.'"'.$tag_str.'");';
        $parseStr .="?>";
        $parseStr .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }
    //文章列表
    public function tagcontentlist($tag,$content){
        $name   = $tag['name'];
        $class_id=isset($tag['class_id']) ? $tag['class_id'] : '';
        $pos_id=isset($tag['pos_id']) ? $tag['pos_id'] : '';
        $limit  = isset($tag['limit']) ? $tag['limit'] : '';
        $order    = isset($tag['order']) ? $tag['order'] : '';
        $where    = isset($tag['where']) ? $tag['where'] : '';
        $key    = isset($tag['key']) ? $tag['key'] : '';
        $tag_str='';
        $tag_str .=!empty($class_id)?'class_id:'.$class_id:'';
        $tag_str .=!empty($pos_id)?'pos_id:'.$pos_id:'';
        $tag_str .=!empty($limit)?';limit:'.$limit:'';
        $tag_str .=!empty($order)?';order:'.$order:'';
        $tag_str .=!empty($where)?';where:'.$where:'';
        if(substr($tag_str, 0, 1)==';') $tag_str=substr($tag_str,1);
        $parseStr = '<?php ';
        $parseStr .='$__LIST__=get_content('.'"'.$tag_str.'");';
        $parseStr .="?>";
        $parseStr .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }
    //表单列表
    public function tagformlist($tag,$content){
        $name   = $tag['name'];
        if(empty($tag['table'])){
            return array();
        }
        $where['table'] = $tag['table'];
        $form_info = model('FieldsetForm')->getWhereInfo($where);
        if(empty($form_info)){
            return array();
        }
        $table = isset($tag['table']) ? $tag['table'] : '';
        $fieldset_id = isset($form_info['fieldset_id']) ? $form_info['fieldset_id'] : '';
        $limit  = isset($tag['limit']) ? $tag['limit'] : '';
        $order    = isset($tag['order']) ? $tag['order'] : '';
        $where    = isset($tag['where']) ? $tag['where'] : '';
        $key    = isset($tag['key']) ? $tag['key'] : '';
        $tag_str='';
        $tag_str .=!empty($table)?';table:'.$table:'';
        $tag_str .=!empty($fieldset_id)?';fieldset_id:'.$fieldset_id:'';
        $tag_str .=!empty($limit)?';limit:'.$limit:'';
        $tag_str .=!empty($order)?';order:'.$order:'';
        $tag_str .=!empty($where)?';where:'.$where:'';
        if(substr($tag_str, 0, 1)==';') $tag_str=substr($tag_str,1);
        $parseStr = '<?php ';
        $parseStr .='$__LIST__=get_formlist('.'"'.$tag_str.'");';
        $parseStr .="?>";
        $parseStr .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parseStr .= $content;
        $parseStr .= '{/volist}';
        return $parseStr;
    }
    //碎片调取
    public function tagfrag($tag){
        if(empty($tag['mark'])){
            return ;
        }
        $mark=$tag['mark'];
        $parse = '<?php ';
        $parse .= 'echo get_flag("' . $mark . '");';
        $parse .= ' ?>';
        return $parse;
    }

    /*************************上面的新的*********************************/
    //栏目列表
    public function tagcategorylist($tag,$content){
        $name = $tag['name']; // name是必填项
        if (empty($name)){
            echo "name 不能为空";
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //上级栏目
        $parent_id = isset($tag['parent_id'])?$tag['parent_id']:0;
        //指定栏目
        $class_id = isset($tag['class_id'])?$tag['class_id']:0;
        //栏目属性
        if (isset($tag['type'])){
            $type=$tag['type'];
        }else{
            $type=3;
        }
        $limit=isset($tag['limit'])?$tag['limit']:10;
        //其他条件
        $where_other = isset($tag['where'])?$tag['where']:0;
        $parse = '<?php ';
        $parse .= '$__WHERE__ = categoryMap('.$parent_id.','.$class_id.','.$type.','.$where_other.');';

        $parse .= '$__LIST__ = model(\'kbcms/Category\')->loadData($__WHERE__,'. $limit .');';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
    /**
     * 文章模块
     */
    public function tagarticlelist($tag, $content){
        $name=$tag['name'];
        if(empty($name)){
            return ;
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //条件
        $class_id = isset($tag['class_id'])?$tag['class_id']:0;//父级分类id
        //排序
        $order = isset($tag['order'])?$tag['order']:'A.time DESC,A.content_id DESC';
        //显示数量
        $limit = isset($tag['limit'])?$tag['limit']:0;
        //获取列表信息
        $parse = <<<EOF
        <?php
                \$list=think\Db::name('content')
                            ->alias('A')
                            ->field('A.*,B.*,C.name as class_name,C.app,C.urlname as class_urlname,C.image as class_image,C.parent_id')
                            ->join('content_article B','A.content_id = B.content_id')
                            ->join('category C','A.class_id = C.class_id');
                if(\$class_id){
                    \$list=\$list->where('A.class_id',$class_id);
                }
                \$list=\$list->order('$order')
                            ->limit('$limit')
                            ->select();
                \$__LIST__ = \$list;
                    foreach(\$__LIST__ as $$key => $$name):
                ?>
EOF;
        $parse .= $content;
        $parse .= '<?php endforeach;?>';
        return $parse;
    }
    /**
     * 表单列表调用
     */
    public function tagformlist11($tag, $content){
        $name=$tag['name'];
        if(empty($name)){
            return ;
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        $limit = isset($tag['limit'])?$tag['limit']:'10';
        $order = isset($tag['order'])?$tag['order']:'data_id DESC';
        $table=$tag['table'];
        if(empty($table)){
            return ;
        }
        //获取表单信息
        $where = array();
        $where['table'] = $tag['table'];
        $formInfo = model('kbcms/FieldsetForm')->getWhereInfo($where);
        if(empty($formInfo)){
            return ;
        }
        $formtable=$formInfo['table'];
        $formfieldset_id=$formInfo['fieldset_id'];
        $list_where=0;
        if(!empty($formInfo['list_where'])){
            $list_where = $formInfo['list_where'];
        }
        $pre=config('database.prefix');
        $parse = <<<EOF
        <?php
                //设置模型
                \$model = model('kbcms/FieldData');
                \$model->setTable('$pre'.'ext_$formtable');
                //获取where
                \$__WHERE__ = fielddataMap($list_where);
                //获取条件
                \$list=\$model->loadList(\$__WHERE__,$limit,'$order');
                \$__LIST__ = \$list;
                //字段列表
                \$__WHERE__ = formfieldsetMap($formfieldset_id);
                \$__FIELDLIST__ = model('kbcms/FieldForm')->loadList(\$__WHERE__);
                \$__DATA__ = array();
                if(!empty(\$__LIST__)){
                    foreach(\$__LIST__ as \$key => \$val){
                        \$__DATA__[\$key]=\$val;
                        foreach (\$__FIELDLIST__ as \$v) {
                            \$__DATA__[\$key][\$v['field']] = model('kbcms/FieldData')->revertField(\$val[\$v['field']],\$v['type'],\$v['config']);
                        }
                        \$__DATA__[\$key]['furl'] = url('kbcms/Form/info',array('id'=>\$val['data_id']));
                        \$__DATA__[\$key]['i'] = \$key;
                    }
                }
                    foreach(\$__DATA__ as $$key => $$name):
                ?>
EOF;
        $parse .= $content;
        $parse .= '<?php endforeach;?>';
        return $parse;
    }
    /**
     * 碎片调用
     */
    public function tagfragment($tag){
        if (empty($tag['mark'])){
            return ;
        }
        $label = isset($tag['mark'])?$tag['mark']:0;
        $where['label']=$label;
        $content=model('kbcms/fragment')->getWhereInfo($where)['content'];
        $content=htmlspecialchars_decode(html_out($content));
        $parse = '<?php ';
        $parse .= "echo '$content';";
        $parse .= ' ?>';
        return $parse;
    }
    /**
     * shop模块
     */
    /**
     * 商品分类列表
     */
    public function taggoodscategorylist($tag, $content){
        $name=$tag['name'];
        if(empty($name)){
            return ;
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //条件
        $parent_id = isset($tag['parent_id'])?$tag['parent_id']:0;//父级分类id
        //排序
        $order = isset($tag['order'])?$tag['order']:'sort_order ASC,id DESC';
        //显示数量
        $limit = isset($tag['limit'])?$tag['limit']:0;
        //获取列表信息
        $parse = <<<EOF
        <?php
                \$list=think\Db::name('goods_category')
                            ->where('parent_id',$parent_id)
                            ->order('$order')
                            ->limit('$limit')
                            ->select();
                
                \$__LIST__ = \$list;
                    foreach(\$__LIST__ as $$key => $$name):
                ?>
EOF;
        $parse .= $content;
        $parse .= '<?php endforeach;?>';
        return $parse;
    }
    /**
     * 商品列表
     */
    public function taggoodslist($tag, $content){
        $name=$tag['name'];
        if(empty($name)){
            return ;
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //条件
        $is_new = isset($tag['is_new'])?$tag['is_new']:0;//新品
        $is_hot = isset($tag['is_hot'])?$tag['is_hot']:0;//热卖
        $is_recommend = isset($tag['is_recommend'])?$tag['is_recommend']:0;//推荐
        //排序
        $order = isset($tag['order'])?$tag['order']:'g.on_time DESC';
        //显示数量
        $limit = isset($tag['limit'])?$tag['limit']:0;
        //获取列表信息
        $parse = <<<EOF
        <?php
                \$__WHERE__ = goodslistMap($is_new,$is_hot,$is_recommend);
                \$list=think\Db::name('goods')
                            ->alias('g')
                            ->field('g.goods_id,g.goods_name,g.shop_price,g.original_img,g.brand_id,b.name brand_name,g.cat_id,gc.name cat_name')
                            ->join('brand b','g.brand_id=b.id','left')
                            ->join('goods_category gc','g.cat_id=gc.id','left')
                            ->where(\$__WHERE__)
                            ->order('$order');
                //获取条件
                if($limit>0){
                    \$list=\$list->paginate('$limit');
                    \$page = \$list->render();
                }else{
                    \$list=\$list->limit('$limit')->select();
                }
                
                \$__LIST__ = \$list;
                    foreach(\$__LIST__ as $$key => $$name):
                ?>
EOF;
        $parse .= $content;
        $parse .= '<?php endforeach;?>';
        return $parse;
    }

    /**
     * 品牌列表调用
     */
    public function tagbrandlist($tag, $content){
        $name=$tag['name'];
        if(empty($name)){
            return ;
        }
        $key = isset($tag['key'])?$tag['key']:'key';
        //条件
        $is_hot = isset($tag['is_hot'])?$tag['is_hot']:0;//热卖
        //排序
        $order = isset($tag['order'])?$tag['order']:'sort ASC,id DESC';
        //显示数量
        $limit = isset($tag['limit'])?$tag['limit']:0;//热卖
        //获取表单信息
        $parse = <<<EOF
        <?php
                \$__WHERE__ = brandlistMap($is_hot);
                \$list=think\Db::name('brand')
                        ->field('id brand_id,name brand_name')
                        ->where(\$__WHERE__)
                        ->order('$order');
                //获取条件
                if($limit>0){
                    \$list=\$list->paginate('$limit');
                    \$page = \$list->render();
                }else{
                    \$list=\$list->limit('$limit')->select();
                }
                \$__LIST__ = \$list;
                    foreach(\$__LIST__ as $$key => $$name):
                ?>
EOF;
        $parse .= $content;
        $parse .= '<?php endforeach;?>';
        return $parse;
    }



}