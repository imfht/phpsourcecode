<?php

declare(strict_types=1);

namespace App\Http\Controllers\OAuth;

use App\User;
use Exception;
use PCIT\Coding\Service\OAuth\Client as CodingClient;
use PCIT\Framework\Attributes\Route;
use PCIT\Gitee\Service\OAuth\Client as GiteeClient;
use PCIT\GitHub\Service\OAuth\Client as GitHubClient;
use PCIT\PCIT;

class IndexController
{
    /**
     * @var CodingClient|GiteeClient|GitHubClient
     */
    protected static $oauth;

    /**
     * @var string
     */
    protected static $git_type;

    /**
     * enable state.
     *
     * @var bool
     */
    protected $state = false;

    /**
     * @param mixed $git_type
     *
     * @throws \Exception
     */
    public function bootstrap($git_type): void
    {
        $pcit = app(PCIT::class)->git($git_type);

        static::$oauth = $pcit->oauth;
        static::$git_type = $git_type;

        $this->state = static::$oauth->state ?? false;
    }

    /**
     * 获取登录的 URL.
     */
    #[Route('get', 'oauth/{git_type}/login')]
    public function getLoginUrl(string $git_type): void
    {
        /*
         * logout -> unset access_token
         *
         * OAuth login -> get access_token and expire from Session | expire one day
         */
        if (\Session::get($git_type.'.access_token')
        and \Session::get($git_type.'.expire') > time()) {
            $username_from_session = \Session::get($git_type.'.username');

            // 重定向到个人主页
            \Response::redirect(implode('/', ['/profile', $git_type, $username_from_session]));
        }

        $state = session_create_id();

        \Session::put($git_type.'.state', $state);

        $this->bootstrap($git_type);

        $url = static::$oauth->getLoginUrl($state);

        if ('' === $url) {
            if ('github' === static::$git_type) {
                throw new Exception('GitHub App not set, you can create new GitHub App by click '.config('app.host').'/api/github/app/new', 500);
            }

            throw new Exception(static::$git_type.' OAuth App not set', 500);
        }

        // 重定向到登录 url
        \Response::redirect($url);

        exit;
    }

    /**
     * 服务器重定向页面.
     *
     * @throws \Exception
     */
    #[Route('get', 'oauth/${git_type}')]
    public function getAccessToken(string $git_type): void
    {
        $this->bootstrap($git_type);

        if ($this->state ?? false) {
            $state = \Session::pull(static::$git_type.'.state');
            $this->getAccessTokenCommon($state);

            return;
        }

        $this->getAccessTokenCommon(null);
    }

    /**
     * @throws \Exception
     */
    public function getAccessTokenCommon(?string $state): void
    {
        $git_type = static::$git_type;

        $request = app('request');

        // $code = $_GET['code'] ?? false;
        $code = $request->query->get('code');

        if (!$code) {
            throw new Exception('code not found');
        }

        try {
            list($accessToken, $refreshToken) = static::$oauth->getAccessToken((string) $code, $state);

            $accessToken && \Session::put($git_type.'.access_token', $accessToken);

            $pcit = app(PCIT::class)->git($git_type, $accessToken);

            $userInfoArray = $pcit->user_basic_info->getUserInfo();
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), 500);
        }

        $uid = $userInfoArray['uid'];
        $name = $userInfoArray['name'];
        $pic = $userInfoArray['pic'];
        $email = $userInfoArray['email'];

        $this->handleRefreshToken((int) $uid, $refreshToken, $git_type);

        \Session::put($git_type.'.uid', $uid);
        \Session::put($git_type.'.username', $name);
        \Session::put($git_type.'.pic', $pic);
        \Session::put($git_type.'.email', $email);
        $remember_day = config('session.remember_day');
        \Session::put($git_type.'.expire', time() + $remember_day * 24 * 60 * 60);

        \Response::redirect(config('app.host').'/profile/'.$git_type.'/'.$name);

        exit;
    }

    /**
     * @param $refreshToken
     *
     * @throws \Exception
     */
    public function handleRefreshToken(int $uid, $refreshToken, string $gitType): void
    {
        if (!$refreshToken) {
            return;
        }

        User::updateRefreshToken($uid, $refreshToken, $gitType);
    }
}
