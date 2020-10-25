<?php
namespace Server;

class StatsServer extends Server
{
    //相关配置
    public $count;
    public $task_count;
    public $insert_mysql; //data
    public $setting;
    public $recyle_time = 60000;//60秒回收一次内存
    public $insert_time = 65000;//65
    static public $time_interval = 1; //5min
    static public $time_key_interval = 1;//

    protected $serv;
    protected $worker_id;
    protected $pid_file;
    //监听端口
    const SVR_PORT_STATS = 9903;
    const STATS_PKG_LEN = 25;

    const PROCESS_NAME = "stats_server";

    const T_ALL = 1;
    const T_SERVER = 2;
    const T_CLIENT = 3;

    /**
     * 将数据插入数据库
     * @param $data
     */
    function insertToDB($data)
    {
        $today = date('Ymd');
        $table_server = 'stats_server_'.$today;
        $table_client = 'stats_client_'.$today;
        $table_total = 'stats_'.$today;

        foreach ($data as $key => $content)
        {
            $this->log("insert task-{$key} to stats_server table");
            foreach ($content['server'] as $server_ip => $c)
            {
                $server_count = $this->getCount($content,self::T_SERVER,$server_ip);
                $server_count['ip'] =  $server_ip;
                $server_count['total_client'] = self::tryEncode($server_count, 'total_client');
                $server_count['succ_client'] = self::tryEncode($server_count, 'succ_client');
                $server_count['fail_client'] = self::tryEncode($server_count, 'fail_client');
                $server_count['ret_code'] = self::tryEncode($server_count, 'ret_code');
                $server_count['succ_ret_code'] = self::tryEncode($server_count, 'succ_ret_code');
                $result=table($table_server)->put($server_count);
                if (!$result and \Swoole::$php->db->errno() == 1146)
                {
                    $this->createTable1($table_server);
                    table($table_server)->put($server_count);
                }
            }

            $this->log("insert task-{$key} to stats_client table");
            foreach ($content['client'] as $client_ip => $c)
            {
                $client_count = $this->getCount($content,self::T_CLIENT,$client_ip);
                $client_count['ip'] =  $client_ip;
                $client_count['total_server'] = self::tryEncode($client_count, 'total_server');
                $client_count['succ_server'] = self::tryEncode($client_count, 'succ_server');
                $client_count['fail_server'] = self::tryEncode($client_count, 'fail_server');
                $client_count['ret_code'] = self::tryEncode($client_count, 'ret_code');
                $client_count['succ_ret_code'] = self::tryEncode($client_count, 'succ_ret_code');

                $ret = table($table_client)->put($client_count);
                if (!$ret and \Swoole::$php->db->errno() == 1146)
                {
                    $this->createTable2($table_client);
                    table($table_client)->put($client_count);
                }
            }

            $this->log("insert task-{$key} to stats table");

            $count = $this->getCount($content,self::T_ALL);
            $count['total_server'] = self::tryEncode($count, 'total_server');
            $count['succ_server'] = self::tryEncode($count, 'succ_server');
            $count['fail_server'] = self::tryEncode($count, 'fail_server');
            $count['total_client'] = self::tryEncode($count, 'total_client');
            $count['succ_client'] = self::tryEncode($count, 'succ_client');
            $count['fail_client'] = self::tryEncode($count, 'fail_client');
            $count['ret_code'] = self::tryEncode($count, 'ret_code');
            $count['succ_ret_code'] = self::tryEncode($count, 'succ_ret_code');

            $ret = table($table_total)->put($count);
            if (!$ret and \Swoole::$php->db->errno() == 1146)
            {
                $this->createTable3($table_total);
                table($table_total)->put($count);
            }
        }
    }

