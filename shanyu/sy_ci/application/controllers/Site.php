<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class Site extends CI_Controller {

	public function rss(){
		$feed = new Feed();

		$seo=config_item('seo');
		$url=config_item('base_url');
		$host=parse_url($url)['host'];
		$category_names=array_flip(config_item('category_names'));

		$this->load->database();
		if($this->db->count_all('article')){
	        $sql_article="SELECT id,cid,title,description,add_time,edit_time FROM {$this->db->dbprefix('article')} WHERE 1=1 ORDER BY id DESC LIMIT 10";
	        $query=$this->db->query($sql_article);
	        $first_row = $query->first_row();

			$channel = new Channel();
			$channel
			    ->title($seo['title'])
			    ->description($seo['description'])
			    ->url($url)
			    ->copyright("Copyright 2015-2016, {$host}")
			    ->pubDate($first_row->add_time)
			    ->lastBuildDate($first_row->edit_time)
			    ->ttl(60)
			    ->appendTo($feed);

			foreach ($query->result() as $row)
			{
				$item = new Item();
				$item
				    ->title($row->title)
				    ->description($row->description)
				    ->url("{$url}article/{$category_names[$row->cid]}/{$row->id}.html")
				    ->pubDate($row->add_time)
				    ->appendTo($channel);
			}
        }

		echo $feed->render();
	}

	public function test(){
		echo 'You know nothing';
	}

}
