<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Utils;


use Ke\Web\Html;

class SemanticUI extends Html
{
	
	public $classForm = 'ui form';
	public $classTableList = 'ui celled compact striped single line selectable table';
	
	public $tagFieldSet = 'div';
	public $tagFieldSetLegend = '';
	public $tagFieldSetContent = 'div';
	public $tagFieldSetError = 'div';
	public $tagFieldSetInline = 'div';
	
	public $classFieldSet = 'field';
	public $classFieldSetLegend = 'field-legend';
	public $classFieldSetContent = 'field-content';
	
	public $classFieldSetRequire = 'required';
	public $classFieldSetError = 'error';
	public $classFieldSetFields = 'fields inline';
	
	public $tagButtons = 'div';
	public $classButtons = 'ui buttons';
	public $buttonsDelimiter = '';
	
	public $classButton = 'ui button';
	public $classButtonSubmit = 'ui button primary';
	public $classButtonReset = 'ui button secondary';
	
	public $classButtonLink = 'ui button';
	
	public $classMessage = 'ui message';
	public $classMessageWarning = 'ui warning message';
	public $classMessageError = 'ui error message';
	public $classMessageSuccess = 'ui success message';
	
	public $pageUsedKV = false;
	
	public $pageRow = '<div class="ui pagination menu">{first}{prev}{links}{next}{last}</div><div class="page-form">{current} / {total} {button}</div>';
	public $pageGoto = 'select';
	
	public $classPageWrap = 'page-box';
	public $classPageLink = 'item';
	public $classPageSpan = 'item disabled';
	public $classPageSpanActive = 'item active';
	public $classPageButton = 'ui button primary';
	public $classPageSpanEllipsis = 'item disable';
	
	public function labelInput(string $label, string $type, $attr = null)
	{
		$content = parent::labelInput($label, $type, $attr);
		if ($type === 'checkbox')
			$content = $this->tag('div', $content, 'ui ' . $type);
		else if ($type === 'radio')
			$content = $this->tag('div', $content, 'ui checkbox ' . $type);
		$content = $this->tag('div', $content, 'field');
		return $content;
	}
	
	public function fa(string $icon, $attr = null): string
	{
		return $this->tag('icon', null, $this->preIcon($icon, $attr));
	}
	
	public function icon(string $icon, $attr = null): string
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		$class = "{$icon} icon";
		$this->addClass($attr, $class);
		return $this->tag('i', null, $attr);
	}
	
	public function getAllMenus()
	{
		return [];
	}
	
	public function menu(array $data, $attr = null)
	{
		if (!is_array($attr))
			$attr = $this->attr2array($attr);
		
		$html = $this;
		
		$content = [];
		
		foreach ($data as $menu) {
			if (isset($menu['isHidden']) && $menu['isHidden'])
				continue;
			$tag = 'a';
			$class = ['item'];
			$menuAttr = [];
			
			$menuItem = '';
			$menuContent = [];
			
			if (!empty($menu['class']))
				$class[] = $menu['class'];
			
			$text = $menu['text'] ?? '';
			if (!empty($menu['icon']))
				$text = $html->icon($menu['icon']) . $text;
			
			if (!empty($menu['children'])) {
				$class = 'ui simple dropdown item';
				$content[] = $html->tag('div', [
					$html->link($text, $menu['link'] ?? '#', $menu['class'] ?? ''),
					$html->icon('dropdown'),
					$this->menu($menu['children'], $menu['class'] ?? null),
				], $this->mkMenuAttr($class));
			} else {
				
				if ($menu['type'] === 'link') {
					$menuItem = $html->link($text, $menu['link'] ?? '#', $this->mkMenuAttr($class));
				} else if ($menu['type'] === 'header') {
					if (!empty($menu['link'])) {
						$menuItem = $html->link($text, $menu['link'], $this->mkMenuAttr($class));
					} else {
						$class = 'header';
						$menuItem = $html->tag('div', $text, $this->mkMenuAttr($class));
					}
				} else if ($menu['type'] === 'divider') {
					$class = 'divider';
					$menuItem = $html->tag('div', '', $this->mkMenuAttr($class));
				}
				$content[] = $menuItem;
			}
		}
		
		$attr = $this->addClass($attr, 'ui menu');
		
		return $html->tag('div', $content, $attr);
	}
	
	public function mkMenuAttr($class)
	{
		return [
			'class' => $class,
		];
	}
}