<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use iscms\Alisms\SendsmsPusher as Sms;
use App\Util\SMSUtil;
use Illuminate\Support\Facades\DB;
use App\Material;
/*
 * 利用 linux 定时器定时执行命令
 */
class ReturnNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returnNotice:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '发送短信给即将逾期的用户';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Sms $sms)
    {
        //
       $returnNotices = DB::table('mm_material as m')
        ->leftjoin('mm_using_record as u','u.material_id','=','m.id')
        ->leftjoin('users','users.id','=','u.user_id')
        ->select('users.phone','m.name')
        ->where('m.status','=',Material::MATERIAL_STATUS_WASRENT)
        ->where('u.deadline','>',date(DateUtil::FORMAT,strtotime('-1 day')))
        ->where('u.deadline','<',date(DateUtil::FORMAT,time()))// 注释本行是为了让错误的数据暴露出来，不注释则可以提高稳定性
        ->get();
       if($returnNotices->count()>0){
       		foreach ($returnNotices as $returnNotice){
    			SMSUtil::sendReturnNotice($sms, $returnNotice->phone, $returnNotice->name);
       		}
       }
    }
}
