<?php

namespace addons\qrcode\controller;

use PHPQRCode\QRcode;
use think\addons\Controller;

class Index extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function build()
    {
        $param = $this->request->param();
        empty($param['text']) && $this->error('请输入文本内容');
        $data = [
            'text'   => $param['text'],
            'level'  => empty($param['level']) ? 0 : $param['level'],
            'size'   => empty($param['size']) ? 3 : $param['size'],
            'margin' => empty($param['margin']) ? 4 : $param['margin'],
        ];
        if (empty($param['save'])) {
            QRcode::png($data['text'], false, $data['level'], $data['size'], $data['margin'], false);
            exit;
        } else {
            $config = get_addon_config('qrcode');
            if (!is_dir($config['path'])) {
                @mkdir($config['path'], 0755, true);
            }
            $outfile = $config['path'] . '/' . md5(microtime(true)) . '.' . $config['type'];
            QRcode::png($data['text'], $outfile, $data['level'], $data['size'], $data['margin'], true);
            return $this->request->domain() . ltrim($outfile, '.');
        }
    }
}
