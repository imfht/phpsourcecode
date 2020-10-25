<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

use ilinei\image;

/**
 * 文章
 * @author sigmazel
 * @since v1.0.2
 */
class _article{
	/**
	 * 搜索
	 * @return string[]
	 */
	public function search(){
		global $_var;
		
		$_category = new _category();
		
		$querystring = $wheresql = '';
		$ordersql = 'ORDER BY a.PUBDATE DESC, a.ARTICLEID DESC';
		
		if($_var['gp_cid']) {
			$category = $_category->get_by_id($_var['gp_cid']);
			if($category){
				$querystring .= '&cid='.$_var['gp_cid'];
				$wheresql .= " AND c.PATH LIKE '{$category[PATH]}%'";
			}
		}
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.PUBDATE >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.PUBDATE <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.ARTICLEID, a.TITLE, a.ADDRESS, a.AUTHOR, a.KEYWORDS, a.LINK, a.SUMMARY, IFNULL(c.CNAME,'')) LIKE '%{$_var[gp_txtKeyword]}%'";
            elseif($_var['gp_sltType'] == 1) $wheresql .= " AND a.ARTICLEID LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 2) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 3) $wheresql .= " AND a.ADDRESS LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 4) $wheresql .= " AND a.AUTHOR LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 5) $wheresql .= " AND a.KEYWORDS LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 6) $wheresql .= " AND a.LINK LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 7) $wheresql .= " AND a.SUMMARY LIKE '%{$_var[gp_txtKeyword]}%'";
            elseif($_var['gp_sltType'] == 8) $wheresql .= " AND c.CNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltIsTop']) {
			$querystring .= '&sltIsTop='.$_var['gp_sltIsTop'];
			
			if($_var['gp_sltIsTop'] == 1) $wheresql .= " AND a.ISTOP  = 1";
			elseif($_var['gp_sltIsTop'] == 2) $wheresql .= " AND a.ISTOP  = 0";
		}
		
		if($_var['gp_sltIsCommend']) {
			$querystring .= '&sltIsCommend='.$_var['gp_sltIsCommend'];
			$wheresql .= " AND a.ISCOMMEND  = '{$_var[gp_sltIsCommend]}'";
		}
		
		if($_var['gp_sltIsAudit']) {
			$querystring .= '&sltIsAudit='.$_var['gp_sltIsAudit'];
			
			if($_var['gp_sltIsAudit'] == 1) $wheresql .= " AND a.ISAUDIT  = 1";
			elseif($_var['gp_sltIsAudit'] == 2) $wheresql .= " AND a.ISAUDIT  = 0";
		}
		
		if($_var['gp_sltSort']) {
			$querystring .= '&sltSort='.$_var['gp_sltSort'];
			
			if($_var['gp_sltSort'] == 1) $ordersql = " ORDER BY a.ISTOP DESC, a.ISCOMMEND DESC, a.PUBDATE DESC";
			elseif($_var['gp_sltSort'] == 2) $ordersql = " ORDER BY a.PUBDATE ASC";
			elseif($_var['gp_sltSort'] == 3) $ordersql = " ORDER BY a.PUBDATE DESC";
			elseif($_var['gp_sltSort'] == 4) $ordersql = " ORDER BY a.HITS DESC";
		}
		
		if($_var['gp_hdnSCategoryID'] && strexists($_var['gp_hdnSCategoryID'], ',')) {
			$temparr = explode(',', $_var['gp_hdnSCategoryID']);
			
			$querystring .= '&hdnSCategoryID='.$_var['gp_hdnSCategoryID'];
			$_var['gp_cid'] = $temparr[0] + 0;
		}
		
		if($_var['gp_txtSExpried']) {
			$querystring .= '&txtSExpried='.$_var['gp_txtSExpried'];
			$wheresql .= " AND a.EXPRIED <= '{$_var[gp_txtSExpried]}'";
		}
		
		if($_var['gp_txtBeginHits']) {
			$querystring .= '&txtBeginHits='.$_var['gp_txtBeginHits'];
			$wheresql .= " AND a.Hits >= '{$_var[gp_txtBeginHits]}'";
		}
		
		if($_var['gp_txtEndHits']) {
			$querystring .= '&txtEndHits='.$_var['gp_txtEndHits'];
			$wheresql .= " AND a.Hits <= '{$_var[gp_txtEndHits]}'";
		}
		
		if($_var['gp_sltHasImage']) {
			$querystring .= '&sltHasImage='.$_var['gp_sltHasImage'];
			
			if($_var['gp_sltHasImage'] == 1) $wheresql .= " AND LENGTH(a.FILE01) > 0";
			elseif($_var['gp_sltHasImage'] == 2) $wheresql .= " AND LENGTH(a.FILE01) = 0";
		}
		
		if($_var['gp_sltHasCategory']){
			$querystring .= '&sltHasCategory='.$_var['gp_sltHasCategory'];
			
			if($_var['gp_sltHasCategory'] == 1) $wheresql .= " AND c.CATEGORYID IS NOT NULL";
			elseif($_var['gp_sltHasCategory'] == 2) $wheresql .= " AND c.CATEGORYID IS NULL";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql, 'ordersql' => $ordersql);
	}
	
	/**
	 * 根据ID获取记录
	 * @param integer $id
	 * @param number $format
	 * @return $row
	 */
	public function get_by_id($id, $format = 1){
		global $db;
		
		$_category = new _category();
		
		$article = $db->fetch_first("SELECT a.*, b.CONTENT, c.CNAME, c.IDENTITY, c.COLUMNS, c.ISAUDIT AS CATEGORY_ISAUDIT FROM tbl_article a INNER JOIN tbl_article_content b ON a.ARTICLEID = b.ARTICLEID LEFT JOIN tbl_category c ON a.CATEGORYID = c.CATEGORYID WHERE a.ARTICLEID = '{$id}'");
		
		if ($article && $format){
			$article = format_row_files($article);
			if($article['TYPE'] == 2) $article = format_row_file($article, 'CONTENT');
			
			$article = $_category->format($article);

			$article['PUBDATE'] = str_replace(' 00:00:00', '', $article['PUBDATE']);
			$article['SUMMARY'] = nl2br($article['SUMMARY']);
		}
		
		return $article;
	}
	
	/**
	 * 获取数量
	 * @param string $identity
	 * @param string $wheresql
	 * @param number $expried
	 * @return number
	 */
	public function get_count($identity, $wheresql = '', $expried = 1){
		global $db;
		
		$now_date = date('Y-m-d');
		
		$wheresql .= $identity ? " AND c.IDENTITY = '{$identity}'" : '';
		if($expried) $wheresql .= " AND (a.EXPRIED IS NULL OR a.EXPRIED = '0000-00-00' OR a.EXPRIED >= '{$now_date}')";
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_article a, tbl_category c WHERE a.CATEGORYID = c.CATEGORYID AND a.ISAUDIT = 1 {$wheresql}") + 0;
	}
	
	/**
	 * 获取数量-简化
	 * @param string $wheresql
	 * @return number
	 */
	public function get_count_of_join($wheresql = ''){
	    global $db;
	
	    return $db->result_first("SELECT COUNT(1) FROM tbl_article a LEFT JOIN tbl_category c ON a.CATEGORYID = c.CATEGORYID WHERE 1 {$wheresql}") + 0;
	}
	
	/**
	 * 获取列表
	 * @param string $identity
	 * @param integer $start
	 * @param integer $perpage
	 * @param string $wheresql
	 * @param string $ordersql
	 * @param number $fetchall
	 * @param number $expried
	 * @return $rows
	 */
	public function get_list($identity, $start, $perpage, $wheresql = '', $ordersql = '', $fetchall = 0, $expried = 1){
		global $db;
		
		$_category = new _category();
		
		$now_date = date('Y-m-d');
		
		$wheresql .= $identity ? " AND c.IDENTITY = '{$identity}'" : '';	
		if($expried) $wheresql .= " AND (a.EXPRIED IS NULL OR a.EXPRIED = '0000-00-00' OR a.EXPRIED >= '{$now_date}')";
		
		!$ordersql && $ordersql = "ORDER BY a.ISTOP DESC, a.ISCOMMEND DESC, a.PUBDATE DESC, a.ARTICLEID DESC";
		
		if($fetchall){
			$temp_query = $db->query("SELECT a.*, b.CONTENT, c.CNAME, c.IDENTITY, c.COLUMNS, c.ISAUDIT AS CATEGORY_ISAUDIT FROM tbl_article a, tbl_article_content b, tbl_category c WHERE a.ARTICLEID = b.ARTICLEID AND a.CATEGORYID = c.CATEGORYID AND a.ISAUDIT = 1 {$wheresql} {$ordersql} LIMIT $start, $perpage");
		}else{
			$temp_query = $db->query("SELECT a.*, c.CNAME, c.IDENTITY, c.COLUMNS, c.ISAUDIT AS CATEGORY_ISAUDIT FROM tbl_article a, tbl_category c WHERE a.CATEGORYID = c.CATEGORYID AND a.ISAUDIT = 1 {$wheresql} {$ordersql} LIMIT $start, $perpage");
		}
		
		$rows = array();
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			if($row['TYPE'] == 2) $row = format_row_file($row, 'CONTENT');
			
			$row = $_category->format($row);
			
			$row['PUBDATE'] = str_replace(' 00:00:00', '', $row['PUBDATE']);
			$row['SUMMARY'] = nl2br($row['SUMMARY']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}

	/**
	 * 获取列表-简化
	 * @param integer $start
	 * @param integer $perpage
	 * @param string $wheresql
	 * @param string $ordersql
	 * @return $rows
	 */
	public function get_list_of_join($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$_category = new _category();
		
		!$ordersql && $ordersql = "ORDER BY a.PUBDATE DESC, a.ARTICLEID DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.*, c.CNAME, c.COLUMNS FROM tbl_article a LEFT JOIN tbl_category c ON a.CATEGORYID = c.CATEGORYID WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = $_category->format($row);
			
			if($row['MODULE'] && !strexists($row['MODULE'], 'empty')){
				$tmparr = explode('|', $row['MODULE']);
				if(count($tmparr) > 2) $row['MODULE'] = cutstr($tmparr[count($tmparr) - 1], 50);
			}
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	/**
	 * 获取第一篇
	 * @param string $identity
	 * @param string $wheresql
	 * @param string $ordersql
	 * @param number $fetchall
	 * @param number $expried
	 * @return $row
	 */
	public function get_first($identity, $wheresql = '', $ordersql = '', $fetchall = 0, $expried = 1){
		global $db;
		
		$_category = new _category();
		
		$now_date = date('Y-m-d');
		
		$wheresql .= $identity ? " AND c.IDENTITY = '{$identity}'" : '';	
		if($expried) $wheresql .= " AND (a.EXPRIED IS NULL OR a.EXPRIED = '0000-00-00' OR a.EXPRIED >= '{$now_date}')";
		
		!$ordersql && $ordersql = "ORDER BY a.ISTOP DESC, a.ISCOMMEND DESC, a.PUBDATE DESC, a.ARTICLEID DESC";
		
		if($fetchall){
			$article = $db->fetch_first("SELECT a.*, b.CONTENT, c.CNAME, c.IDENTITY, c.COLUMNS, c.ISAUDIT AS CATEGORY_ISAUDIT FROM tbl_article a, tbl_article_content b, tbl_category c WHERE a.ARTICLEID = b.ARTICLEID AND a.CATEGORYID = c.CATEGORYID AND a.ISAUDIT = 1 {$wheresql} {$ordersql} LIMIT 0, 1");
		}else{
			$article = $db->fetch_first("SELECT a.*, c.CNAME, c.IDENTITY, c.COLUMNS, c.ISAUDIT AS CATEGORY_ISAUDIT FROM tbl_article a, tbl_category c WHERE a.CATEGORYID = c.CATEGORYID AND a.ISAUDIT = 1 {$wheresql} {$ordersql} LIMIT 0, 1");
		}
		
		if($article){
			$article = format_row_files($article);
			if($article['TYPE'] == 2) $article = format_row_file($article, 'CONTENT');
			
			$article = $_category->format($article);
			
			$article['PUBDATE'] = str_replace(' 00:00:00', '', $article['PUBDATE']);
			$article['SUMMARY'] = nl2br($article['SUMMARY']);
		}
		
		return $article;
	}
	
	/**
	 * 获取上一篇
	 * @param $row $article
	 * @return $row
	 */
	public function get_prev($article){
		$articles = $this->get_list($article['IDENTITY'], 0, 1, " AND a.PUBDATE > '{$article[PUBDATE]}' AND a.ARTICLEID <> '{$article[ARTICLEID]}'", 'ORDER BY a.PUBDATE ASC, a.ARTICLEID ASC');
		
		return count($articles) > 0 ? $articles[0] : null;
	}
	
	/**
	 * 获取下一篇
	 * @param $row $article
	 * @return $row
	 */
	public function get_next($article){
		$articles = $this->get_list($article['IDENTITY'], 0, 1, " AND a.PUBDATE < '{$article[PUBDATE]}' AND a.ARTICLEID <> '{$article[ARTICLEID]}'");
		
		return count($articles) > 0 ? $articles[0] : null;
	}
	
	/**
	 * 获取文件
	 * @param $row $article
	 * @param integer $filenum
	 * @return $rows
	 */
	public function get_files($article, $filenum){
		$article_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($article['FILE'.sprintf('%02d', $i)])) $article_files[] = $article['FILE'.sprintf('%02d', $i)];
		}
	
		return $article_files;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_article', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_article', $data, "ARTICLEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_article', "ARTICLEID = '{$id}'");
		$db->delete('tbl_article_content', "ARTICLEID = '{$id}'");
	}
	
	//外部添加
	public function add($data){
		global $db;
		
		$content = $data['CONTENT'];
		unset($data['CONTENT']);
		
		$db->insert('tbl_article', $data);
		
		$articleid = $db->insert_id();
		
		$db->insert('tbl_article_content', array('ARTICLEID' => $articleid, 'CONTENT' => $content));
	}
	
	//外部修改
	public function modify($id, $data){
		global $db;
		
		$content = $data['CONTENT'];
		unset($data['CONTENT']);
		
		$db->update('tbl_article', $data, "ARTICLEID = {$id}");
		$db->update('tbl_article_content', array('CONTENT' => $content), "ARTICLEID = {$id}");
	}
	
	//提取图片
	public function pick($filenum, $content){
		$cimage = new image();
		
		$pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.bmp|\.png]))[\'|\"].*?[\/]?>/";
		preg_match_all($pattern, $content, $matchs);
		
		$filearr = array();
		$imgcount = 0;
		
		if(count($matchs[1]) == 0) {
			$pattern = "/<[img|IMG].*?src=[\\\][\'|\"](.*?(?:[\.gif|\.jpg|\.bmp|\.png]))[\\\][\'|\"].*?[\/]?>/";
			preg_match_all($pattern, $content, $matchs);
		}
		
		foreach($matchs[1] as $key => $val){
			$tempval = strtolower($val);
			if(substr($tempval, 0, 7) == 'http://') continue;
			
			if($tempval[0] != '/') $tempval = '/'.$tempval;
			if($imgcount < $filenum){
				$filepath = strpos($tempval, '/attachment/') !== false ? substr($tempval, strpos($tempval, '/attachment/') + 12) : $tempval;
				$filename = strrpos($filepath, '/') !== false ? substr($filepath, strrpos($filepath, '/') + 1) : $filepath;
				$tempimgsize = getimagesize('attachment/'.$filepath);
				$filearr['FILE'.sprintf('%02d', $imgcount + 1)] = $filepath.'|'.$filename.'|'.thumb_image($cimage, $filepath, array('ThumbType' => 2)).'|'.$tempimgsize[0].'|'.$tempimgsize[1];
				
				$imgcount++;
			}
			
			unset($tempval);
			unset($filepath);
			unset($filename);
		}
		
		return $filearr;
	}
	
	//刷新点击量
	public function flash_hits($id){
		global $db;
		
		$db->query("UPDATE tbl_article SET HITS = HITS + 1 WHERE ARTICLEID = '{$id}'");
	}
	
	//刷新下载量 
	public function flash_down($id){
		global $db;
		
		$db->query("UPDATE tbl_article SET DOWN = DOWN + 1 WHERE ARTICLEID = '{$id}'");
	}

	//格式化记录
    public function format($article){
        $article = format_row_mp4($article, 'CONTENT');
        $article = format_row_mp3($article, 'CONTENT');

        $article['_SUBTITLE'] = $article['SUBTITLE'];
        $article['_SUBTITLE'] = explode('|', $article['_SUBTITLE']);

        $article['SUBTITLE'] = '';
        foreach($article['SUBTITLE'] as $key => $value){
            if($value) $article['SUBTITLE'] .= $value.' ';
        }

        $article['_KEYWORDS'] = $article['KEYWORDS'];
        $article['_KEYWORDS'] = str_replace(array('，', '、'), ',', $article['_KEYWORDS']);
        $article['_KEYWORDS'] = explode(',', $article['_KEYWORDS']);

        $article['KEYWORDS'] = array();
        foreach($article['_KEYWORDS'] as $key => $value){
            if($value) $article['KEYWORDS'][] = $value;
        }

        return $article;
    }

	//模块
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 4){
			$module_data['identity'] = $tmparr[0];
			$module_data['lat'] = $tmparr[1];
			$module_data['lng'] = $tmparr[2];
			$module_data['zoom'] = $tmparr[3];
			$module_data['provinceid'] = $tmparr[4];
			$module_data['cityid'] = $tmparr[5];
		}else{
			$module_data['identity'] = 'map';
			$module_data['lat'] = 32.041750;
			$module_data['lng'] = 118.784158;
			$module_data['zoom'] = 12;
		}
		
		include_once view('/module/cms/view/module');
	}

	//文章多条标签
    public function block_multi($json){
	    global $_var, $dispatches;

	    $params = json_decode($json, 1);

        $identity = $params['identity'];

        if(substr($params['identity'], 0, 1) == '$'){
            if(strpos($params['identity'], '[') === false){
                $identity = $GLOBALS[substr($params['identity'], 1)];
            }else{
                $identity_var = substr($params['identity'], 1, strpos($params['identity'], '[') - 1);
                $identity_key = substr($params['identity'], strpos($params['identity'], '['));
                $identity_key = str_replace(array('[', ']'), '', $identity_key);

                $identity = $GLOBALS[$identity_var][$identity_key];
            }
        }

        $page_url = $dispatches['page'].'.html';
        if($params['param']) $page_url .= '?'.$params['param'].'='.$_var['gp_'.$params['param']];

	    if($params['pager']){
            $count = $this->get_count($identity);
            if($count){
                $perpage = $params['limit'];
                $pages = @ceil($count / $perpage);
                $_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
                $start = ($_var['page'] - 1) * $perpage;

                //只能使用$GLOBALS，以后再解决全局变量问题！
                $GLOBALS['pager'] = pager($count, $perpage, $_var['page'], $page_url, $perpage, false);

                return $this->get_list($identity, $start, $perpage);
            }
        }else{
	        return $this->get_list($identity, 0, $params['limit']);
        }
    }

    //文章搜索标签
    public function block_search($json){
        global $_var, $dispatches;

        $params = json_decode($json, 1);

        $word = $_var['gp_'.$params['param']];
        $word = strip2words($word);

        if(empty($word)) return array();

        $page_url = $dispatches['page'].'.html';
        if($params['param']) $page_url .= '?'.$params['param'].'='.$word;

        if($params['pager']){
            $count = $this->get_count('', "AND CONCAT(a.TITLE, a.SUMMARY) LIKE '%{$word}%'");
            if($count){
                $perpage = $params['limit'];
                $pages = @ceil($count / $perpage);
                $_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
                $start = ($_var['page'] - 1) * $perpage;

                //只能使用$GLOBALS，以后再解决全局变量问题！
                $GLOBALS['pager'] = pager($count, $perpage, $_var['page'], $page_url, $perpage, false);

                $articles = $this->get_list('', $start, $perpage, "AND CONCAT(a.TITLE, a.SUMMARY) LIKE '%{$word}%'");
            }
        }else{
            $articles = $this->get_list('', 0, $params['limit'], "AND CONCAT(a.TITLE, a.SUMMARY) LIKE '%{$word}%'");
        }

        foreach($articles as $key => $article){
            $articles[$key]['TITLE'] = str_replace($word, "<font color=\"red\">{$word}</font>", $article['TITLE']);
            $articles[$key]['SUMMARY'] = str_replace($word, "<font color=\"red\">{$word}</font>", $article['SUMMARY']);
        }

        return $articles;
    }

    //文章单条标签
    public function block_one($json){
        global $_var;

        $params = json_decode($json, 1);

        if(!is_ansi($params['param'])) return null;

        $id = $_var['gp_'.$params['param']] + 0;
        if($id == 0) return null;

        $article = $this->get_by_id($id);
        if(!$article) return null;

        $article = $this->format($article);
        $this->flash_hits($article['ARTICLEID']);

        if($params['other']){
            $article['PREV'] = $this->get_prev($article);
            $article['NEXT'] = $this->get_next($article);
        }

        return $article;
    }
}
?>