    /**
     * 计算计数
     * @param $data
     * @param int $type
     * @param null $ip
     * @return array
     */
    function getCount($data, $type = self::T_ALL, $ip = null)
    {
        $count = array();
        $count['module_id'] =  $data['all']['module_id'];
        $count['interface_id'] =  $data['all']['interface_id'];
        $count['time_key'] =  $data['all']['time_key'];
        $count['date_key'] =  $data['all']['date_key'];
        $count['total_count'] =  $data['all']['total_count'];
        $count['fail_count'] =  $data['all']['fail_count'];
        $count['total_time'] =  $data['all']['total_time'];
        $count['total_fail_time'] =  $data['all']['total_fail_time'];

        if ($type == self::T_ALL)
        {
            $count['total_count'] =  $data['all']['total_count'];
            $count['fail_count'] =  $data['all']['fail_count'];
            $count['total_time'] =  $data['all']['total_time'];
            $count['max_time'] =  $data['all']['max_time'];
            $count['min_time'] =  $data['all']['min_time'];
            $count['total_fail_time'] =  $data['all']['total_fail_time'];

            $count['total_server'] = $data['all']['total_server'];
            $count['succ_server'] = isset($data['all']['succ_server'])?$data['all']['succ_server']:array();
            $count['fail_server'] = isset($data['all']['fail_server'])?$data['all']['fail_server']:array();
            $count['total_client'] = $data['all']['total_client'];
            $count['succ_client'] = isset($data['all']['succ_client'])?$data['all']['succ_client']:array();
            $count['fail_client'] = isset($data['all']['fail_client'])?$data['all']['fail_client']:array();
            $count['ret_code'] = isset($data['all']['ret_code'])?$data['all']['ret_code']:array();
            $count['succ_ret_code'] = isset($data['all']['succ_ret_code'])?$data['all']['succ_ret_code']:array();
        }
        if ($type == self::T_SERVER and !empty($ip))
        {
            $count['total_count'] =  $data['server'][$ip]['total_count'];
            $count['fail_count'] =  $data['server'][$ip]['fail_count'];
            $count['total_time'] =  $data['server'][$ip]['total_time'];
            $count['max_time'] =  $data['server'][$ip]['max_time'];
            $count['min_time'] =  $data['server'][$ip]['min_time'];
            $count['total_fail_time'] =  $data['server'][$ip]['total_fail_time'];

            $count['total_client'] = $data['server'][$ip]['total_client'];
            $count['succ_client'] = isset($data['server'][$ip]['succ_client'])?$data['server'][$ip]['succ_client']:array();
            $count['fail_client'] = isset($data['server'][$ip]['fail_client'])?$data['server'][$ip]['fail_client']:array();
            $count['ret_code'] = isset($data['server'][$ip]['ret_code'])?$data['server'][$ip]['ret_code']:array();
            $count['succ_ret_code'] = isset($data['server'][$ip]['succ_ret_code'])?$data['server'][$ip]['succ_ret_code']:array();
        }
        if ($type == self::T_CLIENT and !empty($ip))
        {
            $count['total_count'] = $data['client'][$ip]['total_count'];
            $count['fail_count'] = $data['client'][$ip]['fail_count'];
            $count['total_time'] = $data['client'][$ip]['total_time'];
            $count['max_time'] = $data['client'][$ip]['max_time'];
            $count['min_time'] = $data['client'][$ip]['min_time'];
            $count['total_fail_time'] =  $data['client'][$ip]['total_fail_time'];

            $count['total_server'] = $data['client'][$ip]['total_server'];
            $count['succ_server'] = isset($data['client'][$ip]['succ_server'])?$data['client'][$ip]['succ_server']:array();
            $count['fail_server'] = isset($data['client'][$ip]['fail_server'])?$data['client'][$ip]['fail_server']:array();
            $count['ret_code'] = isset($data['client'][$ip]['ret_code'])?$data['client'][$ip]['ret_code']:array();
            $count['succ_ret_code'] = isset($data['client'][$ip]['succ_ret_code'])?$data['client'][$ip]['succ_ret_code']:array();
        }
        //平均响应时间
        if ($count['total_count'] != 0)
        {
            $count['avg_time'] = $count['total_time'] / $count['total_count'];
        }
        else
        {
            $count['avg_time'] = 0;
        }
        //平均失败响应时间
        if ($count['fail_count'] != 0)
        {
            $count['avg_fail_time'] = $count['total_fail_time'] / $count['fail_count'];
        }
        else
        {
            $count['avg_fail_time'] = 0;
        }
        return $count;
    }

    static function tryEncode($data, $key)
    {
        if (!empty($data[$key]))
        {
            return json_encode($data[$key]);
        }
        else
        {
            return "";
        }
    }

    function onStart(\swoole_server $serv, $worker_id)
    {
        $this->worker_id = $worker_id;
        if ($this->worker_id <= $this->setting['worker_num'] -1)
        {
            //swoole_set_process_name(self::PROCESS_NAME.": worker #$worker_id");
            $serv->addtimer($this->recyle_time);
            if (isset($this->setting['worker_dump_file']))
            {
                $dump_file = $this->setting['worker_dump_file']."_".$worker_id;
                if (file_exists($dump_file))
                {
                    $this->count = unserialize(file_get_contents($dump_file));
                    $this->log("load worker {$worker_id} data from last :".print_r($this->count,1));
                    unlink($dump_file);
                }
            }
        }
        else
        {
            //swoole_set_process_name(self::PROCESS_NAME.": task #$worker_id");
            $serv->addtimer($this->insert_time);
            if (isset($this->setting['task_dump_file']))
            {
                $dump_file = $this->setting['task_dump_file']."_".$worker_id;
                if (file_exists($dump_file))
                {
                    $this->task_count = unserialize(file_get_contents($dump_file));
                    $this->log("load task {$worker_id} data from last :".print_r($this->task_count,1));
                    unlink($dump_file);
                }
            }
        }
    }

