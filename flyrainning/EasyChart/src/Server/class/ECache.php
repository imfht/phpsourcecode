<?php
class ECache{
  public $key;
  public $date;
  public $file;
  public $data;
  function __construct($key="",$date=""){
    global $_api_uri_;

    $serverhash=md5(__FILE__);
    $this->date=empty($date)?date('Y-m-d'):$date;
    $this->key="key_".$key;
    $this->file="/tmp/chartcache".$serverhash.$_api_uri_;
    $this->data=$this->load();
  }
  function get(){
    $r=false;
    if (isset($this->data[$this->key])){
      if ($this->data[$this->key]['date']==$this->date){
        $r=$this->data[$this->key]['data'];
      }

    }
  //  return false;
    return $r;
  }
  function set($data){

    $this->data[$this->key]=array(
      'date'=>$this->date,
      'data'=>$data
    );
    $this->save();
    return $data;
  }
  function save() {
  	file_exists($this->file) or touch($this->file);
  	file_put_contents($this->file, '<?php return '.var_export($this->data, TRUE).'; ?>');
  }

  function load(){
  	return file_exists($this->file)? require($this->file):array();

  }
  function clean(){
    $this->data=array();
    $this->save();
  }
}
 ?>
