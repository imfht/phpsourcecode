<?php

class SitemapController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $category = Category::all();
        $nodes = Node::all();

        $data_array = array();
        foreach ($category as $row) {
            $data_array[] = array(
                'url' => $this->siteUrl . '/category/' . $row['id'],
                'time' => $row['created_at'] ? $row['created_at'] : date("Y-m-d H:i:s", time())
            );
        }
        foreach ($nodes as $row) {
            $data_array[] = array(
                'url' => $this->siteUrl . '/node/' . $row['id'],
                'time' => $row['created_at']
            );
        }

        $style = $this->siteUrl . "/public/modules/sitemap/sitemap.xsl";
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $xml .= "<?xml-stylesheet type=\"text/xsl\" href=\"" . $style . "\"?>\n";
        $xml .= "<urlset  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd\" xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        foreach ($data_array as $data) {
            $xml .= $this->create_item($data['url'], $data['time']) . "\n";
        }

        $xml .= "</urlset>\n";

        echo $xml;
    }

    public function admin_index() {
        return View::make("sitemap::admin.index");
    }

    function create_item($url, $time) {
        $item = "<url>\n";
        $item .= "<loc>" . $url . "</loc>\n";
        $item .= "<lastmod>" . $time . "</lastmod>\n";
        $item .= "</url>\n";

        return $item;
    }

}