    function onTask($serv, $task_id, $from_worker_id, $data)
    {
        $map = json_decode($data,1);
        if ($map)
        {
            foreach ($map as $k => $v)
            {
                if ($k == 'all')
                {
                    $key = $v['key'];
                    if (!isset($this->task_count[$key]))
                    {
                        list($this->task_count[$key]['all']['module_id'], $this->task_count[$key]['all']['interface_id'], , $this->task_count[$key]['all']['time_key']) = explode('_', $key, 4);
                        $this->task_count[$key]['all']['date_key'] = date('Ymd', time() - 300);
                        $this->task_count[$key]['all']['total_count'] = 0;
                        $this->task_count[$key]['all']['fail_count'] = 0;
                        $this->task_count[$key]['all']['total_time'] = 0;
                        $this->task_count[$key]['all']['total_fail_time'] = 0;
                        $this->task_count[$key]['all']['key'] = $key;

                        $this->task_count[$key]['all']['max_time'] = $v['max_time'];
                        $this->task_count[$key]['all']['min_time'] = $v['min_time'];
                    }
                    else
                    {
                        if ($v['max_time'] > $this->task_count[$key]['all']['max_time'])
                        {
                            $this->task_count[$key]['all']['max_time'] = $v['max_time'];
                        }
                        if ($v['min_time'] < $this->task_count[$key]['all']['min_time'])
                        {
                            $this->task_count[$key]['all']['min_time'] = $v['min_time'];
                        }
                    }

                    /**
                     * all
                     */
                    $this->task_count[$key]['all']['total_count'] += $v['total_count'];
                    $this->task_count[$key]['all']['total_time'] += $v['total_time'];
                    $this->task_count[$key]['all']['fail_count'] += $v['fail_count'];
                    $this->task_count[$key]['all']['total_fail_time'] += $v['total_fail_time'];

                    foreach ($v as $s => $vv)
                    {
                        if (strpos($s, 'total_server_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['total_server'][substr($s, 13)]))
                            {
                                $this->task_count[$key]['all']['total_server'][substr($s, 13)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['total_server'][substr($s, 13)] += $vv;
                            }
                        }
                        if (strpos($s, 'total_client_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['total_client'][substr($s, 13)]))
                            {
                                $this->task_count[$key]['all']['total_client'][substr($s, 13)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['total_client'][substr($s, 13)] += $vv;
                            }
                        }
                        if (strpos($s, 'fail_server_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['fail_server'][substr($s, 12)]))
                            {
                                $this->task_count[$key]['all']['fail_server'][substr($s, 12)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['fail_server'][substr($s, 12)] += $vv;
                            }
                        }
                        if (strpos($s, 'fail_client_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['fail_client'][substr($s, 12)]))
                            {
                                $this->task_count[$key]['all']['fail_client'][substr($s, 12)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['fail_client'][substr($s, 12)] += $vv;
                            }
                        }
                        if (strpos($s, 'ret_code_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['ret_code'][substr($s, 9)]))
                            {
                                $this->task_count[$key]['all']['ret_code'][substr($s, 9)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['ret_code'][substr($s, 9)] += $vv;
                            }
                        }
                        if (strpos($s, 'succ_server_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['succ_server'][substr($s, 12)]))
                            {
                                $this->task_count[$key]['all']['succ_server'][substr($s, 12)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['succ_server'][substr($s, 12)] += $vv;
                            }
                        }
                        if (strpos($s, 'succ_client_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['succ_client'][substr($s, 12)]))
                            {
                                $this->task_count[$key]['all']['succ_client'][substr($s, 12)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['succ_client'][substr($s, 12)] += $vv;
                            }
                        }
                        if (strpos($s, 'succ_ret_code_') === 0)
                        {
                            if (!isset( $this->task_count[$key]['all']['succ_ret_code'][substr($s, 14)]))
                            {
                                $this->task_count[$key]['all']['succ_ret_code'][substr($s, 14)] = $vv;
                            }
                            else
                            {
                                $this->task_count[$key]['all']['succ_ret_code'][substr($s, 14)] += $vv;
                            }
                        }
                    }
                }

                if ($k == 'server')
                {
                    foreach ($v as $server_ip => $server)
                    {
                        if (!isset($this->task_count[$key]['server'][$server_ip]))
                        {
                            $this->task_count[$key]['server'][$server_ip]['total_count'] = $server['total_count'];
                            $this->task_count[$key]['server'][$server_ip]['total_time'] = $server['total_time'];
                            $this->task_count[$key]['server'][$server_ip]['fail_count'] = $server['fail_count'];
                            $this->task_count[$key]['server'][$server_ip]['total_fail_time'] = $server['total_fail_time'];
                            $this->task_count[$key]['server'][$server_ip]['key'] = $key;

                            $this->task_count[$key]['server'][$server_ip]['max_time'] = $server['max_time'];
                            $this->task_count[$key]['server'][$server_ip]['min_time'] = $server['min_time'];
                        }
                        else
                        {
                            $this->task_count[$key]['server'][$server_ip]['total_count'] += $server['total_count'];
                            $this->task_count[$key]['server'][$server_ip]['total_time'] += $server['total_time'];
                            $this->task_count[$key]['server'][$server_ip]['fail_count'] += $server['fail_count'];
                            $this->task_count[$key]['server'][$server_ip]['total_fail_time'] += $server['total_fail_time'];

                            if ($server['max_time'] > $this->task_count[$key]['server'][$server_ip]['max_time'])
                            {
                                $this->task_count[$key]['server'][$server_ip]['max_time'] = $server['max_time'];
                            }
                            if ($server['min_time'] < $this->task_count[$key]['server'][$server_ip]['min_time'])
                            {
                                $this->task_count[$key]['server'][$server_ip]['min_time'] = $server['min_time'];
                            }
                        }
                        foreach ($server as $s => $vv)
                        {
                            if (strpos($s, 'total_client_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['server'][$server_ip]['total_client'][substr($s, 13)]))
                                {
                                    $this->task_count[$key]['server'][$server_ip]['total_client'][substr($s, 13)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['server'][$server_ip]['total_client'][substr($s, 13)] += $vv;
                                }
                            }
                            if (strpos($s, 'fail_client_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['server'][$server_ip]['fail_client'][substr($s, 12)]))
                                {
                                    $this->task_count[$key]['server'][$server_ip]['fail_client'][substr($s, 12)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['server'][$server_ip]['fail_client'][substr($s, 12)] += $vv;
                                }
                            }
                            if (strpos($s, 'ret_code_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['server'][$server_ip]['ret_code'][substr($s, 9)]))
                                {
                                    $this->task_count[$key]['server'][$server_ip]['ret_code'][substr($s, 9)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['server'][$server_ip]['ret_code'][substr($s, 9)] += $vv;
                                }
                            }
                            if (strpos($s, 'succ_client_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['server'][$server_ip]['succ_client'][substr($s, 12)]))
                                {
                                    $this->task_count[$key]['server'][$server_ip]['succ_client'][substr($s, 12)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['server'][$server_ip]['succ_client'][substr($s, 12)] += $vv;
                                }
                            }
                            if (strpos($s, 'succ_ret_code_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['server'][$server_ip]['succ_ret_code'][substr($s, 14)]))
                                {
                                    $this->task_count[$key]['server'][$server_ip]['succ_ret_code'][substr($s, 14)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['server'][$server_ip]['succ_ret_code'][substr($s, 14)] += $vv;
                                }
                            }
                        }

                    }
                }

                if ($k == 'client')
                {
                    foreach ($v as $client_ip => $client)
                    {
                        if (!isset($this->task_count[$key]['client'][$client_ip]))
                        {
                            $this->task_count[$key]['client'][$client_ip]['total_count'] = $client['total_count'];
                            $this->task_count[$key]['client'][$client_ip]['total_time'] = $client['total_time'];
                            $this->task_count[$key]['client'][$client_ip]['fail_count'] = $client['fail_count'];
                            $this->task_count[$key]['client'][$client_ip]['total_fail_time'] = $client['total_fail_time'];
                            $this->task_count[$key]['client'][$client_ip]['key'] = $key;

                            $this->task_count[$key]['client'][$client_ip]['max_time'] = $client['max_time'];
                            $this->task_count[$key]['client'][$client_ip]['min_time'] = $client['min_time'];
                        }
                        else
                        {
                            $this->task_count[$key]['client'][$client_ip]['total_count'] += $client['total_count'];
                            $this->task_count[$key]['client'][$client_ip]['total_time'] += $client['total_time'];
                            $this->task_count[$key]['client'][$client_ip]['fail_count'] += $client['fail_count'];
                            $this->task_count[$key]['client'][$client_ip]['total_fail_time'] += $client['total_fail_time'];

                            if ($client['max_time'] > $this->task_count[$key]['client'][$client_ip]['max_time'])
                            {
                                $this->task_count[$key]['client'][$client_ip]['max_time'] = $client['max_time'];
                            }
                            if ($client['min_time'] < $this->task_count[$key]['client'][$client_ip]['min_time'])
                            {
                                $this->task_count[$key]['client'][$client_ip]['min_time'] = $client['min_time'];
                            }
                        }
                        foreach ($client as $s => $vv)
                        {
                            if (strpos($s, 'total_server_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['client'][$client_ip]['total_server'][substr($s, 13)]))
                                {
                                    $this->task_count[$key]['client'][$client_ip]['total_server'][substr($s, 13)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['client'][$client_ip]['total_server'][substr($s, 13)] += $vv;
                                }
                            }
                            if (strpos($s, 'fail_server_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['client'][$client_ip]['fail_server'][substr($s, 12)]))
                                {
                                    $this->task_count[$key]['client'][$client_ip]['fail_server'][substr($s, 12)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['client'][$client_ip]['fail_server'][substr($s, 12)] += $vv;
                                }
                            }
                            if (strpos($s, 'ret_code_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['client'][$client_ip]['ret_code'][substr($s, 9)]))
                                {
                                    $this->task_count[$key]['client'][$client_ip]['ret_code'][substr($s, 9)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['client'][$client_ip]['ret_code'][substr($s, 9)] += $vv;
                                }
                            }
                            if (strpos($s, 'succ_server_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['client'][$client_ip]['succ_server'][substr($s, 12)]))
                                {
                                    $this->task_count[$key]['client'][$client_ip]['succ_server'][substr($s, 12)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['client'][$client_ip]['succ_server'][substr($s, 12)] += $vv;
                                }
                            }
                            if (strpos($s, 'succ_ret_code_') === 0)
                            {
                                if (!isset( $this->task_count[$key]['client'][$client_ip]['succ_ret_code'][substr($s, 14)]))
                                {
                                    $this->task_count[$key]['client'][$client_ip]['succ_ret_code'][substr($s, 14)] = $vv;
                                }
                                else
                                {
                                    $this->task_count[$key]['client'][$client_ip]['succ_ret_code'][substr($s, 14)] += $vv;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function taskReport()
    {
        if (!empty($this->task_count))
        {
            $return = array();
            $time_key = self::getMinute();
            foreach ($this->task_count as $key => $data)
            {
                //task 将本time_key之前的数据进行上报,本time_key的数据进行汇总
                $m = explode('_', $key, 4);
                if ( $m[2] < $time_key-1 )
                {
                    $return[$key] = $data;
                    unset($this->task_count[$key]);
                }
            }
            echo json_encode($return);
            if ($return)
            {
                $this->insertToDB($return);
            }
        }
    }

    function onFinish($serv, $task_id, $data)
    {
        $this->log("on fin ".print_r(json_decode($data,1),1));
        $this->insert_mysql[] = $data;
    }

    //
    static function getMinute()
    {
        return intval((date('G')*60 + date('i')) / self::$time_interval);
    }

    static function getTimeKey()
    {
        return intval((date('G')*60 + date('i')) / self::$time_key_interval);
    }

    static function getM()
    {
        return date('G')*60 + date('i');
    }

    function parseStatsPackage($client_ip, $pkg_data)
    {
        $pkg = unpack('Ninterface_id/Nmodule_id/Csuccess/Nret_code/Nserver_ip/Nuse_ms/Naddtime', $pkg_data);
        if (!$pkg)
        {
            $this->log("error package. data".$pkg_data);
            return;
        }
        var_dump($pkg);
        $pkg['server_ip'] = long2ip($pkg['server_ip']);
        $pkg['client_ip'] = $client_ip;
        $this->setWorkerCount($pkg);
    }

    //设置计数 生产者
    function setWorkerCount($params)
    {
        $m = self::getM();
        $key = $params['module_id'].'_'.$params['interface_id']."_".intval($m/self::$time_interval)."_".intval($m/self::$time_key_interval);
        $client_ip = $params['client_ip'];
        $server_ip = $params['server_ip'];
        /**
         * all
         */
        if (!isset($this->count[$key]))
        {
            $this->count[$key]['all']['total_count'] = 1;
            $this->count[$key]['all']['total_time'] = 1;
            $this->count[$key]['all']['max_time'] = $params['use_ms'];//最大响应时间
            $this->count[$key]['all']['min_time'] = $params['use_ms'];//最小响应时间
            if ($params['success'] == 0)
            {
                $this->count[$key]['all']['fail_count'] = 1;
                $this->count[$key]['all']['total_fail_time'] = $params['use_ms'];
            }
            else
            {
                $this->count[$key]['all']['fail_count'] = 0;
                $this->count[$key]['all']['total_fail_time'] = 0;
            }
            $this->count[$key]['all']['key'] = $key;
        }
        else
        {
            $this->count[$key]['all']['total_count'] += 1;
            $this->count[$key]['all']['total_time'] += $params['use_ms'];
            if ($params['success'] == 0)
            {
                $this->count[$key]['all']['fail_count'] += 1;
                $this->count[$key]['all']['total_fail_time'] += $params['use_ms'];
            }

            if ($params['use_ms'] > $this->count[$key]['all']['max_time'])
            {
                $this->count[$key]['all']['max_time'] = $params['use_ms'];//最大响应时间
            }
            if ($params['use_ms'] < $this->count[$key]['all']['min_time'])
            {
                $this->count[$key]['all']['min_time'] = $params['use_ms'];//最小响应时间
            }
        }

        if (isset($this->count[$key]['all']['total_server_'.$server_ip]))
        {
            $this->count[$key]['all']['total_server_'.$server_ip] += 1;
        }
        else
        {
            $this->count[$key]['all']['total_server_'.$server_ip] = 1;
        }
        if (isset($this->count[$key]['all']['total_client_'.$client_ip]))
        {
            $this->count[$key]['all']['total_client_'.$client_ip] += 1;
        }
        else
        {
            $this->count[$key]['all']['total_client_'.$client_ip] = 1;
        }
        if ($params['success'] == 0)
        {
            if (isset($this->count[$key]['all']['fail_server_'.$server_ip]))
            {
                $this->count[$key]['all']['fail_server_'.$server_ip] += 1;
            }
            else
            {
                $this->count[$key]['all']['fail_server_'.$server_ip] = 1;
            }
            if (isset($this->count[$key]['all']['fail_client_'.$client_ip]))
            {
                $this->count[$key]['all']['fail_client_'.$client_ip] += 1;
            }
            else
            {
                $this->count[$key]['all']['fail_client_'.$server_ip] = 1;
            }

            if (isset($this->count[$key]['all']['ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['all']['ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['all']['ret_code_'.$params['ret_code']] = 1;
            }
        }
        else
        {
            if (isset($this->count[$key]['all']['succ_server_'.$server_ip]))
            {
                $this->count[$key]['all']['succ_server_'.$server_ip] += 1;
            }
            else
            {
                $this->count[$key]['all']['succ_server_'.$server_ip] = 1;
            }
            if (isset($this->count[$key]['all']['succ_client_'.$client_ip]))
            {
                $this->count[$key]['all']['succ_client_'.$client_ip] += 1;
            }
            else
            {
                $this->count[$key]['all']['succ_client_'.$client_ip] = 1;
            }

            if (isset($this->count[$key]['all']['succ_ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['all']['succ_ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['all']['succ_ret_code_'.$params['ret_code']] = 1;
            }
        }


        /**
         * Server 被调
         */

        if (!isset($this->count[$key]['server'][$server_ip]))
        {
            $this->count[$key]['server'][$server_ip]['total_count'] = 1;
            $this->count[$key]['server'][$server_ip]['total_time'] = 1;
            $this->count[$key]['server'][$server_ip]['max_time'] = $params['use_ms'];//最大响应时间
            $this->count[$key]['server'][$server_ip]['min_time'] = $params['use_ms'];//最小响应时间
            if ($params['success'] == 0)
            {
                $this->count[$key]['server'][$server_ip]['fail_count'] = 1;
                $this->count[$key]['server'][$server_ip]['total_fail_time'] = $params['use_ms'];
            }
            else
            {
                $this->count[$key]['server'][$server_ip]['fail_count'] = 0;
                $this->count[$key]['server'][$server_ip]['total_fail_time'] = 0;
            }
            $this->count[$key]['server'][$server_ip]['key'] = $key;
        }
        else
        {
            $this->count[$key]['server'][$server_ip]['total_count'] += 1;
            $this->count[$key]['server'][$server_ip]['total_time'] += $params['use_ms'];
            if ($params['success'] == 0)
            {
                $this->count[$key]['server'][$server_ip]['fail_count'] += 1;
                $this->count[$key]['server'][$server_ip]['total_fail_time'] += $params['use_ms'];
            }

            if ($params['use_ms'] > $this->count[$key]['server'][$server_ip]['max_time'])
            {
                $this->count[$key]['server'][$server_ip]['max_time'] = $params['use_ms'];//最大响应时间
            }
            if ($params['use_ms'] < $this->count[$key]['server'][$server_ip]['min_time'])
            {
                $this->count[$key]['server'][$server_ip]['min_time'] = $params['use_ms'];//最小响应时间
            }
        }

        if (isset($this->count[$key]['server'][$server_ip]['total_client_'.$client_ip]))
        {
            $this->count[$key]['server'][$server_ip]['total_client_'.$client_ip] += 1;
        }
        else
        {
            $this->count[$key]['server'][$server_ip]['total_client_'.$client_ip] = 1;
        }
        if ($params['success'] == 0)
        {
            if (isset($this->count[$key]['server'][$server_ip]['fail_client_'.$client_ip]))
            {
                $this->count[$key]['server'][$server_ip]['fail_client_'.$client_ip] += 1;
            }
            else
            {
                $this->count[$key]['server'][$server_ip]['fail_client_'.$client_ip] = 1;
            }

            if (isset($this->count[$key]['server'][$server_ip]['ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['server'][$server_ip]['ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['server'][$server_ip]['ret_code_'.$params['ret_code']] = 1;
            }
        }
        else
        {
            if (isset($this->count[$key]['server'][$server_ip]['succ_client_'.$client_ip]))
            {
                $this->count[$key]['server'][$server_ip]['succ_client_'.$client_ip] += 1;
            }
            else
            {
                $this->count[$key]['server'][$server_ip]['succ_client_'.$client_ip] = 1;
            }

            if (isset($this->count[$key]['server'][$server_ip]['succ_ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['server'][$server_ip]['succ_ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['server'][$server_ip]['succ_ret_code_'.$params['ret_code']] = 1;
            }
        }

        /**
         * Client
         */
        if (!isset($this->count[$key]['client'][$client_ip]))
        {
            $this->count[$key]['client'][$client_ip]['total_count'] = 1;
            $this->count[$key]['client'][$client_ip]['total_time'] = 1;
            $this->count[$key]['client'][$client_ip]['max_time'] = $params['use_ms'];
            $this->count[$key]['client'][$client_ip]['min_time'] = $params['use_ms'];
            if ($params['success'] == 0)
            {
                $this->count[$key]['client'][$client_ip]['fail_count'] = 1;
                $this->count[$key]['client'][$client_ip]['total_fail_time'] = $params['use_ms'];
            }
            else
            {
                $this->count[$key]['client'][$client_ip]['fail_count'] = 0;
                $this->count[$key]['client'][$client_ip]['total_fail_time'] = 0;
            }
            $this->count[$key]['client'][$client_ip]['key'] = $key;
        }
        else
        {
            $this->count[$key]['client'][$client_ip]['total_count'] += 1;
            $this->count[$key]['client'][$client_ip]['total_time'] += $params['use_ms'];
            if ($params['success'] == 0)
            {
                $this->count[$key]['client'][$client_ip]['fail_count'] += 1;
                $this->count[$key]['client'][$client_ip]['total_fail_time'] += $params['use_ms'];
            }

            if ($params['use_ms'] > $this->count[$key]['client'][$client_ip]['max_time'])
            {
                $this->count[$key]['client'][$client_ip]['max_time'] = $params['use_ms'];//最大响应时间
            }
            if ($params['use_ms'] < $this->count[$key]['client'][$client_ip]['min_time'])
            {
                $this->count[$key]['client'][$client_ip]['min_time'] = $params['use_ms'];//最小响应时间
            }
        }

        if (isset($this->count[$key]['client'][$client_ip]['total_server_'.$server_ip]))
        {
            $this->count[$key]['client'][$client_ip]['total_server_'.$server_ip] += 1;
        }
        else
        {
            $this->count[$key]['client'][$client_ip]['total_server_'.$server_ip] = 1;
        }
        if ($params['success'] == 0)
        {
            if (isset($this->count[$key]['client'][$client_ip]['fail_server_'.$server_ip]))
            {
                $this->count[$key]['client'][$client_ip]['fail_server_'.$server_ip] += 1;
            }
            else
            {
                $this->count[$key]['client'][$client_ip]['fail_server_'.$server_ip] = 1;
            }

            if (isset($this->count[$key]['client'][$client_ip]['ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['client'][$client_ip]['ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['client'][$client_ip]['ret_code_'.$params['ret_code']] = 1;
            }
        }
        else
        {
            if (isset($this->count[$key]['client'][$client_ip]['succ_server_'.$server_ip]))
            {
                $this->count[$key]['client'][$client_ip]['succ_server_'.$server_ip] += 1;
            }
            else
            {
                $this->count[$key]['client'][$client_ip]['succ_server_'.$server_ip] = 1;
            }

            if (isset($this->count[$key]['client'][$client_ip]['succ_ret_code_'.$params['ret_code']]))
            {
                $this->count[$key]['client'][$client_ip]['succ_ret_code_'.$params['ret_code']] += 1;
            }
            else
            {
                $this->count[$key]['client'][$client_ip]['succ_ret_code_'.$params['ret_code']] = 1;
            }
        }
    }

    function onTimer(\swoole_server $serv, $interval)
    {
        /**
         * worker
         */
        if ($this->worker_id <= $this->setting['worker_num'] -1)
        {
            $time_key = self::getMinute();
            $this->log("worker [{$this->worker_id}] onTimer ({$this->recyle_time}) time_key : $time_key -- ".count($this->count));
            if (!empty($this->count))
            {
                foreach ($this->count as $key => $v)
                {
                    $m = explode('_', $key, 4);
                    if ($time_key == $m[2])
                    {
                        continue;
                    }
                    $target_id = $m[1]%$this->setting['task_worker_num'];
                    $this->log("[{$this->worker_id}] task $key to ".($target_id));
                    $serv->task(json_encode($v),$target_id);
                    unset($this->count[$key]);
                }
            }
        }
        else
        {
            /**
             * task worker
             */
            $time_key = self::getMinute();
            $this->log("task worker [".($this->worker_id)."] onTimer ({$this->insert_time}) time_key : $time_key -- ".count($this->task_count));
            $this->taskReport();
        }
    }

    function workerStop($serv,$worker_id)
    {
        /**
         * worker
         */
        if ($this->worker_id <= $this->setting['worker_num'] -1)
        {
            if (!empty($this->count))
            {
                if (isset($this->setting['worker_dump_file']))
                {
                    $dump_file = $this->setting['worker_dump_file']."_".$worker_id;
                    file_put_contents($dump_file,serialize($this->count));
                }
            }

        }
        else
        {
            /**
             * task worker
             */
            if (!empty($this->task_count))
            {
                if (isset($this->setting['task_dump_file']))
                {
                    $dump_file = $this->setting['task_dump_file']."_".$worker_id;
                    file_put_contents($dump_file,serialize($this->task_count));
                }
            }
        }
    }

    /**
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    function onPackage(\swoole_server $serv, $fd, $from_id, $data)
    {
        $conn = $serv->connection_info($fd, $from_id);
        /**
         *typedef struct
        {
        int32_t interface_id; //接口ID
        int32_t module_id; //模块ID
        int8_t success; //成功或失败
        int32_t ret_code; //返回码
        int32_t server_ip; //服务器端IP
        int32_t millisecond; //调用耗时单位毫秒
        int32_t time; //时间单位秒
        } module_stats;
         */


        $n = strlen($data) / self::STATS_PKG_LEN;
        if (is_float($n))
        {
            $this->log("error udp pacakge size[".strlen($data)."]. data={$data}");
            return;
        }
        for ($i = 0; $i < $n; $i++)
        {
            $pkg_data = substr($data, $i * self::STATS_PKG_LEN, self::STATS_PKG_LEN);
            $this->parseStatsPackage($conn['remote_ip'], $pkg_data);
        }
    }

    function onMasterStart($server)
    {
        //swoole_set_process_name(self::PROCESS_NAME.": master");
        $this->log("stats server start");
        file_put_contents($this->pid_file,$server->master_pid);
    }

    function onManagerStart($server)
    {
        //swoole_set_process_name(self::PROCESS_NAME . ": manager");
    }

    function onManagerStop($server)
    {
        $this->log("stats server shutdown");
        if (file_exists($this->pid_file))
        {
            unlink($this->pid_file);
        }
    }

    function createTable1($table)
    {
        $sql1 = "CREATE TABLE IF NOT EXISTS `{$table}` (
              `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
              `interface_id` int(11) NOT NULL,
              `module_id` int(11) NOT NULL,
              `ip` varchar(16) NOT NULL,
              `time_key` int(11) NOT NULL,
              `date_key` date NOT NULL,
              `total_count` int(11) NOT NULL,
              `fail_count` int(11) NOT NULL,
              `total_time` double NOT NULL,
              `total_fail_time` double NOT NULL,
              `avg_time` int(11) NOT NULL,
              `avg_fail_time` int(11) NOT NULL,
              `max_time` int(11) NOT NULL,
              `min_time` int(11) NOT NULL,
              `fail_client` text NOT NULL,
              `succ_client` text NOT NULL,
              `total_client` text NOT NULL,
              `ret_code` text NOT NULL,
              `succ_ret_code` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        \Swoole::$php->db->query($sql1);

        $sql2 = "ALTER TABLE `{$table}`
          ADD KEY `module_id` (`module_id`),
          ADD KEY `interface_id` (`interface_id`),
          ADD KEY `interface_id_2` (`interface_id`);";
        \Swoole::$php->db->query($sql2);

        $this->log("new table {$table}");
    }

    function createTable2($table)
    {
        $sql1 = "CREATE TABLE IF NOT EXISTS `{$table}` (
          `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
          `interface_id` int(11) NOT NULL,
          `module_id` int(11) NOT NULL,
          `ip` varchar(16) NOT NULL,
          `time_key` int(11) NOT NULL,
          `date_key` date NOT NULL,
          `total_count` int(11) NOT NULL,
          `fail_count` int(11) NOT NULL,
          `total_time` double NOT NULL,
          `total_fail_time` double NOT NULL,
          `avg_time` int(11) NOT NULL,
          `avg_fail_time` int(11) NOT NULL,
          `max_time` int(11) NOT NULL,
          `min_time` int(11) NOT NULL,
          `fail_server` text NOT NULL,
          `succ_server` text NOT NULL,
          `total_server` text NOT NULL,
          `ret_code` text NOT NULL,
          `succ_ret_code` text NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        \Swoole::$php->db->query($sql1);

        $sql2 = "ALTER TABLE `{$table}`
            ADD KEY `module_id` (`module_id`),
            ADD KEY `interface_id` (`interface_id`),
            ADD KEY `interface_id_3` (`interface_id`,`module_id`,`date_key`);";
        \Swoole::$php->db->query($sql2);

        $this->log("new table {$table}");
    }

    function createTable3($table)
    {
        $sql1 = "CREATE TABLE IF NOT EXISTS `{$table}` (
    `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `interface_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `time_key` int(11) NOT NULL,
  `date_key` date NOT NULL,
  `total_count` int(11) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `total_time` double NOT NULL,
  `total_fail_time` double NOT NULL,
  `avg_time` int(11) NOT NULL,
  `avg_fail_time` int(11) NOT NULL,
  `max_time` int(11) NOT NULL DEFAULT '0' COMMENT '最长时间',
  `min_time` int(11) NOT NULL DEFAULT '0' COMMENT '最小时间',
  `fail_server` text NOT NULL,
  `succ_server` text NOT NULL,
  `total_server` text NOT NULL,
  `fail_client` text NOT NULL,
  `succ_client` text NOT NULL,
  `total_client` text NOT NULL,
  `ret_code` text NOT NULL,
  `succ_ret_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        \Swoole::$php->db->query($sql1);

        $sql2 = "ALTER TABLE `{$table}` ADD KEY `module_interface` (`module_id`,`interface_id`);";
        \Swoole::$php->db->query($sql2);

        $this->log("new table {$table}");
    }

    function run($_setting = array())
    {
        $default_setting = array(
            'worker_num' => 24,
            'task_worker_num' => 24,
            'max_request' => 0,
            //'dispatch_mode' => 4,
        );

        $this->pid_file = $_setting['pid_file'];
        $setting = array_merge($default_setting, $_setting);
        $this->setting = $setting;
        $serv = new \swoole_server('0.0.0.0', self::SVR_PORT_STATS, SWOOLE_PROCESS, SWOOLE_UDP);
        $serv->set($setting);
        $serv->on('start', array($this, 'onMasterStart'));
        $serv->on('managerStart', array($this, 'onManagerStart'));
        $serv->on('managerStop', array($this, 'onManagerStop'));
        $serv->on('workerStart', array($this, 'onStart'));
        $serv->on('workerStop', array($this, 'workerStop'));
        $serv->on('receive', array($this, 'onPackage'));
        $serv->on('timer', array($this, 'onTimer'));
        $serv->on('task', array($this, 'onTask'));
        $serv->on('finish', array($this, 'onFinish'));
        $this->serv = $serv;
        $this->serv->start();
    }
}
