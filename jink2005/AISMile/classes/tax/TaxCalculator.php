<?php
/**
 * MILEBIZ Ã×ÀÖÉÌ³Ç
 * ============================================================================
 * °æÈ¨ËùÓĞ 2011-20__ Ã×ÀÖÍø¡£
 * ÍøÕ¾µØÖ·: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5.0
 *
 * TaxCaculator is responsible of the tax computation
 */
class TaxCalculatorCore
{
	/**
  	 * COMBINE_METHOD sum taxes
	 * eg: 100â‚¬ * (10% + 15%)
	 */
	const COMBINE_METHOD = 1;

	/**
	 * ONE_AFTER_ANOTHER_METHOD apply taxes one after another
	 * eg: (100â‚¬ * 10%) * 15%
	 */
	const ONE_AFTER_ANOTHER_METHOD = 2;

	/**
	 * @var array $taxes
	 */
	public $taxes;

	/**
	 * @var int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
	 */
	public $computation_method;


	/**
	 * @param array $taxes
	 * @param int $computation_method (COMBINE_METHOD | ONE_AFTER_ANOTHER_METHOD)
	 */
	public function __construct(array $taxes = array(), $computation_method = TaxCalculator::COMBINE_METHOD)
	{
		// sanity check
		foreach ($taxes as $tax)
			if (!($tax instanceof Tax))
				throw new Exception('Invalid Tax Object');

		$this->taxes = $taxes;
		$this->computation_method = (int)$computation_method;
	}

	/**
	 * Compute and add the taxes to the specified price
	 *
	 * @param price_te price tax excluded
	 * @return float price with taxes
	 */
	public function addTaxes($price_te)
	{
		return $price_te * (1 + ($this->getTotalRate() / 100));
	}


	/**
	 * Compute and remove the taxes to the specified price
	 *
	 * @param price_ti price tax inclusive
	 * @return price without taxes
	 */
	public function removeTaxes($price_ti)
	{
		return $price_ti / (1 + $this->getTotalRate() / 100);
	}

	/**
	 * @return float total taxes rate
	 */
	public function getTotalRate()
	{
		$taxes = 0;
		if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
		{
			$taxes = 1;
			foreach ($this->taxes as $tax)
				$taxes *= (1 + (abs($tax->rate) / 100));

			$taxes = $taxes - 1;
			$taxes = $taxes * 100;
		}
		else
		{
			foreach ($this->taxes as $tax)
				$taxes += abs($tax->rate);
		}

		return (float)$taxes;
	}

	/**
	 * Return the tax amount associated to each taxes of the TaxCalculator
	 *
	 * @param float $price_te
	 * @return array $taxes_amount
	 */
	public function getTaxesAmount($price_te)
	{
		$taxes_amounts = array();

		foreach ($this->taxes as $tax)
		{
			if ($this->computation_method == TaxCalculator::ONE_AFTER_ANOTHER_METHOD)
			{
				$taxes_amounts[$tax->id] = $price_te * (abs($tax->rate) / 100);
				$price_te = $price_te + $taxes_amounts[$tax->id];
			}
			else
				$taxes_amounts[$tax->id] = ($price_te * (abs($tax->rate) / 100));
		}

		return $taxes_amounts;
	}
}

