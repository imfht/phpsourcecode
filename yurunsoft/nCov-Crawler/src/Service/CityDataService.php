<?php
namespace ImiApp\Service;

use Imi\Db\Db;
use ImiApp\Model\CityData;
use ImiApp\Enum\CountryType;
use Imi\Bean\Annotation\Bean;
use Imi\Aop\Annotation\Inject;

/**
 * @Bean("CityDataService")
 */
class CityDataService
{
    /**
     * @Inject("AreaDataService")
     *
     * @var \ImiApp\Service\AreaDataService
     */
    protected $areaDataService;

    /**
     * 根据最后修改记录
     *
     * @param string $provinceName
     * @param string $cityName
     * @return \ImiApp\Model\CityData|null
     */
    public function getLastModify(string $provinceName, string $cityName): ?CityData
    {
        return CityData::query()->whereEx([
            'province_name' =>  $provinceName,
            'city_name'     =>  $cityName,
        ])->order('modify_time', 'desc')->limit(1)->select()->get();
    }

    /**
     * 使用数据创建记录
     *
     * @param string $provinceName
     * @param array $data
     * @return \ImiApp\Model\CityData
     */
    public function createByData(string $provinceName, array $data): CityData
    {
        $parent = $this->areaDataService->getByName(CountryType::CHINA, $provinceName);
        if(!$parent)
        {
            throw new \RuntimeException(sprintf('Not found %s', $provinceName));
        }
        $record = CityData::newInstance($data);
        $record->parentId = $parent->id;
        $record->provinceName = $provinceName;
        $record->modifyTime = (int)(microtime(true) * 1000);
        $record->save();
        return $record;
    }

    /**
     * 根据最后修改记录
     *
     * @return \ImiApp\Model\CityData[]
     */
    public function selectLastModify(): array
    {
        static $sql = <<<SQL
SELECT
	* 
FROM
	( SELECT * FROM tb_city_data ORDER BY modify_time DESC LIMIT 1000 ) a 
GROUP BY
    parent_id, city_name
SQL;
        return Db::query()->execute($sql)->getArray();
    }

    /**
     * 根据日期跨度查询
     *
     * @param int $parentId
     * @param string $cityName
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectAreasDateSpan(int $parentId, string $cityName, string $beginDate, string $endDate): array
    {
        $timeList = $this->selectTimeListByDateSpan($parentId, $cityName, $beginDate, $endDate);
        if(!$timeList)
        {
            return [];
        }
        $list = CityData::dbQuery()->fieldRaw('modify_time,confirmed_count,suspected_count,cured_count,dead_count')
                                     ->where('parent_id', '=', $parentId)
                                     ->where('city_name', '=', $cityName)
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
     * @param int $parentId
     * @param string $cityName
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectTimeListByDateSpan(int $parentId, string $cityName, string $beginDate, string $endDate): array
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
		tb_city_data a 
	WHERE
        `parent_id` = :parentId
        AND `city_name` = :cityName
		AND a.modify_time >= :beginTimestamp
		AND a.modify_time <= :endTimestamp
	) aa 
GROUP BY
	aa.date
SQL;
        $result = Db::query()->bindValues([
            ':parentId'         =>  $parentId,
            ':cityName'         =>  $cityName,
            ':beginTimestamp'   =>  strtotime($beginDate) * 1000,
            ':endTimestamp'     =>  strtotime($endDate . ' 23:59:59') * 1000,
        ])->execute($sql);
        return $result->getColumn();
    }

}
