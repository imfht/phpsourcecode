<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5
 */
class PDFCore
{
	public $filename;
	public $pdf_renderer;
	public $objects;
	public $template;

	const TEMPLATE_INVOICE = 'Invoice';
	const TEMPLATE_ORDER_RETURN = 'OrderReturn';
	const TEMPLATE_ORDER_SLIP = 'OrderSlip';
	const TEMPLATE_DELIVERY_SLIP = 'DeliverySlip';
	const TEMPLATE_SUPPLY_ORDER_FORM = 'SupplyOrderForm';

	public function __construct($objects, $template, $smarty)
	{
		$this->pdf_renderer = new PDFGenerator((bool)Configuration::get('PS_PDF_USE_CACHE'));
		$this->template = $template;
		$this->smarty = $smarty;

		$this->objects = $objects;
		if (!($objects instanceof Iterator) && !is_array($objects))
			$this->objects = array($objects);
	}

	public function render($display = true)
	{
		$render = false;
		$this->pdf_renderer->setFontForLang('zh');
		foreach ($this->objects as $object)
		{
			$template = $this->getTemplateObject($object);
			if (!$template)
				continue;

			if (empty($this->filename))
			{
				$this->filename = $template->getFilename();
				if (count($this->objects) > 1)
					$this->filename = $template->getBulkFilename();
			}

			$template->assignHookData($object);

			$this->pdf_renderer->createHeader($template->getHeader());
			$this->pdf_renderer->createFooter($template->getFooter());
			$this->pdf_renderer->createContent($template->getContent());
			$this->pdf_renderer->writePage();
			$render = true;

			unset($template);
		}

		if ($render)
			return $this->pdf_renderer->render($this->filename, $display);
	}

	public function getTemplateObject($object)
	{
		$class = false;
		$classname = 'HTMLTemplate'.$this->template;

		if (class_exists($classname))
		{
			$class = new $classname($object, $this->smarty);
			if (!($class instanceof HTMLTemplate))
				throw new MileBizException('Invalid class. It should be an instance of HTMLTemplate');
		}

		return $class;
	}
}