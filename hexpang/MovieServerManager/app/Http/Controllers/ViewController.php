<?php

namespace App\Http\Controllers;

use Request;
use Cache;
use Storage;
use App\MovieBot\WatchSoMuch;
use hexpang\Client\SSHClient\SSHClient;

class ViewController extends Controller
{
    public $storage;
    public function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

        return round(pow(1024, $base - floor($base)), $precision).' '.$suffixes[floor($base)];
    }
    public function __construct()
    {
        $this->storage = Storage::disk('local');
    }
    private function loadMovieInfo($id)
    {
        $fileName = 'movie/'.$id.'.json';
        $info = null;
        $bot = new WatchSoMuch();

        if ($this->storage->exists($fileName)) {
            $json = $this->storage->get($fileName);
            $info = json_decode($json, true);
        } else {
            $info = $bot->loadMovieInfo($id);
            $this->storage->put($fileName, json_encode($info));
        }

        // && !isset($info['torrent'])
        if ($info) {
            $torrent = $bot->loadTorrentInfo($info['url']);
            $info['torrent'] = $torrent;
            $this->storage->put($fileName, json_encode($info));
        }

        return $info;
    }
    private function systemAction($action, $param = null, $param1 = null)
    {
        $ssh = new SSHClient(env('SERVER_HOST'), env('SERVER_SSH_PORT', 22), env('SERVER_USERNAME'), env('SERVER_PASSWORD'));

        if ($action == 'status') {
            if ($ssh->connect() && $ssh->authorize()) {
                $diskUses_cmd = 'df -h | grep /dev/sda';
                $disk = $ssh->cmd($diskUses_cmd);
                if ($disk) {
                    while (stripos($disk, '  ')) {
                        $disk = str_ireplace('  ', ' ', $disk);
                    }
                    $d = explode("\n", $disk);
                    $disk = [];
                    foreach ($d as $dd) {
                        if ($dd != '') {
                            $disk[] = explode(' ', $dd);
                        }
                    }
                    // dd($disk);
                }
                $checks = ['minidlna', 'smbd', 'aria2c'];
                if ($param && $param1) {
                    // dd($param);
                    //服务管理
                    $cmd = '';
                    if ($param == 'aria2c') {
                        if ($param1 == 'start') {
                            $cmd = 'aria2c -D --conf-path=/home/osmc/.aria2/aria2.conf';
                        } else {
                            $cmd = "ps -A |grep 'aria2c'| awk '{print $1}'";
                            $pid = $ssh->cmd($cmd);
                            $cmd = "kill {$pid}";
                            // $r = $ssh->cmd($cmd);
                        }
                    } else {
                        $cmd = 'sudo /etc/init.d/'.$param.' '.$param1;
                    }
                    $s = $ssh->cmd($cmd);
                    sleep(1);
                }
                $result = [];
                foreach ($checks as $check) {
                    $r = $ssh->cmd('ps -aux | grep '.$check);
                    $check_str = '/'.$check;
                    if ($check == 'aria2c') {
                        $check_str = 'conf-path';
                    }
                    if (stripos($r, $check_str) === false) {
                        $result[$check] = false;
                    } else {
                        $result[$check] = true;
                    }
                }
                $ssh->disconnect();

                return ['service' => $result, 'disks' => $disk];
            } else {
                return;
            }
        }
    }
    private function treeFiles($path)
    {
        $dir = dir($path);
        $files = [];
        $filter = ['mkv', 'mp4', 'avi'];
        while ($file = $dir->read()) {
            if ((is_dir("$path/$file")) and ($file != '.') and ($file != '..')) {
                $result = $this->treeFiles("$path/$file");
                $files = array_merge($result, $files);
            } else {
                if ($file != '.' && $file != '..') {
                    if (substr($file, 0, 2) != '._') {
                        $ext = explode('.', $file);
                        $ext = $ext[count($ext) - 1];
                        if (in_array($ext, $filter)) {
                            $files[] = $path.'/'.$file;
                        }
                    }
                }
            }
        }
        $dir->close();

        return $files;
    }
    private function loadLocalMovies()
    {
        if (env('MOVIE_FILE_URL', null) == null) {
            return;
        }
        $files = $this->treeFiles(env('MOVIE_FILE_URL'));
        $movies = [];
        foreach ($files as $file) {
            $exp = explode('/', $file);
            $fileName = end($exp);
            $movies[] = ['name' => $fileName, 'file' => $file, 'size' => $this->formatBytes(filesize($file))];
        }
        return $movies;
    }
    private function movieAction($action, $param = null, $param1 = null)
    {
        $param1 = $param1 ? $param1 : -1;
        if ($action == 'local') {
            if ($param != null) {
                $movieFile = base64_decode($param);
                if (file_exists($movieFile)) {
                    //Omxplayer
                }
            }
            $data['movies'] = $this->loadLocalMovies();

            return $data;
        } elseif ($action == 'list' || $action == 'search') {
            $type = json_decode('{"6":"动作","22":"成人","14":"冒险","15":"动画","11":"传记","4":"喜剧","8":"犯罪","10":"纪录片","1":"纪录片","16":"家庭","12":"幻想","27":"胶片","28":"娱乐","17":"历史","7":"恐怖","19":"音乐","20":"音乐剧","2":"魔幻","25":"新闻","26":"现实TV","5":"浪漫","9":"科幻","23":"短片","13":"运动","105":"脱口秀","3":"惊悚","106":"电视剧","21":"战争","18":"西部"}', true);
            $area = json_decode('{"us":"USA","gb":"UK","ca":"Canada","au":"Australia","in":"India","jp":"Japan","fr":"France","kr":"Korea","de":"Germany","hk":"Hong Kong","th":"Thailand","it":"Italy","cn":"China","es":"Spain","nz":"New Zealand","nl":"Netherlands","ie":"Ireland","no":"Norway","za":"South Africa","dk":"Denmark","be":"Belgium","se":"Sweden","mx":"Mexico","ru":"Russia","pl":"Poland","ro":"Romania"}', true);
            $bot = new WatchSoMuch();
            $page = $param ? $param : 1;
            $cacheName = "movie_{$action}_{$param1}_{$page}";
            $result = null;//Cache::get($cacheName);
            $start_page = 1;
            if ($page > 3) {
                $start_page = $page - 3;
            } else {
                $start_page = 1;
            }
            if ($page > 3) {
                $end_page = $page + 3;
            } else {
                $end_page = $start_page + 5;
            }
            if (!$result || $action == 'search') {
                if ($action == 'search') {
                    $result = $bot->loadMovies(1, $param1);
                } else {
                    $result = $bot->loadMovies($page, $param1);    
                }
                Cache::put($cacheName, $result, 240);
            }
            foreach ($result['movies'] as $k => $v) {
                $id = $v['id'];
                $fileName = 'movie/'.$id.'.json';
                if (!$this->storage->exists($fileName)) {
                    $this->storage->put($fileName, json_encode($result['movies'][$k]));
                }
            }
            $result['page'] = $page;
            // $result['movies'] = $result;
            $result['movie_type'] = $type;
            $result['type'] = $param1;
            if ($action != 'search')
                $result['page_range'] = [$start_page, $end_page];
            $result['area_list'] = $area;
            // dd($result['page_range']);
            return $result;
        } elseif (is_numeric($action)) {
            $info = $this->loadMovieInfo($action);
            if (is_numeric($param)) {
                //下载
                $torrent = $info['torrent'][$param];

                $fileName = 'torrent/'.$action.'-'.$param.'.torrent';
                if (!$this->storage->exists($fileName)) {
                    $url = $torrent['file'];
                    $bot = new WatchSoMuch();
                    $magnet = $bot->downloadTorrent($url);
                    $this->storage->put($fileName, $magnet);
                }
                $torrent = $this->storage->get($fileName);
                $ac = new AriaController();
                // dd($ac->addTorrent($torrent));
                $r = $ac->addUri(array($torrent),array(
                    'dir'=>env('MOVIE_FILE_URL'),
                ));
                if (isset($r['result'])) {
                    $info['download'] = '下载任务已添加.';
                } else {
                    if ($r == null) {
                        $info['download'] = '无法连接到服务器.';
                    } else {
                        $info['download'] = '下载任务添加失败.';
                    }
                }
            }

            return $info;
        } else {
            return;
        }
    }
    public function aria2Action($action, $param = null, $param1 = null)
    {
        $result = [];
        $ac = new AriaController();
        if ($action == 'tasks') {
            //任务查看
            if ($param && $param1) {
                $gid = $param;
                $action = $param1;
                $ac->$param1($gid);
            }
            $stat = $ac->getGlobalStat();
            if ($stat == null) {
                return;
            }
            if (isset($stat['error'])) {
                $result['error'] = $stat['error'];
            } else {
                $result['stat'] = $stat['result'];
                $result['stat']['downloadSpeed'] = $this->formatBytes($result['stat']['downloadSpeed']);
                $result['stat']['uploadSpeed'] = $this->formatBytes($result['stat']['uploadSpeed']);
                //uploadSpeed
            }
            $downloading = $ac->tellActive();

            $result['downloading'] = $downloading['result'];
            foreach ($result['downloading'] as $k => $v) {
                $result['downloading'][$k]['completedLength'] = $this->formatBytes($v['completedLength']);
                $result['downloading'][$k]['totalLength'] = $this->formatBytes($v['totalLength']);
                $result['downloading'][$k]['downloadSpeed'] = $this->formatBytes($v['downloadSpeed']);
                //$this->formatBytes($result['stat']['uploadSpeed'] * 8);
            }
            // dd($result);
        }

        return $result;
    }
    public function showView(Request $request, $view = 'movie', $action = 'list', $param = null, $param1 = null)
    {
        $title = '';
        $file = \File::get(storage_path('config/menu.json'));
        $menus = json_decode($file, true);
        $data = [];
        if ($view == 'movie') {
            if ($action == 'search') {
                $data = $this->movieAction('search', $action, $param);
                $view = 'movie';
                $action = 'list';
            } else {
                $data = $this->movieAction($action, $param, $param1);
                if (is_numeric($action)) {
                    $action = 'detail';
                }                
            }
        } elseif ($view == 'system') {
            $data = $this->systemAction($action, $param, $param1);
        } elseif ($view == 'aria2') {
            $data = $this->aria2Action($action, $param, $param1);
        }

        return view("{$view}.{$action}", ['menus' => $menus, 'view' => $view, 'action' => $action, 'data' => $data, 'title' => $title, 'param' => $param, 'param1' => $param1]);
    }
}
