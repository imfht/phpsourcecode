<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /*总记录数*/
    protected $totalData = 0;
    /*一页条数*/
    protected $pageSize = 2;
    /*总页数*/
    protected $page = 0;

    /**
     * @return array
     */
    public function getTotalData(){
        return $this->totalData = \DB::select("SELECT count(1) FROM bijis WHERE share = :share ",[':share'=>1]);
    }

    /**
     * @return float
     */
    public function getPage(){
        return $this->page = ceil($this->totalData/$this->pageSize);
    }


}
