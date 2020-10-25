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
 * Simple class to output CSV data
 * Uses CollectionCore
 * @since 1.5
 */
class CSVCore
{
	public $filename;
    public $collection;
    public $delimiter;

    /**
     * Loads objects, filename and optionnaly a delimiter.
     * @param Collection $collection collection of objects / array (of non-objects)
     * @param string $filename : used later to save the file
     * @param string $delimiter Optional : delimiter used
     */
	public function __construct($collection, $filename, $delimiter = ';')
	{
		$this->filename = $filename;
		$this->delimiter = $delimiter;
		$this->collection = $collection;
	}

	/**
	 * Main function
	 * Adds headers
	 * Outputs
	 */
	public function export()
	{
		$this->headers();

		$header_line = false;

		foreach ($this->collection as $object)
		{
			$vars = get_object_vars($object);
			if (!$header_line)
			{
				$this->output(array_keys($vars));
				$header_line = true;
			}

			// outputs values
			$this->output($vars);
			unset($vars);
		}
	}

	/**
	 * Wraps data and echoes
	 * Uses defined delimiter
	 */
	public function output($data)
	{
    	$wraped_data = array_map(array('CSVCore', 'wrap'), $data);
        echo sprintf("%s\n", implode($this->delimiter, $wraped_data));
	}

	/**
	 * Escapes data
	 * @param string $data
	 * @return string $data
	 */
    public static function wrap($data)
    {
    	$data = Tools::safeOutput($data, '";');
        return sprintf('"%s"', $data);
    }

    /**
     * Adds headers
     */
    public function headers()
    {
        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
		header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="'.$this->filename.'.csv"');
    }
}

