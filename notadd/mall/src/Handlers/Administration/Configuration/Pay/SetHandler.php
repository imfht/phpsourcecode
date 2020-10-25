<?php
/**
 * This file is part of Notadd.
 *
 * @author TwilRoad <heshudong@ibenchu.com>
 * @copyright (c) 2017, notadd.com
 * @datetime 2017-05-03 16:09
 */
namespace Notadd\Mall\Handlers\Administration\Configuration\Pay;

use Illuminate\Container\Container;
use Notadd\Foundation\Routing\Abstracts\Handler;
use Notadd\Foundation\Setting\Contracts\SettingsRepository;

/**
 * Class SetHandler.
 */
class SetHandler extends Handler
{
    /**
     * @var \Notadd\Foundation\Setting\Contracts\SettingsRepository
     */
    protected $settings;

    /**
     * SetHandler constructor.
     *
     * @param \Illuminate\Container\Container                         $container
     * @param \Notadd\Foundation\Setting\Contracts\SettingsRepository $settings
     */
    public function __construct(Container $container, SettingsRepository $settings)
    {
        parent::__construct($container);
        $this->settings = $settings;
    }

    /**
     * Execute Handler.
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->withCode(200)->withMessage('');
    }
}
