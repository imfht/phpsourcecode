<?php
namespace ImiApp\Service;

use Imi\Db\Db;
use ImiApp\Model\AreaData;
use ImiApp\Enum\CountryType;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;

/**
 * @Bean("AreaDataService")
 */
class AreaDataService
{
    /**
     * @Inject("CityDataService")
     *
     * @var \ImiApp\Service\CityDataService
     */
    protected $cityDataService;

    /**
     * 根据修改时间获取记录
     *
     * @param int $id
     * @param int $time
     * @return \ImiApp\Model\AreaData|null
     */
    public function getByModifyTime(int $id, int $time): ?AreaData
    {
        return AreaData::find([
            'id'            =>  $id,
            'modify_time'   =>  $time,
        ]);
    }

    /**
     * 使用数据创建记录
     *
     * @param array $data
     * @return \ImiApp\Model\AreaData
     */
    public function createByData(array $data): AreaData
    {
        $record = AreaData::newInstance($data);
        $record->save();
        return $record;
    }

    /**
     * 使用名称获取记录
     *
     * @param integer $countryType
     * @param string $name
     * @return \ImiApp\Model\AreaData|null
     */
    public function getByName(int $countryType, string $name): ?AreaData
    {
        return AreaData::find([
            'country_type'  =>  $countryType,
            'province_name' =>  $name,
        ]);
    }

    /**
     * 查询最新数据
     *
     * @param bool $withCity
     * @return array
     */
    public function selectLastModify(bool $withCity): array
    {
        static $sql = <<<SQL
SELECT
	* 
FROM
	( SELECT * FROM tb_area_data ORDER BY modify_time DESC LIMIT 1000 ) a 
GROUP BY
	province_name 
ORDER BY
	sort;
SQL;
        $list = Db::query()->execute($sql)->getArray();
        if($withCity)
        {
            $cities = $this->cityDataService->selectLastModify();
        }
        $china = $foreign = [];
        foreach($list as $item)
        {
            if($withCity)
            {
                $itemCities = [];
                foreach($cities as $cityItem)
                {
                    if($cityItem['parent_id'] == $item['id'] && $cityItem['province_name'] == $item['province_name'])
                    {
                        $itemCities[] = $cityItem;
                    }
                }
                $item['cities'] = $itemCities;
            }
            switch($item['country_type'])
            {
                case CountryType::CHINA:
                    $china[] = $item;
                    break;
                case CountryType::FOREIGN:
                    $foreign[] = $item;
                    break;
            }
        }
        return [
            'china'     =>  $china,
            'foreign'   =>  $foreign,
        ];
    }

    /**
     * 根据日期跨度查询
     *
     * @param int $countryType
     * @param string $provinceName
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectAreasDateSpan(int $countryType, string $provinceName, string $beginDate, string $endDate): array
    {
        $timeList = $this->selectTimeListByDateSpan($countryType, $provinceName, $beginDate, $endDate);
        if(!$timeList)
        {
            return [];
        }
        $list = AreaData::dbQuery()->fieldRaw('modify_time,confirmed_count,suspected_count,cured_count,dead_count')
                                     ->where('country_type', '=', $countryType)
                                     ->where('province_name', '=', $provinceName)
                                     ->whereIn('modify_time', $timeList)
                                     ->order('modify_time')
                                     ->select()
                                     ->getArray();
        foreach($list as &$item)
        {
            $item['date'] = date('Y-m-d', $item['modify_time'] / 1000);
        }
        return $list;
    }

    /**
     * 根据日期跨度查询
     *
     * @param int $countryType
     * @param string $provinceName
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectTimeListByDateSpan(int $countryType, string $provinceName, string $beginDate, string $endDate): array
    {
        static $sql = <<<SQL
SELECT
	max( aa.modify_time ) AS modify_time
FROM
	(
	SELECT
		FROM_UNIXTIME( modify_time / 1000, '%Y-%m-%d' ) date,
		a.modify_time 
	FROM
		tb_area_data a 
	WHERE
        `country_type` = :countryType
        AND `province_name` = :provinceName
		AND a.modify_time >= :beginTimestamp
		AND a.modify_time <= :endTimestamp
	) aa 
GROUP BY
	aa.date
SQL;
        $result = Db::query()->bindValues([
            ':countryType'      =>  $countryType,
            ':provinceName'     =>  $provinceName,
            ':beginTimestamp'   =>  strtotime($beginDate) * 1000,
            ':endTimestamp'     =>  strtotime($endDate . ' 23:59:59') * 1000,
        ])->execute($sql);
        return $result->getColumn();
    }

}
