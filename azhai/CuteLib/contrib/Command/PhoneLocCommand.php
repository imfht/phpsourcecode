<?php
namespace Cute\Contrib\Command;

use \Cute\Shell\Command;
use \Cute\Cache\TSVCache;
use \Cute\Contrib\GEO\PhoneLoc;


class PhoneLocWriter extends PhoneLoc
{
    use \Cute\Utility\BinaryWriter;
}


class PhoneLocCommand extends Command
{
    protected $dat = null;
    protected $idat = null;

    protected function openFiles($filename)
    {
        $this->dat = new PhoneLocWriter($filename);
        $version = date('Y.m');
        $this->dat->initiate(true);
        $this->dat->writeHeaders($version);
        $this->idat = new PhoneLocWriter(); //临时文件用于存放索引数据
        $this->idat->initiate(true);
    }

    protected function closeFiles()
    {
        $this->dat->writeHeaders();
        $this->idat->close();
        $this->dat->close();
    }

    protected function writeData(& $data)
    {
        $last_pos = 0;
        $start_item = '';
        $stop_item = '';
        $records = [];
        foreach ($data as $i => $row) {
            $words = implode(' ', array_slice($row, 1));
            if (!isset($records[ $words ])) {
                $position = $this->dat->tell();
                $this->dat->writeString($words);
                $records[ $words ] = $position;
            } else {
                $position = $records[ $words ];
            }
            if ($last_pos !== $position) {
                if ($start_item) {
                    $this->idat->writeTel($start_item);
                    $this->idat->writeTel($stop_item);
                    $this->idat->writeOffset($last_pos);
                }
                $last_pos = $position;
                $start_item = $row[0];
            }
            $stop_item = $row[0];
        }
        $this->idat->writeTel($start_item);
        $this->idat->writeTel($stop_item);
        $this->idat->writeOffset($last_pos);
        $this->dat->appendIndexes($this->idat);
    }

    public function execute()
    {
        $source = CUTE_ROOT . '/misc/mobile.csv';
        $cache = new TSVCache(basename($source, '.csv'), dirname($source));
        $data = $cache->initiate()->readData(2);
        $this->openFiles(CUTE_ROOT . '/misc/phoneloc.dat');
        $this->writeData($data);
        $this->closeFiles();
        $this->app->writeln('DONE');
    }
}


