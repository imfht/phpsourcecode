<?php
namespace JWT;

use Firebase\JWT\JWT as FJwt;

class Jwt
{
    const RS256 = 'RS256';
    const HS256 = 'HS256';

    /**
     * @param $leeway
     */
    public static function setLeeway($leeway)
    {
        FJwt::$leeway = $leeway;
    }

    /**
     * @param $Jwt
     * @param $jwt_key
     * @param null $err_info
     * @return bool|object
     */
    public static function JwtHS256Check($Jwt, $jwt_key, &$err_info = null)
    {
        try {
            if (empty($Jwt)) {
                throw new \Exception('token is null');
            }
            $decoded = FJwt::decode($Jwt, $jwt_key, array(self::HS256));
            if (empty($decoded)) {
                throw new \Exception('payload is null');
            }
            return $decoded;
        } catch (\Exception $e) {
            $err_info = $e->getMessage();
            return false;
        }
    }

    /**
     * @param $Jwt
     * @param $publicKey
     * @param array $err_info
     * @return bool|object
     */
    public static function JwtRS256Check($Jwt, $publicKey, &$err_info = null)
    {
        try {
            if (empty($Jwt)) {
                throw new \Exception('token is null');
            }
            $decoded = FJwt::decode($Jwt, $publicKey, array(self::RS256));
            if (empty($decoded)) {
                throw new \Exception('payload is null');
            }
            return $decoded;
        } catch (\Exception $e) {
            $err_info = $e->getMessage();
            return false;
        }
    }

    /**
     * @param $payload
     * @param $jwt_key
     * @param string $alg
     * @return string
     */
    public static function createJwt($payload, $jwt_key, $alg = self::HS256)
    {
        if (empty($jwt_key)) {
            return false;
        }
        switch ($alg) {
            case self::RS256:
                $jwk = FJwt::encode($payload, $jwt_key, self::RS256);
                break;
            default:
                $jwk = FJwt::encode($payload, $jwt_key);
                break;
        }

        return $jwk;
    }
}
