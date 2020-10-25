<?php

class CextendController extends Controller {

	public function __construct()
	{
		parent::Controller();
	}

	public function index()
	{
		echo 'Hello World';
	}

	public function rebuild()
	{
		$dbFile = '772487861.db';

		$fromConfig = array(
			'filename' => APPLICATION_PATH.'/data/MobileQQ/data_old_backup/databases/'.$dbFile,
			'tbprefix' => '',
			'dbdriver' => 'sqlite',
			'debug'    => TRUE
		);

		$toConfig = $fromConfig;
		$toConfig['filename'] = APPLICATION_PATH.'/data/MobileQQ/data_old_backup/data/'.$dbFile;

		$this->load->db($fromConfig, 'fdb');
		$this->load->db($toConfig, 'tdb');

		$this->tdb->begin();

		$sth = $this->fdb->get('sqlite_master', 'tbl_name,sql', array('type'=>'table', 'name !%'=>'sqlite_%', 'name not like'=>'android_%'))->sth;

		while ($table = $this->fdb->fetch($sth)) {
			$table['sql'] = substr_replace($table['sql'], 'CREATE TABLE IF NOT EXISTS', 0, 12);
			$this->tdb->exec($table['sql']);

			$this->fdb->get($table['tbl_name'], '*', null, array('orderby'=>'_id asc'));

			while ($row = $this->fdb->fetch()) {
				if (isset($row['_id'])) unset($row['_id']);
				$this->tdb->insert($table['tbl_name'], $row);
			}
		}

		$this->tdb->commit();
		//$this->tdb->rollback();
	}

	public function other()
	{
		$h = 'ss';
		printr($h instanceof PDOStatement);
	}

}
