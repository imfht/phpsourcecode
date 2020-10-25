<?php

namespace App\Http\Controllers\Api\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


trait ProxyTrait
{
    public function authenticate($guard = '')
    {
        $client = new Client();

        try {
            $url = request()->root() . '/api/oauth/token';

            if ($guard) {
                $params = array_merge(config('passport.proxy'), [
                    'username' => request('email'),
                    'password' => request('password'),
                    'provider' => $guard
                ]);
            } else {
                $params = array_merge(config('passport.proxy'), [
                    'username' => request('email'),
                    'password' => request('password'),
                ]);
            }


            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            abort(401, '请求失败，服务器错误');
        }

        if ($respond->getStatusCode() !== 401) {
            return json_decode($respond->getBody()->getContents(), true);
        }
        abort(401, '账号或密码错误');

    }

    public function getRefreshtoken()
    {
        $client = new Client();

        try {
            $url = request()->root() . '/api/oauth/token';

            $params = array_merge(config('passport.refresh_token'), [
                'refresh_token' => request('refresh_token'),
            ]);

            $respond = $client->request('POST', $url, ['form_params' => $params]);
        } catch (RequestException $exception) {
            abort(401, '请求失败，服务器错误');
        }

        if ($respond->getStatusCode() !== 401) {
            return json_decode($respond->getBody(), true);
        }
        abort(401, '不正确的 refresh_token');

    }
}
