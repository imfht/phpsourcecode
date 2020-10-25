<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/12
 * Time: 21:06
 */
class Seeder
{
  private $CI;  //CI实例引用
  protected $db;
  protected $dbforge;

  public function __construct()
  {
    $this->CI = & get_instance();
    //$this->CI->load->database('testing');    //播种播到testing上
    $this->CI->load->dbforge();
    $this->db = $this->CI->db;
    $this->dbforge = $this->CI->dbforge;
  }


  /**
   * 运行播种
   *
   * @param $seeder 播种类
   */
  public function call($seeder){
    //播种类放在database/seeds/上
    $file = APPPATH . 'database/seeds/' . $seeder . '.php';
    require_once $file;
    $obj = new $seeder;
    $obj->run();

  }


}