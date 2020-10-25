<?php
namespace ImiApp\Module\Crawler\Service;

use Imi\Log\Log;
use Yurun\Util\HttpRequest;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;
use Imi\Db\Annotation\Transaction;

/**
 * @Bean("CrawlerService")
 */
class CrawlerService
{
    /**
     * @Inject("StatisticsService")
     *
     * @var \ImiApp\Service\StatisticsService
     */
    protected $statisticsService;

    /**
     * @Inject("AreaDataService")
     *
     * @var \ImiApp\Service\AreaDataService
     */
    protected $areaDataService;

    /**
     * @Inject("CityDataService")
     *
     * @var \ImiApp\Service\CityDataService
     */
    protected $cityDataService;

    /**
     * @Transaction
     *
     * @return void
     */
    public function run()
    {
        $content = $this->getPageContent();
        $this->parseStatistics($content);
        $this->parseProvince($content);
        $this->parseForeign($content);
        $this->parseCity($content);
    }

    /**
     * 获取页面内容
     *
     * @return string
     */
    public function getPageContent(): string
    {
        $http = new HttpRequest;
        $response = $http->get('https://3g.dxy.cn/newh5/view/pneumonia_peopleapp');
        return $response->body();
    }

    const STATISTICS_LEFT = <<<STR
<script id="getStatisticsService">try { window.getStatisticsService = 
STR;

    const STATISTICS_RIGHT = <<<STR
}catch(e)
STR;

    /**
     * 处理统计数据
     *
     * @param string $content
     * @return void
     */
    public function parseStatistics(string $content)
    {
        $result = preg_match('/' . preg_quote(self::STATISTICS_LEFT) . '(.+?)' . preg_quote(self::STATISTICS_RIGHT) . '/', $content, $matches);
        if($result <= 0 || !($data = json_decode($matches[1], true)))
        {
            Log::error(sprintf('parseStatistics failed'));
            return;
        }
        $record = $this->statisticsService->getByModifyTime($data['modifyTime']);
        if($record)
        {
            $canCreate = false;
            foreach($record as $k => $v)
            {
                if($data[$k] != $v)
                {
                    $canCreate = true;
                    break;
                }
            }
            if(!$canCreate)
            {
                return;
            }
        }
        $this->statisticsService->createByData($data);
    }

    const PROVINCE_LEFT = <<<STR
<script id="getListByCountryTypeService1">try { window.getListByCountryTypeService1 = 
STR;

    const PROVINCE_RIGHT = <<<STR
}catch(e)
STR;

    /**
     * 处理省
     *
     * @param string $content
     * @return void
     */
    public function parseProvince(string $content)
    {
        $result = preg_match('/' . preg_quote(self::PROVINCE_LEFT) . '(.+?)' . preg_quote(self::PROVINCE_RIGHT) . '/', $content, $matches);
        if($result <= 0 || !($dataList = json_decode($matches[1], true)))
        {
            Log::error(sprintf('parseProvince failed'));
            return;
        }
        foreach($dataList as $data)
        {
            $record = $this->areaDataService->getByModifyTime($data['id'], $data['modifyTime']);
            if($record)
            {
                $canCreate = false;
                foreach($record as $k => $v)
                {
                    if($data[$k] != $v)
                    {
                        $canCreate = true;
                        break;
                    }
                }
                if(!$canCreate)
                {
                    continue;
                }
            }
            $this->areaDataService->createByData($data);
        }
    }

    const FOREIGN_LEFT = <<<STR
<script id="getListByCountryTypeService2">try { window.getListByCountryTypeService2 = 
STR;

    const FOREIGN_RIGHT = <<<STR
}catch(e)
STR;

    /**
     * 处理外国
     *
     * @param string $content
     * @return void
     */
    public function parseForeign(string $content)
    {
        $result = preg_match('/' . preg_quote(self::FOREIGN_LEFT) . '(.+?)' . preg_quote(self::FOREIGN_RIGHT) . '/', $content, $matches);
        if($result <= 0 || !($dataList = json_decode($matches[1], true)))
        {
            Log::error(sprintf('parseForeign failed'));
            return;
        }
        foreach($dataList as $data)
        {
            $record = $this->areaDataService->getByModifyTime($data['id'], $data['modifyTime']);
            if($record)
            {
                $canCreate = false;
                foreach($record as $k => $v)
                {
                    if($data[$k] != $v)
                    {
                        $canCreate = true;
                        break;
                    }
                }
                if(!$canCreate)
                {
                    continue;
                }
            }
            $this->areaDataService->createByData($data);
        }
    }

    const CITY_LEFT = <<<STR
<script id="getAreaStat">try { window.getAreaStat = 
STR;
    
    const CITY_RIGHT = <<<STR
}catch(e)
STR;

    /**
     * 处理城市
     *
     * @param string $content
     * @return void
     */
    public function parseCity(string $content)
    {
        $result = preg_match('/' . preg_quote(self::CITY_LEFT) . '(.+?)' . preg_quote(self::CITY_RIGHT) . '/', $content, $matches);
        if($result <= 0 || !($dataList = json_decode($matches[1], true)))
        {
            Log::error(sprintf('parseCity failed'));
            return;
        }
        static $compareFields = [
            'confirmedCount',
            'suspectedCount',
            'curedCount',
            'deadCount',
        ];
        foreach($dataList as $dataItem)
        {
            foreach($dataItem['cities'] as $data)
            {
                $record = $this->cityDataService->getLastModify($dataItem['provinceName'], $data['cityName']);
                if($record)
                {
                    $canCreate = false;
                    foreach($compareFields as $fieldName)
                    {
                        if($record[$fieldName] != $data[$fieldName])
                        {
                            $canCreate = true;
                            break;
                        }
                    }
                    if(!$canCreate)
                    {
                        continue;
                    }
                }
                $this->cityDataService->createByData($dataItem['provinceName'], $data);
            }
        }
    }

}
