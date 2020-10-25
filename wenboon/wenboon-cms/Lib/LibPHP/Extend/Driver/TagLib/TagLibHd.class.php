<?php
    class TagLibHd extends TagLib{
    
        protected $tags   =  array(
            'loop'    => array('attr'=>'table,id,pid,cid,uid,orderby,limit,as,field,show,module,page','level'=>5),
            );
        public function _loop($attr,$content) {
            $attr=$this->parseXmlAttr($attr);
            switch ($attr['table']) {
                case 'category':
                    if(empty($attr['id'])) $attr['id']=0;
                    if(empty($attr['pid'])) $attr['pid']=0;
                    if(empty($attr['limit'])) $attr['limit']=1000;
                    if(empty($attr['show'])) $attr['show']=0;
                    if(empty($attr['as'])) $attr['as']='$v';
                    if(empty($attr['orderby'])) $attr['orderby']='asc';
                    $str=<<<str
<?php
                    if({$attr['id']})
                        \$cate_c_s=M('category')->where(array('id'=>{$attr['id']}))->select();
                    else{
                        \$cate_c_s=M('category')->where(array('show'=>{$attr['show']},'pid'=>{$attr['pid']}))->order('orders {$attr['orderby']}')->limit({$attr['limit']})->select();
                        /*
                        if(\$cate_c_s==null){
                            \$body_ss=M('category')->where(array('id'=>{$attr['pid']}))->find();
                            \$cate_c_s=M('category')->where(array('show'=>{$attr['show']},'pid'=>\$body_ss['pid']))->order('orders {$attr['orderby']}')->limit({$attr['limit']})->select();
                        }*/
                    }
                    
                    \$cate_c_s=formatCat(\$cate_c_s);
                    foreach(\$cate_c_s as {$attr['as']}):
?>
str;
                    $str .= $content;
                    $str .= '<?php endforeach;?>';
                    return $str;
                    break;
                case 'attachment':
                    if(empty($attr['id'])) $attr['id']=0;
                    if(empty($attr['pid'])) $attr['pid']=0;
                    if(empty($attr['limit'])) $attr['limit']=1000;
                    if(empty($attr['as'])) $attr['as']='$v';
                    if(empty($attr['orderby'])) $attr['orderby']='cdate desc';
                    if(empty($attr['page'])) $attr['page']='$att_page';
                    
                    $str=<<<str
<?php
                    import('ORG.Util.Page');
                    \$where_atta=array();
                    if({$attr["id"]}){
                        \$where_atta['id']={$attr['id']};
                    }
                    else{
                        \$where_atta['tag']={$attr['module']};
                        \$where_atta['pid']={$attr['pid']};
                    }
                    
                    \$count_atta= M('attachment')->where(\$where_atta)->count();
                    \$Pages_atta= new Page(\$count_atta,{$attr['limit']});
                    \$limit_atta=\$Pages_atta->firstRow .',' .\$Pages_atta->listRows;
                    
                    \$attachment=M('attachment')->where(\$where_atta)->order('{$attr['orderby']}')->limit(\$limit_atta)->select();
                    {$attr['page']}=\$Pages_atta->show();
                    foreach(\$attachment as {$attr['as']}): 
?>
str;
                    $str .= $content;
                    $str .= '<?php endforeach;?>';
                    return $str;
                    break;
                case 'discuss':
                    if(empty($attr['id'])) $attr['id']=0;
                    if(empty($attr['uid'])) $attr['uid']=0;
                    if(empty($attr['pid'])) $attr['pid']=0;
                    if(empty($attr['limit'])) $attr['limit']=1000;
                    if(empty($attr['as'])) $attr['as']='$v';
                    if(empty($attr['orderby'])) $attr['orderby']='cdate desc';
                    if(empty($attr['page'])) $attr['page']='$att_page';
                    $str=<<<str
<?php
                    import('ORG.Util.Page');
                    \$where_discuss=array();
                    if({$attr["id"]}){
                        \$where_discuss['id']={$attr['id']};
                    }
                    else if({$attr["uid"]}){
                        \$where_discuss['user']={$attr['uid']};
                    }
                    else
                    {
                        \$where_discuss['tag']={$attr['module']};
                        \$where_discuss['pid']={$attr['pid']};
                    }
                    \$count_discuss= M('discuss')->where(\$where_discuss)->count();
                    \$Pages_discuss= new Page(\$count_discuss,{$attr['limit']});
                    \$limit_discuss=\$Pages_discuss->firstRow .',' .\$Pages_discuss->listRows;
                    
                    \$discuss=D('Discuss')->relation(true)->where(\$where_discuss)->order('{$attr['orderby']}')->limit(\$limit_discuss)->select();
                    {$attr['page']}=\$Pages_discuss->show();
                    foreach(\$discuss as {$attr['as']}): 
?>
str;
                    $str .= $content;
                    $str .= '<?php endforeach;?>';
                    return $str;
                    break;
                case 'advert':
                    if(empty($attr['limit'])) $attr['limit']=1000;
                    if(empty($attr['as'])) $attr['as']='$v';
                    if(empty($attr['orderby'])) $attr['orderby']='asc';

                    $str=<<<str
<?php
                    \$advert_s=M('advertext')->where(array('pid'=>{$attr['id']},'ls'=>1))->order('orders {$attr['orderby']}')->limit({$attr['limit']})->select();
                    foreach(\$advert_s as {$attr['as']}): 
?>
str;
                    $str .= $content;
                    $str .= '<?php endforeach;?>';
                    return $str;
                    break;
                default:
                    if(empty($attr['id'])) $attr['id']=0;
                    if(empty($attr['cid'])) $attr['cid']=0;
                    if(empty($attr['orderby'])) $attr['orderby']='cdate desc';
                    if(empty($attr['limit'])) $attr['limit']=50;
                    if(empty($attr['as'])) $attr['as']='$v';
                    if(empty($attr['page'])) $attr['page']='$page';
                    if(empty($attr['field'])) $attr['field']='0';
                    if(empty($attr['user'])) $attr['user']=0;
                    
                    $str=<<<str
<?php
                    import('ORG.Util.Page'); 
                    \$where_c=array();
                    \$where_c=null;
                     if({$attr['id']}!=0){
                        \$where_c['id']={$attr['id']};
                     }
                     else{
                            if({$attr['cid']}!=0)
                            {
                                \$Cat_c_s=M('category')->order('orders asc')->where(array('show'=>0))->select();
                                \$Cat_c_s=formatCat(\$Cat_c_s);
                                \$data_a=getchidsid(\$Cat_c_s,{$attr['cid']});
                                array_push(\$data_a,{$attr['cid']});
                                \$where_c['cid']=array('in',\$data_a);
                            }
                            if("{$attr['field']}"!="0")
                            {
                                if(strpos("{$attr['field']}",'like')!=false){
                                     \$match_m = array(); 
                                    preg_match_all('/\|(.*?)\|/', "{$attr['field']}", \$match_m);
                                    if(\$match_m[0][0]){
                                        \$_string_tem=str_ireplace(\$match_m[0][0],\$\$match_m[1][0],"{$attr['field']}");
                                    }
                                    else{
                                        \$_string_tem="{$attr['field']}";
                                    }
                                    
                                    \$_string_tem=strtoupper(\$_string_tem);
                                    \$_string_array=explode('LIKE',\$_string_tem);
                                    \$where_c['_string']=\$_string_array[0]." LIKE '%".ltrim(\$_string_array[1])."%'";
                                }
                                else{
                                    \$match_m = array(); 
                                    preg_match_all('/\|(.*?)\|/', "{$attr['field']}", \$match_m);
                                    
                                    if(\$match_m[0][0]){
                                        \$_string=str_ireplace(\$match_m[0][0],\$\$match_m[1][0],"{$attr['field']}");
                                    }
                                    else{
                                        \$_string="{$attr['field']}";
                                    }
                                    \$_string=str_ireplace(' eq '," = '",\$_string);
                                    \$_string=str_ireplace(' neq '," <> '",\$_string);
                                    \$_string=str_ireplace(' gt '," > '",\$_string);
                                    \$_string=str_ireplace(' egt '," >= '",\$_string);
                                    \$_string=str_ireplace(' lt '," < '",\$_string);
                                    \$_string=str_ireplace(' elt '," <= '",\$_string);
                                
                                    \$where_c['_string']=\$_string."'";
                                }
                            }
                     }
                    if({$attr['user']}!=0){
                        \$where_c['user']={$attr['user']};
                    }
                    if(\$where_c==null){
                        \$where_c[id]=array('neq',100000);
                    }
                    
                    \$count= D('Content')->table(C('DB_PREFIX')."{$attr['table']}")->relation(true)->where(\$where_c)->count();
                    \$Pages= new Page(\$count,{$attr['limit']});
                    \$limits=\$Pages->firstRow .',' .\$Pages->listRows;
                    
                    \$content_s=D('Content')->table(C('DB_PREFIX')."{$attr['table']}")->relation(true)->where(\$where_c)->order('{$attr['orderby']}')->limit(\$limits)->select();
                    
                    \$content_s=formatCon(\$content_s);
                    {$attr['page']}=\$Pages->show();
                    
                    foreach(\$content_s as {$attr['as']}): 
?>
str;
                    $str .= $content;
                    $str .= '<?php endforeach;?>';
                    
                    //清空变量
                    $attr['table']=null;
                    $attr['id']=null;
                    $attr['pid']=null;
                    $attr['cid']=null;
                    $attr['orderby']=null;
                    $attr['limit']=null;
                    $attr['as']=null;
                    $attr['field']=null;
                    $attr['show']=null;
                    $attr['module']=null;
                    $attr['page']=null;
                    
                    return $str;
            }        
        }
            
    }
?>