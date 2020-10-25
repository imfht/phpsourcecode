<?php


namespace WxSDK\core\model\card;


use WxSDK\core\model\Model;

class DateInfo extends Model
{
    /**
     * @var string	必填。	DATE_TYPE_FIX_TIME_RANGE 表示固定日期区间，DATETYPE FIX_TERM 表示固定时长 （自领取后按天算。	使用时间的类型，旧文档采用的1和2依然生效。
     */
    public $type;
    /**
     * @var int	unsigned,必填。type为DATE_TYPE_FIX_TIME_RANGE时专用，表示起用时间。从1970年1月1日00:00:00至起用时间的秒数，最终需转换为字符串形态传入。（东八区时间,UTC+8，单位为秒）
     */
    public $begin_timestamp;
    /**
     * @var int	必填。	type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天内有效，不支持填写0。
     */
    public $fixed_term;
    /**
     * @var int	必填。type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天开始生效，领取后当天生效填写0。（单位为天）
     */
    public $fixed_begin_term;
    /**
     * @var int	unsigned 必填/非必填。type为DATE_TYPE_FIX_TIME_RANGE时为必填；
     * 也可用于DATE_TYPE_FIX_TERM时间类型，表示卡券统一过期时间 ， 建议设置为截止日期的23:59:59过期 。
     * （ 东八区时间,UTC+8，单位为秒 ），设置了fixed_term卡券，当时间达到end_timestamp时卡券统一过期
     */
    public $end_timestamp;

    /**
     * DateInfo constructor.
     * @param string $type DATE_TYPE_FIX_TIME_RANGE 表示固定日期区间，DATETYPE FIX_TERM 表示固定时长 （自领取后按天算。	使用时间的类型，旧文档采用的1和2依然生效。
     * @param int $begin_timestamp type为DATE_TYPE_FIX_TIME_RANGE时专用，表示起用时间。从1970年1月1日00:00:00至起用时间的秒数，最终需转换为字符串形态传入。（东八区时间,UTC+8，单位为秒）
     * @param int $fixed_term type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天内有效，不支持填写0。
     * @param int $fixed_begin_term type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天开始生效，领取后当天生效填写0。（单位为天）
     * @param int $end_timestamp 必填/非必填。type为DATE_TYPE_FIX_TIME_RANGE时为必填；
     * 也可用于DATE_TYPE_FIX_TERM时间类型，表示卡券统一过期时间 ， 建议设置为截止日期的23:59:59过期 。
     * （ 东八区时间,UTC+8，单位为秒 ），设置了fixed_term卡券，当时间达到end_timestamp时卡券统一过期
     */
    public function __construct(string $type, int $begin_timestamp,int $end_timestamp = null, int $fixed_term = null, int $fixed_begin_term = null)
    {
        $this->type = $type;
        $this->begin_timestamp = $begin_timestamp;
        $this->fixed_term = $fixed_term;
        $this->fixed_begin_term = $fixed_begin_term;
        $this->end_timestamp = $end_timestamp;
    }


}