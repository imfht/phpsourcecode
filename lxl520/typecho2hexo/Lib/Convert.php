<?php
namespace Mohuishou\Lib;

class Convert{
    /**
     * config对象
     * @var Config
     */
    protected $_config;

    /**
     * Attachment对象
     * @var Attachment
     */
    protected $_attachment;

    /**
     * Convert constructor.
     */
    public function __construct()
    {
        $this->_config=Config::getInstance();

        //链接数据库取出数据
        $this->getData();
    }

    /**
     * 从数据库获取数据并且做出相关处理
     */
    protected function getData(){
        //从数据库取出数据
        $db=$this->_config->get("db");
        try {
            $dhn = new \PDO($db['dsn'], $db["user"], $db["password"]);
            $prefix = $db["prefix"];
            $sql = <<<TEXT
select title,text,created,category,tags from {$prefix}_contents c,
 (select cid,group_concat(m.name) tags from {$prefix}_metas m,{$prefix}_relationships r where m.mid=r.mid and m.type='tag' group by cid ) t1,
(select cid,m.name category from {$prefix}_metas m,{$prefix}_relationships r where m.mid=r.mid and m.type='category') t2
where t1.cid=t2.cid and c.cid=t1.cid
TEXT;
            $result = $dhn->query($sql);
            $rows = $result->fetchAll(\PDO::FETCH_BOTH);
        }catch (\PDOException $e){
            die($e->errorInfo);
        }
        //初始化ATTACHMENT对象
        $is_download=$this->_config->get("attachment")["is_download"];
        $is_download && $this->_attachment=new Attachment();

        foreach ($rows as $row){
            //字符编码转换
            if($this->_config->get("is_gbk")){
                $row=$this->gbk2uft8($row);
            }

            $created_time=date('Y-m-d H:i:s',$row["created"]);
            $content=str_replace('<!--markdown-->','',$row["text"]);
            $filename=str_replace(array(" ","?","\\","/" ,":" ,"|", "*" ),'-',$row["title"]);

            //附件下载
            if($is_download){
                $content=$this->attachment($filename,$content);
            }
            $temp=<<<EOT
title: {$row["title"]}
categories: {$row["category"]}
tags: [{$row["tags"]}]
date: {$created_time}
---

{$content}
EOT;
            //保存文档
            file_put_contents(__DIR__."/../FILE/".$filename.".md",$temp);
        }
    }

    /**
     * 字符编码转换
     * @param $r
     * @return mixed
     */
    protected function gbk2uft8($r){
        foreach ($r as $key => &$value) {
            $value=iconv("GB2312","UTF-8//IGNORE",$value);
        }
        return $r;
    }

    /**
     * 附件转换
     * @param $filename
     * @param $content
     * @return mixed
     */
    protected function attachment($filename,$content){
        return $this->_attachment->save($filename,$content);
    }




}