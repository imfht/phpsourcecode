<?php
/**\
 * @description Movie Bot For WatchSoMuch.com
 * @author hexpang
 */

namespace App\MovieBot;

use hexpang\moviebot\IBot;


class WatchSoMuch implements IBot
{
    public $baseUrl;
    private $total;
    private $totalPage;

    public function __construct()
    {
        $this->baseUrl = 'https://watchsomuch.org/Includes/MovieData.aspx?t=1&_=' . intval(time() / 10000);
    }
    public function downloadTorrent($url, $fileName = null)
    {
        $url = "https://watchsomuch.org{$url}";
        $html = $this->loadWithCurl($url);
        libxml_use_internal_errors(true);
        $dom = new \DomDocument;
        $dom->loadHTML($html);
        $x = new \DomXPath($dom);
        $magnet = $x->query('//*[@id="txtMagnetLink"]');
        return $magnet[0]->getAttribute('value');
    }
    public function loadWithCurl($url){
        $ch = curl_init();
        $headers = ['User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36 Edg/83.0.478.54'];
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); 
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function loadUrl($url, $cache = true)
    {
        $cacheFile = 'cache/'.urlencode($url).'.cache';
        if (file_exists($cacheFile) && $cache) {
            $url = $cacheFile;
            $response = file_get_contents($cacheFile);
        } else {
            $response = $this->loadWithCurl($url);   
            if ($cache) {
                if (!file_exists('cache/')) {
                    mkdir('cache/');
                }
                $handle = fopen($cacheFile, 'w+');
                fwrite($handle, $response);
                fflush($handle);
                fclose($handle);
            }  
        }
        return $response;
    }

    private function loadDB(){
        $response = $this->loadUrl($this->baseUrl);
        $bodyStart = stripos($response, '[');
        $bodyEnd = stripos($response, '];');
        $body = substr($response, $bodyStart, $bodyEnd - $bodyStart + 1);
        $body = str_replace(", ,", ',"",', $body);
        $body = str_replace(",,", ',"",', $body);
        $body = str_replace(",]", ']', $body);
        $jsonObj = json_decode($body, true);
        return $jsonObj;
    }

