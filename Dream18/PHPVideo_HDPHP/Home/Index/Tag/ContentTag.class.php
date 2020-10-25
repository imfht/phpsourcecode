<?php

/**
 * 前台标签库
 * Class ContentTag
 *
 * @author hdxj <houdunwangxj@gmail.com>
 */
class ContentTag extends Tag {
	public $Tag = array(
		'channel'  => array( 'block' => 1, 'level' => 4 ),
		'arclist'  => array( 'block' => 1, 'level' => 4 ),
		'pagelist' => array( 'block' => 1, 'level' => 4 ),
		'pageshow' => array( 'block' => 0, 'level' => 4 ),
		'tag'      => array( 'block' => 1, 'level' => 4 ),
		'pagenext' => array( 'block' => 0, 'level' => 4 ),
		'location' => array( 'block' => 0, 'level' => 4 ),
		'user'     => array( 'block' => 1, 'level' => 4 ),
	);

	//栏目列表
	public function _channel( $attr, $content ) {
		//类型:top顶级 son下级 self同级 current指定栏目
		$type = isset( $attr['type'] ) ? $attr['type'] : "self";
		//显示条数
		$row = isset( $attr['row'] ) ? $attr['row'] : 10;
		//指定的栏目cid
		$cid = isset( $attr['cid'] ) ? ( $attr['cid'][0] == '$' ? $attr['cid']
			: "'{$attr['cid']}'" ) : 0;
		//当前栏目的class样式
		$class = isset( $attr['class'] ) ? $attr['class'] : '';
		$php
		       = <<<str
        <?php
        \$type=strtolower(trim('$type'));
        \$cid=$cid;
        if(empty(\$cid)){
            \$cid=Q('cid',0,'intval');
        }
        \$cid=explode(',',\$cid);
        \$db = M("category");
        \$where=array();
        if (\$type == 'top') {
            \$where['pid']=array('EQ',0);
        }else if(\$cid) {
            switch (\$type) {
                case 'current':
                    \$where['cid'] =array('IN',\$cid);
                    break;
                case "son":
                    \$where['pid'] =array('IN',\$cid);
                    break;
                case "self":
                    \$selfMap['cid']=array('IN',\$cid);
                    \$pid = \$db->where(\$selfMap)->getField('pid');
                    \$where['pid'] =array('EQ',\$pid);
                    break;
            }
        }
        \$where['cat_show']=array('EQ',1);
        \$result = \$db->where(\$where)->order("catorder ASC")->limit($row)->all();
        if(\$result){
            //当前栏目,用于改变样式
            \$currentCid = Q('cid',0,'intval');
            foreach (\$result as \$index=>\$field):
                \$field['_index']=\$index;
                \$field['_first']=\$index==0?true:false;
                \$field['_last']=\$index==(count(\$result)-1)?true:false;
                \$field['class']=\$currentCid==\$field['cid']?"$class":'';
                \$field['caturl'] = Url::category(\$field);
                if(\$field['cattype']==3){
                    \$field['catlink']='<a href="'.\$field['caturl'].'" target="_blank">'.\$field['catname'].'</a>';
                }else{
                    \$field['catlink']='<a href="'.\$field['caturl'].'">'.\$field['catname'].'</a>';
                }
                \$field['catimage']=empty(\$field['catimage'])?'':'__ROOT__'.\$field['catimage'];
            ?>
str;
		$php .= $content;
		$php
			.= '<?php endforeach;}

        ?>';

		return $php;
	}

	//文章列表
	public function _arclist( $attr, $content ) {
		$cid = isset( $attr['cid'] ) ? ( $attr['cid'][0] == '$' ? $attr['cid']
			: "'{$attr['cid']}'" ) : 0;
		$aid = isset( $attr['aid'] ) ? trim( $attr['aid'] ) : '';
		$mid = isset( $attr['mid'] ) ? trim( $attr['mid'] ) : '';
		$row = isset( $attr['row'] ) ? trim( $attr['row'] ) : 10;
		//简单长度
		$infolen = isset( $attr['infolen'] ) ? trim( $attr['infolen'] ) : 80;
		//标题长度
		$titlelen = isset( $attr['titlelen'] ) ? trim( $attr['titlelen'] ) : 80;
		//属性
		$flag = isset( $attr['flag'] ) ? trim( $attr['flag'] ) : '';
		//排序
		$order = isset( $attr['order'] ) ? strtolower( trim( $attr['order'] ) )
			: '';
		//排除属性
		$noflag = isset( $attr['noflag'] ) ? trim( $attr['noflag'] ) : '';
		//相关文章
		$relative = isset( $attr['relative'] ) ? intval( $attr['relative'] )
			: 0;
		//获取子栏目文章
		$sub_channel = isset( $attr['sub_channel'] )
			? intval( $attr['sub_channel'] ) : 0;
		$php
		             = <<<str
        <?php
            \$categoryCache=S('category');
            \$modelCache = S('model');
            \$mid='$mid';//模型mid
            \$mid = \$mid?intval(\$mid):Q('mid',1,'intval');
            \$cid =$cid;
            \$cid = \$cid?explode(',',str_replace(' ','',\$cid)):array(Q('cid',0,'intval'));
            //如果有栏目取栏目的mid为\$mid值
            if(\$cid){
                \$mid=\$categoryCache[\$cid[0]]['mid'];
            }
            \$aid='$aid';
            \$order ='$order';
            \$flag='$flag';//有此flag
            \$noflag='$noflag';//除了flag
            \$sub_channel=$sub_channel;//包含子栏目数据
            \$relative=$relative;//相关文章
            //模型
            \$table = \$modelCache[\$mid]['table_name'];
            \$db = V(\$table);
            \$db->view[\$table] = array('_type' => "INNER");
            \$db->view['category'] = array('_type' => 'INNER', '_on' => "category.cid=\$table.cid");
            \$db->view['user'] = array('_type' => 'INNER', '_on' => "user.uid=\$table.uid");
            \$db->view['model'] = array('_type' => 'INNER', '_on' => 'model.mid=category.mid');
            \$where=array();
            //---------------------------排序类型-------------------------------
            if(\$order){
                switch(\$order){
                    case 'hot':
                        //查看次数最多
                        \$order='click DESC';
                        break;
                    case 'rand':
                        //随机排序
                        \$order='rand()';
                        break;
                    case 'new':
                        //最新文章
                        \$order='aid DESC';
                        break;
                    default:
                       \$order= str_replace(array('aid','cid'), array(\$db->table.'.aid','category.cid'), \$order);
                }
            }else{
                \$order='aid DESC';
            }
            //---------------------------查询条件-------------------------------
                //相关文章
                if(\$relative){
                    //与本文相关的，按标签相关联的
                    if(\$aid=Q('aid',0,'intval')){
                        \$tid = M('content_tag')->where("mid=\$mid AND aid=\$currentAid")->getField('tid',true);
                        if(\$tid){
                            \$map=array(
                                'tid'=>array('IN',\$tid),
                                'aid'=>array('NEQ',\$aid)
                            );
                            \$aids = M('content_tag')->where(\$_tid)->group('aid')->limit($row)->getField('aid',true);
                            if(!empty(\$aids)){
                               \$where['aid']=array('IN',\$aids);
                            }
                        }
                    }
                }
                //子栏目处理
                if(!empty(\$cid)){
                    //查询条件
                    if(\$sub_channel){
                        \$category = array_keys(Data::channelList(\$categoryCache,\$cid[0]));
                        \$category[]=\$cid[0];
                        \$where[]="category.cid IN(".implode(',',\$category).")";
                    }else{
                        \$where[]="category.cid IN(".implode(',',\$cid).")";
                    }
                }
                //指定筛选属性flag='1,2,3'时,获取指定属性的文章
		        if(\$flag){
		            \$flagCache =S('flag'.\$mid);
		            \$flag = explode(',',\$flag);
		            foreach(\$flag as \$f){
		                \$f=\$flagCache[\$f-1];
		                \$where[]="find_in_set('\$f',flag)";
		            }
		        }
		        //排除flag
		        if(\$noflag){
		            \$flagCache =S('flag'.\$mid);
		            \$noflag = explode(',',\$noflag);
		            foreach(\$noflag as \$f){
		                \$f=\$flagCache[\$f-1];
		                \$where[]="!find_in_set('\$f',flag)";
		            }
		        }
                //指定文章
                if (\$aid) {
                    \$where['aid']=array('IN',\$aid);
                }
                //已经审核的文章
                \$where[]='content_status=1';
                //---------------------------------指定显示条数--------------------------------------
                \$db->limit($row);
                //-----------------------------------获取数据----------------------------------------
                \$result = \$db->order(\$order)->where(\$where)->all();
                if(\$result):
                    foreach(\$result as \$index=>\$field):
                        \$field=content_field(\$field);
                        \$field['_index']=\$index;
                        \$field['_first']=\$index==0?true:false;
                        \$field['_last']=\$index==(count(\$result)-1)?true:false;
                        \$field['title']=mb_substr(\$field['title'],0,$titlelen,'utf8');
                        \$field['title']=\$field['color']?"<span style='color:".\$field['color']."'>".\$field['title']."</span>":\$field['title'];
                        \$field['description']=mb_substr(\$field['description'],0,$infolen,'utf-8');
                         if(\$field['new_window'] || \$field['redirecturl']){
                        	\$field['link']='<a href="'.\$field['url'].'" target="_blank">'.\$field['title'].'</a>';
						}else{
							\$field['link']='<a href="'.\$field['url'].'">'.\$field['title'].'</a>';
						}
                ?>
str;
		$php .= $content;
		$php
			.= '<?php endforeach;endif;
                    unset($where);
                ?>';

		return $php;
	}

	//分页列表
	public function _pagelist( $attr, $content ) {
		$cid = isset( $attr['cid'] ) ? ( $attr['cid'][0] == '$' ? $attr['cid']
			: "'{$attr['cid']}'" ) : 0;
		$mid = isset( $attr['mid'] ) ? trim( $attr['mid'] ) : '';
		$row = isset( $attr['row'] ) ? trim( $attr['row'] ) : 10;
		//简单长度
		$infolen = isset( $attr['infolen'] ) ? intval( $attr['infolen'] ) : 80;
		//标题长度
		$titlelen = isset( $attr['titlelen'] ) ? intval( $attr['titlelen'] )
			: 80;
		//属性
		$flag = isset( $attr['flag'] ) ? trim( $attr['flag'] ) : '';
		//排序属性
		$noflag = isset( $attr['noflag'] ) ? trim( $attr['noflag'] ) : '';
		//排序
		$order = isset( $attr['order'] ) ? trim( $attr['order'] ) : '';
		//获取子栏目文章
		$sub_channel = isset( $attr['sub_channel'] )
			? intval( $attr['sub_channel'] ) : 1;
		$php
		             = <<<str
        <?php
            \$categoryCache=S('category');
            \$modelCache = S('model');
            \$mid='$mid';//模型mid
            \$mid = \$mid?intval(\$mid):Q('mid',1,'intval');
            \$cid =$cid;
            \$cid = \$cid?\$cid:Q('cid',0,'intval');
            //如果有栏目取栏目的mid为\$mid值
            if(\$cid){
                \$mid=\$categoryCache[\$cid]['mid'];
            }
            \$order ='$order';
            \$flag='$flag';
            \$noflag='$noflag';
            \$sub_channel=$sub_channel;
            //模型
            \$table = \$modelCache[\$mid]['table_name'];
            \$db = V(\$table);
            \$db->view[\$table] = array('_type' => "INNER");
            \$db->view['category'] = array('_type' => 'INNER', '_on' => "category.cid=\$table.cid");
            \$db->view['user'] = array('_type' => 'INNER', '_on' => "user.uid=\$table.uid");
            \$db->view['model'] = array('_type' => 'INNER', '_on' => 'model.mid=category.mid');
            //---------------------------排序类型-------------------------------
             if(\$order){
                switch(\$order){
                    case 'hot':
                        //查看次数最多
                        \$order='click DESC';
                        break;
                    case 'rand':
                        //随机排序
                        \$order='rand()';
                        break;
                     case 'new':
                        //最新文章
                        \$order='aid DESC';
                        break;
                    default:
                        \$order= str_replace(array('aid','cid'), array(\$db->table.'.aid','category.cid'), \$order);
                }
            }else{
                \$order='aid DESC';
            }
            //---------------------------查询条件-------------------------------
                \$where=array();
                //子栏目处理
                if(!empty(\$cid)){
                    //查询条件
                    if(\$sub_channel){
                        \$category = array_keys(Data::channelList(\$categoryCache,\$cid));
                        \$category[]=\$cid;
                        \$where[]="category.cid IN(".implode(',',\$category).")";
                    }else{
                        \$where[]="category.cid IN(".\$cid.")";
                    }
                }
                //指定筛选属性flag='1,2,3'时,获取指定属性的文章
		        if(\$flag){
		            \$flagCache =S('flag'.\$mid);
		            \$flag = explode(',',\$flag);
		            foreach(\$flag as \$f){
		                \$f=\$flagCache[\$f-1];
		                \$where[]="find_in_set('\$f',flag)";
		            }
		        }
		        //排除flag
		        if(\$noflag){
		            \$flagCache =S('flag'.\$mid);
		            \$noflag = explode(',',\$noflag);
		            foreach(\$noflag as \$f){
		                \$f=\$flagCache[\$f-1];
		                \$where[]="!find_in_set('\$f',flag)";
		            }
		        }
                //已经审核的文章
                \$where[]='content_status=1';
                //总条数
                \$count = \$db->where(\$where)->count();
                //分页设置
                if(\$cid){
                    \$category=\$categoryCache[\$cid];
                    if(\$category['cat_url_type']==2){
                        //开启伪静态模型
                        if(C('REWRITE_ENGINE')){
                            Page::\$staticUrl="m=Index&c=Category&a=index&cid=".\$category['cid']."&page={page}";
                        }
                    }else{//静态
                        \$html_path = C("HTML_PATH") ? C("HTML_PATH") . '/' : '';
                        Page::\$staticUrl='__ROOT__/'.\$html_path.
                        str_replace(array('{catdir}','{cid}'),array(\$category['catdir'],\$category['cid']),\$category['cat_html_url']);
                    }
                }else{//首页
                    Page::\$staticUrl=U('Index/Index/index',array('page'=>'{page}'));
                }
                \$page= new Page(\$count,$row);
                //-----------------------------------获取数据----------------------------------------
                \$result= \$db->where(\$where)->order(\$order)->limit(\$page->limit())->all();
                if(\$result):
                    foreach(\$result as \$index=>\$field):
                        \$field=content_field(\$field);
                        \$field['_index']=\$index;
                        \$field['_first']=\$index==0?true:false;
                        \$field['_last']=\$index==(count(\$result)-1)?true:false;
                        \$field['title']=mb_substr(\$field['title'],0,$titlelen,'utf8');
                        \$field['title']=\$field['color']?"<span style='color:".\$field['color']."'>".\$field['title']."</span>":\$field['title'];
                        \$field['description']=mb_substr(\$field['description'],0,$infolen,'utf-8');
                         if(\$field['new_window'] || \$field['redirecturl']){
                        	\$field['link']='<a href="'.\$field['url'].'" target="_blank">'.\$field['title'].'</a>';
						}else{
							\$field['link']='<a href="'.\$field['url'].'">'.\$field['title'].'</a>';
						}
                ?>
str;
		$php .= $content;
		$php .= '<?php endforeach;endif;?>';

		return $php;
	}

	//页码
	public function _pageshow( $attr, $content ) {
		$style = isset( $attr['style'] ) ? $attr['style'] : 2;
		$row   = isset( $attr['row'] ) ? intval( $attr['row'] ) : 8;

		return <<<str
        <?php if(is_object(\$page))echo \$page->show($style,$row);?>
str;
	}

	//上一篇与下一篇
	public function _pagenext( $attr, $content ) {
		return '';
		$type     = isset( $attr['type'] ) ? $attr['type'] : 'pre,next';
		$pre_str  = isset( $attr['pre'] ) ? $attr['pre'] : "上一篇: ";
		$next_str = isset( $attr['next'] ) ? $attr['next'] : "上一篇: ";
		$titlelen = isset( $attr['titlelen'] ) ? intval( $attr['titlelen'] )
			: 10;
		$php
		          = <<<str
        <?php
        \$type='$type';\$titlelen = $titlelen;
        \$mid = Q('mid',0,'intval');
        //导入模型类
        \$db =ContentViewModel::getInstance(\$mid);
        \$aid = Q('aid',NULL,'intval');
        //上一篇
        if(strstr(\$type,'pre')){
            \$content = \$db->relation(\$db->table.',category')->where("aid<\$aid")->order("aid desc")->find();
            if (\$content) {
                \$content['title']=mb_substr(\$content['title'],0,\$titlelen,'utf-8');
                \$url = Url::content(\$content);
                echo "$pre_str <a href='".\$url."'>" . \$content['title'] . "</a>";
            } else {
                echo "$pre_str <span>没有了</span></li>";
            }
        }
        //下一篇
        if(strstr(\$type,'next')){
            \$content = \$db->relation(\$db->table.',category')->where("aid>\$aid")->order("aid ASC")->find();
            if (\$content) {
                \$content['title']=mb_substr(\$content['title'],0,\$titlelen,'utf-8');
                \$url = Url::content(\$content);
                echo "$next_str <a href='".\$url."'>" . \$content['title'] . "</a>";
            } else {
                echo "$next_str <span>没有了</span>";
            }
        }
        ?>
str;

		return $php;
	}

	//显示标签云
	public function _tag( $attr, $content ) {
		$type = isset( $attr['type'] ) ? $attr['type'] : 'hot';
		$row  = isset( $attr['row'] ) ? intval( $attr['row'] ) : 10;
		$php
		      = <<<str
        <?php
        \$type= '$type';
        \$row =$row;
        \$db=M('tag');
        switch(\$type){
            case 'new':
                \$result = \$db->order('tid DESC')->limit(\$row)->all();
                break;
			case 'hot':
			default:
                \$result = \$db->order('total DESC')->limit(\$row)->all();
                break;
        }
        foreach(\$result as \$field):
            \$field['url']=U('Search/Index/index',array('g'=>'Addons','type'=>'tag','wd'=>\$field['tag']));
        ?>
str;
		$php .= $content;
		$php .= "<?php endforeach;?>";

		return $php;
	}

	//当前位置
	public function _location( $attr, $content ) {
		$sep = isset( $attr['sep'] ) ? $attr['sep'] : ' > ';
		//分隔符
		$php
			= <<<str
        <?php
        \$sep = "$sep";
        if(!empty(\$_REQUEST['cid'])){
            \$cat = S("category");
            \$cat= array_reverse(Data::parentChannel(\$cat,\$_REQUEST['cid']));
            \$str = "<a href='__ROOT__/index.php'>首页</a>{$sep}";
            foreach(\$cat as \$c){
                \$str.="<a href='".Url::category(\$c)."'>".\$c['catname']."</a>".\$sep;
            }
            echo substr(\$str,0,-(strlen(\$sep)));
        }
        ?>
str;

		return $php;
	}

	//获得用户
	public function _user( $attr, $content ) {
		$row = isset( $attr['row'] ) ? $attr['row'] : 20;
		$php
		     = <<<str
        <?php
            \$db=M('user');
            \$data = \$db->where("user_status=1")->order("logintime DESC")->limit($row)->all();
            foreach(\$data as \$field):
                \$field['url'] = U('Member/Space/index',array('uid'=>\$field['uid']));
                \$field['icon']='__ROOT__/'.\$field['icon'];
            ?>
str;
		$php .= $content;
		$php
			.= '<?php endforeach;
                    unset($data);
                ?>';

		return $php;

	}
}