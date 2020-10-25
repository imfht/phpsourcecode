<?php
/**
 * @since   2016-09-03
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


class ReturnCode {
    const SUCCESS = 0;
    const INVALID = -1;
    const DB_SAVE_ERROR = -2;
    const DB_READ_ERROR = -3;
    const CACHE_SAVE_ERROR = -4;
    const CACHE_READ_ERROR = -5;
    const FILE_SAVE_ERROR = -6;
    const LOGIN_ERROR = -7;
    const NOT_EXISTS = -8;
    const JSON_PARSE_FAIL = -9;
    const TYPE_ERROR = -10;
    const NUMBER_MATCH_ERROR = -11;
    const EMPTY_PARAMS = -12;
    const LOW_PARAM = -13;
    const OUT_RANGE = -14;
    const REACH_LIMIT = -15;
    const DATA_EXISTS = -16;
    const ONLINE_PROTECT = -17;
    const ATTACK_LIMIT = -18;

    const OTHER_LOGIN = -19;
    const VERSION_INVALID = -20;
    const MAINTENANCE = -21;

    const USERNAME_INVALID = -22;

    const SESSION_TIMEOUT = -997;
    const UNKNOWN = -998;
    const EXCEPTION = -999;
}