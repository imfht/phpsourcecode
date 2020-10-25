<?php
namespace ImiApp\Service;

use Imi\Bean\Annotation\Bean;
use Imi\Db\Db;
use Imi\Util\Text;
use ImiApp\Model\Statistics;

/**
 * @Bean("StatisticsService")
 */
class StatisticsService
{
    /**
     * 根据修改时间获取记录
     *
     * @param int $time
     * @return \ImiApp\Model\Statistics|null
     */
    public function getByModifyTime(int $time): ?Statistics
    {
        return Statistics::find([
            'modify_time'   =>  $time,
        ]);
    }

    /**
     * 使用数据创建记录
     *
     * @param array $data
     * @return \ImiApp\Model\Statistics
     */
    public function createByData(array $data): Statistics
    {
        $record = Statistics::newInstance($data);
        $record->save();
        return $record;
    }

    /**
     * 获取最新记录
     *
     * @return \ImiApp\Model\Statistics|null
     */
    public function getLastModify(): ?Statistics
    {
        return Statistics::query()->order('modify_time', 'desc')->limit(1)->select()->get();
    }

    /**
     * 根据日期跨度查询
     *
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectByDateSpan(string $beginDate, string $endDate): array
    {
        $timeList = $this->selectTimeListByDateSpan($beginDate, $endDate);
        if(!$timeList)
        {
            return [];
        }
        $list = Statistics::dbQuery()->fieldRaw('modify_time,confirmed_count,suspected_count,cured_count,dead_count')
                                     ->whereIn('modify_time', $timeList)
                                     ->order('modify_time')
                                     ->select()
                                     ->getArray();
        if(!$timeList)
        {
            return [];
        }
        $result = [];
        foreach($list as $item)
        {
            $newItem = [];
            foreach($item as $k => $v)
            {
                $newItem[Text::toCamelName($k)] = $v;
            }
            $newItem['date'] = date('Y-m-d', $item['modify_time'] / 1000);
            $result[] = $newItem;
        }
        return $result;
    }

    /**
     * 根据日期跨度查询
     *
     * @param string $beginDate
     * @param string $endDate
     * @return array
     */
    public function selectTimeListByDateSpan(string $beginDate, string $endDate): array
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
		tb_statistics a 
	WHERE
		a.modify_time >= :beginTimestamp
		AND a.modify_time <= :endTimestamp
	) aa 
GROUP BY
	aa.date
SQL;
        $result = Db::query()->bindValues([
            ':beginTimestamp'   =>  strtotime($beginDate) * 1000,
            ':endTimestamp'     =>  strtotime($endDate . ' 23:59:59') * 1000,
        ])->execute($sql);
        return $result->getColumn();
    }

}
