<?php
namespace Cute\Contrib\Command;

use \Cute\Shell\Command;
use \Cute\Cache\TSVCache;
use \Cute\Contrib\GEO\IPCountry;


class IPCountryWriter extends IPCountry
{
    use \Cute\Utility\BinaryWriter;
}


class IPCountryCommand extends Command
{
    protected $dat = null;
    protected $idat = null;

    protected function openFiles($filename)
    {
        $this->dat = new IPCountryWriter($filename);
        $version = date('Y.m');
        $this->dat->initiate(true);
        $this->dat->writeHeaders($version);
        $this->idat = new IPCountryWriter(); //临时文件用于存放索引数据
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
        $records = [];
        foreach ($data as $i => $row) {
            if (strpos($row[0], ':') !== false) { //IPv6
                break;
            }
            $words = $row[2];
            if (!isset($records[ $words ])) {
                $position = $this->dat->tell();
                $this->dat->writeString($words);
                $records[ $words ] = $position;
            } else {
                $position = $records[ $words ];
            }
            $this->idat->writeIP($row[0]);
            $this->idat->writeIP($row[1]);
            $this->idat->writeOffset($position);
        }
        $this->dat->appendIndexes($this->idat);
    }

    public function execute()
    {
        //$source = realpath(reset($this->args));
        $source = CUTE_ROOT . '/misc/dbip-country.csv';
        $cache = new TSVCache(basename($source, '.csv'), dirname($source), ',');
        $data = $cache->initiate()->readData(3);
        $this->openFiles(CUTE_ROOT . '/misc/ipcountry.dat');
        $this->writeData($data);
        $this->closeFiles();
        $this->app->writeln('DONE');
    }
}


