<?php
namespace App\Controllers;
use App\Models\SystemMailModel;
use DB;

class SystemMailController extends BaseController{

    public function __construct()
    {
        $this->system_mail = new SystemMailModel;
    }

    public function add($input)
    {
        if(empty($input)){
            return $this->setError('参数不能为空');
        }       
        $result = $this->system_mail->addSystemMail($input);

        if ($result) {
            return ['id' => $result];
        }else{
            return $this->setError('操作失败');
        }
    }


    public function update($input)
    {

        if (empty($input['id'])) {
            return $this->setError('编号不能为空');
        }
        $row = $this->system_mail->getSystemMailById($input['id']);
        if (empty($row)) {
            return $this->setError('对应信息不存在');
        }

        return $this->system_mail->updateSystemMail($row['id'], $input);
    }

    public function delete($input)
    {
        if (empty($input['id'])) {
            return $this->setError('编号不能为空');
        }


        $row = $this->system_mail->getSystemMailById($input['id']);

        if (empty($row)) {
            return true;
        }

        return $this->system_mail->delSystemMail($input['id']);
    }


    public function get($input)
    {

        $page = empty($input['page']) ? 0 :  $input['page'];
        $size = empty($input['size']) ? 10 : $input['size'];
        $system_mails = $this->system_mail->getSystemMail($input, $page, $size);
        $system_mail_num = $this->system_mail->countSystemMail($input);
        return ['result'=>$system_mails,'total'=>$system_mail_num];
    }
}