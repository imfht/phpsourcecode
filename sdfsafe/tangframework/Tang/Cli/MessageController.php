<?php
namespace Tang\Cli;
use Tang\Services\ConfigService;
use Tang\Services\I18nService;
use Tang\Services\RequestService;
use Tang\Web\Parameters;
use Tang\Web\View\ViewService;

class MessageController extends CliController
{
	public static function create()
	{
		$instance = new self();
		$instance->request = RequestService::getService();
		$instance->config = ConfigService::getService();
		$instance->i18n = I18nService::getService();
		$instance->setParameters(new Parameters('','',''));
		$instance->view = ViewService::getService();
		return $instance;
	}
}