    public function loadWithPage($page = 1, $type = -1)
    {
        $pageSize = 10;
        $lists = [];
        $jsonObj = $this->loadDB();
        if ($type > -1 && is_numeric($type)) {
            $jsonObj = array_filter($jsonObj, function ($v) use ($type) { return in_array($type, $v[5]); });
        }
        if (!is_numeric($type) || !is_numeric($page)) {
            $lists = array_filter($jsonObj, function ($v) use ($type) { return stripos($v[1], $type) > -1; });
        } else {
            $this->total = count($jsonObj);
            $this->totalPage = round(count($jsonObj) / $pageSize);
            if ($this->totalPage * $pageSize < $this->total) {
                $this->totalPage++;
            }
            $offset = $page * $pageSize - $pageSize;            
            for($i = $offset; $i < $offset + $pageSize; $i++) {
                $lists[] = $jsonObj[$i];
            }
        }
        return $lists;
    }
    private function getMovieItemById($movieId) {
        $db = $this->loadDB();
        $movieItem = array_filter($db, function($item) use ($movieId) { return intval($item[0]) === intval($movieId); });
        return count($movieItem) > 0 ? array_shift($movieItem) : null;
    }
    public function loadTorrentInfo($url)
    {
        $result = [];
        if(preg_match('/\d+/',$url,$nums)){
            $movieId = $nums[0];
            $item = $this->getMovieItemById($movieId);
            $torrentBase = "https://watchsomuch.org/Movies/ajMovieTorrents.aspx?mid={$movieId}";
            $tvTorrentBase = "https://watchsomuch.org/Movies/ajTVTorrents.aspx?mid={$movieId}";
            $html = $this->loadWithCurl($torrentBase);
            libxml_use_internal_errors(true);
            $dom = new \DomDocument;
            $dom->loadHTML($html);
            $x = new \DomXPath($dom);
            $torrents = $x->query('//*[@id="TorrentsList"]/a');
            $torrent = [];
            foreach($torrents as $k => $v) {
                $titlePath = $x->query('div[1]/text()', $v);
                $sizePath = $x->query('div[2]/div[1]/text()', $v);
                $torrent[] = ['file_name' => trim($titlePath[0]->nodeValue), 'file' => $v->getAttribute("href") ,'size' => trim($sizePath[1]->nodeValue)];
            }
            $result = $torrent;
        }
        return $result;
    }
    public function loadMovieInfo($id)
    {
        return $this->movieConvert($this->getMovieItemById($id));
    }
    private function movieConvert($movieItem) {
        $typeDB = json_decode('{"6":"动作","22":"成人","14":"冒险","15":"动画","11":"传记","4":"喜剧","8":"犯罪","10":"纪录片","1":"纪录片","16":"家庭","12":"幻想","27":"胶片","28":"娱乐","17":"历史","7":"恐怖","19":"音乐","20":"音乐剧","2":"魔幻","25":"新闻","26":"现实TV","5":"浪漫","9":"科幻","23":"短片","13":"运动","105":"脱口秀","3":"惊悚","106":"电视剧","21":"战争","18":"西部"}', true);
        $countryDB = json_decode('{"us":"美国","gb":"英国","ca":"加拿大","au":"澳大利亚","in":"印度","jp":"日本","fr":"法国","kr":"韩国","de":"德国","hk":"香港","th":"泰国","it":"意大利","cn":"中国","es":"西班牙","nz":"新西兰","nl":"荷兰","ie":"爱尔兰","no":"挪威","za":"南非","dk":"Denmark","be":"Belgium","se":"Sweden","mx":"Mexico","ru":"俄罗斯","pl":"波兰","ro":"罗马尼亚"}', true);
        $sourceDB = json_decode('{"4096":"Bluray","2048":"WebRip","1024":"DVDRip","512":"HDRip HC","256":"HDRip","128":"Screener","64":"TVRip","32":"Telecine","16":"Workprint","8":"Camera-Audio","4":"Telesync","2":"Camera","1":"Unknown","0":"Coming Soon"}', true);
            $padId = str_pad($movieItem[0], 9, '0', STR_PAD_LEFT);
            $videoHD = str_replace(" ", "-", $movieItem[4]);
            $videoTitle = str_replace(" ", "-", $movieItem[1]) . '-' . $movieItem[3];
            $hdType = @$sourceDB[$movieItem[16]];
            $type = $movieItem[5];
            if ($type) {
                foreach ($type as $k => $v) {
                    $type[$k] = $typeDB[$v];
                }                
            } else {
                $type = [];
            }
            $country = $movieItem[8];
            if($country) {
                foreach ($country as $k => $v) {
                    if (isset($countryDB[$v])) {
                        $country[$k] = $countryDB[$v];
                    }
                }                
            } else {
                $country = [];
            }
            //https://watchsomuch.org/Movie/4943820-HQ-WebRip/Download-1608863/Once-Is-Enough-2020-1080p-AMZN-WEBRip-DDP5-1-x264-alfaHD
            $detailUrl = "https://watchsomuch.org/Movie/{$movieItem[0]}-{$videoHD}/${videoTitle}";
            $torrentBase = "https://watchsomuch.org/Movies/ajMovieTorrents.aspx?mid={$movieItem[0]}";
            $tvTorrentBase = "https://watchsomuch.org/Movies/ajTVTorrents.aspx?mid={$movieItem[0]}";
            $movie = [
                'title' => "[{$hdType}] " . $movieItem[1],
                'url' => $detailUrl, //"https://watchsomuch.org/Movie/{$movieItem[0]}-{$videoHD}/${videoTitle}",
                'image' => "https://media.watchsomuch.com/PosterL/{$padId}_Full.jpg",
                'id' => $movieItem[0],
                'country' => join(",", $country),
                'director' => '',
                'script' => '',
                'actor' => '',
                'score' => $movieItem[10],
                'type' => join(",", $type),
                'year' => $movieItem[3]
            ];
            return $movie;
    }
    public function loadMovies($page, $type = -1)
    {
        $source = $this->loadWithPage($page, $type);
        $r_movies = [];
        foreach ($source as $key => $movieItem) {
            $r_movies[] = $this->movieConvert($movieItem);
        }
        // $movie = [
        //   'title' => $title->plaintext,
        //   'url' => $href->href,
        //   'type' => $movie_type,
        //   'country' => $movie_countries,
        //   'director' => $movie_director,
        //   'script' => $movie_script,
        //   'actor' => $movie_actor,
        //   'image' => $image->src,
        //   'id' => $id,
        //   'score' => $score,
        // ];
        return ['movies' => $r_movies, 'total_result' => $this->total, 'total_page' => $this->totalPage];
    }
}
