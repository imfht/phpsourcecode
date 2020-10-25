<?php

/**
 * @see https://github.com/FriendsOfPHP/Sami
 *
 *  $ sudo curl -fsSL http://get.sensiolabs.org/sami.phar -o /usr/local/bin/sami
 *  $ sudo chmod +x /usr/local/bin/sami
 *
 *  $ sami update .sami.php
 *  $ cd build ; php -S 0.0.0.0:8080
 *
 */

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Module')
    ->exclude('Error')
    ->in($dir = __DIR__.'/src');

$versions = GitVersionCollection::create($dir)
    ->addFromTags('18.*.*')// add tag
    ->add('master', 'master branch'); // add branch

return new Sami($iterator);

return new Sami($iterator, [
        'versions' => $versions,
        'title' => 'Tencent AI SDK API',
        'build_dir' => __DIR__.'/build/%version%',
        'cache_dir' => __DIR__.'/cache/%version%',
        'remote_repository' => new GitHubRemoteRepository('khs1994-php/tencent-ai', __DIR__),
    ]
);
