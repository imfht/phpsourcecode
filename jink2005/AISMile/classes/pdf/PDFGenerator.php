<?php

require_once(_PS_TOOL_DIR_.'tcpdf/config/lang/eng.php');
require_once(_PS_TOOL_DIR_.'tcpdf/tcpdf.php');

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
class PDFGeneratorCore extends TCPDF
{
	const DEFAULT_FONT = 'helvetica';

	public $header;
	public $footer;
	public $content;
	public $font;

	public $font_by_lang = array('zh' => 'cnsong');


	public function __construct($use_cache = false)
	{
		parent::__construct('P', 'mm', 'A4', true, 'UTF-8', $use_cache, false);
	}

	/**
	 * set the PDF encoding
	 * @param string $encoding
	 */
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 *
	 * set the PDF header
	 * @param string $header HTML
	 */
	public function createHeader($header)
	{
		$this->header = $header;
	}

	/**
	 *
	 * set the PDF footer
	 * @param string $footer HTML
	 */
	public function createFooter($footer)
	{
		$this->footer = $footer;
	}

	/**
	 *
	 * create the PDF content
	 * @param string $content HTML
	 */
	public function createContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Change the font
	 * @param string $iso_lang
	 */
	public function setFontForLang($iso_lang)
	{
		$this->font = PDFGenerator::DEFAULT_FONT;
		if (array_key_exists($iso_lang, $this->font_by_lang))
			$this->font = $this->font_by_lang[$iso_lang];

		$this->setHeaderFont(array($this->font, '', PDF_FONT_SIZE_MAIN));
		$this->setFooterFont(array($this->font, '', PDF_FONT_SIZE_MAIN));
		$this->setFont($this->font);
	}

	/**
	 * @see TCPDF::Header()
	 */
	public function Header()
	{
		$this->writeHTML($this->header);
	}

	/**
	 * @see TCPDF::Footer()
	 */
	public function Footer()
	{
		$this->writeHTML($this->footer);
	}

	/**
	 * Render the pdf file
	 *
	 * @param string $filename
     * @param boolean $inline
	 * @throws MileBizException
	 */
	public function render($filename, $display = true)
	{
		if (empty($filename))
			throw new MileBizException('Missing filename.');

		$this->lastPage();

		$output = $display ? 'I' : 'S';
		return $this->output($filename, $output);
	}

	/**
	 * Write a PDF page
	 */
	public function writePage()
	{
		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(18);
		$this->setMargins(10, 40, 10);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		$this->AddPage();

		$this->writeHTML($this->content, true, false, true, false, '');
	}
}