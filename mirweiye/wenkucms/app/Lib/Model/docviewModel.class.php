<?php

class docviewModel extends Model{

	
	/*本文件尚未启用*/
   
    /**
     * 获取预览文件内容
     */
    public function get_doc_info($alias) {
        return file_get_contents($alias);
    }
/**
     * 写入预览文件内容
     */
    public function put_doc_info($alias) {
        return file_put_contents($alias);
    }
    
   
}