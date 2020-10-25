<?php
namespace system\services\common\test;

use system\datalevels\DaoType;
use workerbase\classs\datalevels\DaoFactory;
use workerbase\traits\Tools;

/**
 * 业务测试
 * @author fukaiyao
 */
class Test
{
    use Tools;

    /**
     * 测试数据接口
     * @var
     */
    private $_testDao;

    /**
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->_testDao = DaoFactory::getDao(DaoType::COMMON_TEST);
    }

    public function test($msg)
    {
        $data = $this->rsaEncrypt(
            'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqWmpFXJ8LFRWN9OitbH2fyafESkuQWDDbDjyV1RQhaapOb5Ny0OEEVQXyuFeC6l6+m0VU71y2xjsUTAvZhz7UAk6N5cwbRVY4Wc7cLmmVhjkF71r+mIBbDrkdb39QXFP2VQvz+Iddpj+JlhbUvmyIbtSgGo4loNaxeQoSNVHiopnFfMJC220yyhzetvLg2X430sjXny3ue94neB4NwkGf8DsHn6Gm+nhNqRyOjn2owqv7BlphPeKUy36xpoaAFV3xZY6B1feaTrx3deZX3uU+SSTAa6Rc5pj7q3TdD30z7+W7/reuaKOP+iGSZNI1wKHUum+iNatPKESFCT0Gmk4pQIDAQAB',
            $msg
        );
        $data = $this->rsaDecrypt('MIIEpAIBAAKCAQEAqWmpFXJ8LFRWN9OitbH2fyafESkuQWDDbDjyV1RQhaapOb5Ny0OEEVQXyuFeC6l6+m0VU71y2xjsUTAvZhz7UAk6N5cwbRVY4Wc7cLmmVhjkF71r+mIBbDrkdb39QXFP2VQvz+Iddpj+JlhbUvmyIbtSgGo4loNaxeQoSNVHiopnFfMJC220yyhzetvLg2X430sjXny3ue94neB4NwkGf8DsHn6Gm+nhNqRyOjn2owqv7BlphPeKUy36xpoaAFV3xZY6B1feaTrx3deZX3uU+SSTAa6Rc5pj7q3TdD30z7+W7/reuaKOP+iGSZNI1wKHUum+iNatPKESFCT0Gmk4pQIDAQABAoIBAEWPQcKxoDSfaEtB1XQfHyP0Gqn0K67iaTsdYrvivbEyzhcMgWqtTSPEUISX5oKJUxpSAcjBZ9B4OkfXrg6SZcnmEAZVSKfxdO4P8gMF5ztAux7YQuaqqQTkZXvGx57ARNXqUDteD1Tr2qap7s1yAucAwA5EDvoV8wZ/+N523AoQv8mynQYWY3twMFjlKulaqOi3A7ElsADH4U+1424QCt0vtulHrccqC9pg1cbsS887eUO8FawgVLXWh9f1bebzB7LnUG20uB48R0QdEmRv7mE8jXxS46jgJzcWeEoXhdfXnGAUgpWxRjfOCmJ4JfdAnK13GalfBEhGj/NQwW5dQ0ECgYEA6R4JqM75JRzZp3cwEVG2+DzoMmjDMILle6tyvlSx/2VCQBz6eI10O7+LuC2oi8o0mzFTtVgDJYHoR2aOSN8KZs588WKhohsRBD6E8q35EUXCFmLpoCwrAG7hiWlkMD5FhCk//oyFm8o5EHGuGYrHl3hjuPq2Cvqx9g4MF5FXAdECgYEAugrNPM0RGeoTNMRmnP32aJ0iv95RWnpUVecMa5Qf5b8wFF6cGscP46kme1ts1LCQDnopl5hLxIz45C89M/tQ2CaluDVkd+ObuXciaaPfs41g9S/AjpGYUKj8MiOWcOv0s2zKPkXUorAYmiu0W2r0FxijuzY8IZpkd2cP3TNDCpUCgYEA45qbTcE/Clg/vj0lplNFNNuqzcTxhoTW8Ec2EdT5sWU5KQXiGx/pM4jSLvINVOcJM9kWZMFY2R8cHdJo64cxTa0f2kI1k+OfWqh7/8GSo6WbWWYbunJFTff0pshKtLun/eCUhcDHlpL74i1MEc4pD5/QpcPLR677YETY043pCHECgYEAoT7vd709Dzrj/p4jWfp78VwQXD/yPvs70WB6UVuG8fftUhpWLpdN3EIlSlGJWCbYFNQo7G1hbi/JIO0YnM872LxWcfxE4exyciMhvnH8V4E4AgqrWGY0n+R3AXX61FCOPF0URTj8/SynhihPH9TpToNalc6B+5X3cc3v4AaoGqkCgYBRH6ITaMojgz3gpWYXXDzyJkfJN+iySC8+YZ0JVkrYloFWdDP3gzfInnRiOXOAuQi8o+jJfAEsxaKiXwFn3DnVPaI2KmWprU7MwssykS2FBFLbMeZ/zHfZ7oJItVhYifYKmbYW2SSQmabFeH1hTiP8vXTm91ECx8iIHorgTcIWow==',$data);
        return $data;
    }

    /**
     *  获取一条记录
     * @param mixed $id - 主键id
     * @param mixed $fields - 返回字段，多个字段逗号分隔, 为空返回全部 (支持以数组的形式传递字段)
     * @param boolean $isLock         - 是否对读取的数据强制加上for update
     * @return null | array            - 找到返回一条记录(详细字段请参考对应的表)，找不到返回null
     */
    public function getInfoById($id, $fields = null, $isLock = false)
    {
        return $this->_testDao->getInfoById($id, $fields, $isLock);
    }

    /**
     * 添加一条记录
     * @param array $info         - 详细字段参考对应表字段
     * @return boolean | int      - 添加成功返回自增ID(不存在自增id返回0，多条插入返回true)，失败返回false
     */
    public function add($info)
    {
        return $this->_testDao->add($info);
    }

}