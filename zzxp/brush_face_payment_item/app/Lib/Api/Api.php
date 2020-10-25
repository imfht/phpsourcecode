<?php 
namespace App\Lib\Api;

class Api extends BaseApi
{
    protected $path = '';
    public $system = 'api';

    function __construct($setting = [])
    {
        parent::__construct($setting);
    }

    public function addQuestion($data){

        $url = $this->apiUrl('question/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getQuestion($data){

        $url = $this->apiUrl('question/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateQuestion($data){

        $url = $this->apiUrl('question/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delQuestion($data){

        $url = $this->apiUrl('question/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function addArticle($data){

        $url = $this->apiUrl('article/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getArticle($data){

        $url = $this->apiUrl('article/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateArticle($data){

        $url = $this->apiUrl('article/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delArticle($data){

        $url = $this->apiUrl('article/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addAttachment($data){

        $url = $this->apiUrl('attachment/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getAttachment($data){

        $url = $this->apiUrl('attachment/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateAttachment($data){

        $url = $this->apiUrl('attachment/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delAttachment($data){

        $url = $this->apiUrl('attachment/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function addBank($data){

        $url = $this->apiUrl('bank/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getBank($data){

        $url = $this->apiUrl('bank/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateBank($data){

        $url = $this->apiUrl('bank/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delBank($data){

        $url = $this->apiUrl('bank/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addBusiness($data){

        $url = $this->apiUrl('business/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getBusiness($data){

        $url = $this->apiUrl('business/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateBusiness($data){

        $url = $this->apiUrl('business/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delBusiness($data){

        $url = $this->apiUrl('business/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addBusinessType($data){

        $url = $this->apiUrl('business-type/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getBusinessType($data){

        $url = $this->apiUrl('business-type/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateBusinessType($data){

        $url = $this->apiUrl('business-type/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delBusinessType($data){

        $url = $this->apiUrl('business-type/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function addBusinessMachine($data){

        $url = $this->apiUrl('business-machine/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getBusinessMachine($data){

        $url = $this->apiUrl('business-machine/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateBusinessMachine($data){

        $url = $this->apiUrl('business-machine/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delBusinessMachine($data){

        $url = $this->apiUrl('business-machine/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    
    public function addDistrict($data){

        $url = $this->apiUrl('district/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getDistrict($data){

        $url = $this->apiUrl('district/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateDistrict($data){

        $url = $this->apiUrl('district/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delDistrict($data){

        $url = $this->apiUrl('district/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    
    public function addGoods($data){

        $url = $this->apiUrl('goods/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getGoods($data){

        $url = $this->apiUrl('goods/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateGoods($data){

        $url = $this->apiUrl('goods/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delGoods($data){

        $url = $this->apiUrl('goods/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addGoodsCar($data){

        $url = $this->apiUrl('goods-car/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getGoodsCar($data){

        $url = $this->apiUrl('goods-car/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateGoodsCar($data){

        $url = $this->apiUrl('goods-car/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delGoodsCar($data){

        $url = $this->apiUrl('goods-car/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addIntroduceMoney($data){

        $url = $this->apiUrl('introduce-money/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getIntroduceMoney($data){

        $url = $this->apiUrl('introduce-money/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateIntroduceMoney($data){

        $url = $this->apiUrl('introduce-money/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delIntroduceMoney($data){

        $url = $this->apiUrl('introduce-money/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addLevelName($data){

        $url = $this->apiUrl('level-name/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getLevelName($data){

        $url = $this->apiUrl('level-name/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateLevelName($data){

        $url = $this->apiUrl('level-name/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delLevelName($data){

        $url = $this->apiUrl('level-name/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addMember($data){

        $url = $this->apiUrl('member/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getMember($data){

        $url = $this->apiUrl('member/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function memberStatistics($data){

        $url = $this->apiUrl('member/statistics');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function memberPay($data){

        $url = $this->apiUrl('member/pay');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function member($data){

        $url = $this->apiUrl('member/member');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateMember($data){

        $url = $this->apiUrl('member/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delMember($data){

        $url = $this->apiUrl('member/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addMemberOrders($data){

        $url = $this->apiUrl('member-orders/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getMemberOrders($data){

        $url = $this->apiUrl('member-orders/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateMemberOrders($data){

        $url = $this->apiUrl('member-orders/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delMemberOrders($data){

        $url = $this->apiUrl('member-orders/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addMessage($data){

        $url = $this->apiUrl('message/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getMessage($data){

        $url = $this->apiUrl('message/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateMessage($data){

        $url = $this->apiUrl('message/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delMessage($data){

        $url = $this->apiUrl('message/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addMoneyLog($data){

        $url = $this->apiUrl('money-log/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getMoneyLog($data){

        $url = $this->apiUrl('money-log/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateMoneyLog($data){

        $url = $this->apiUrl('money-log/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delMoneyLog($data){

        $url = $this->apiUrl('money-log/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addOrderInfo($data){

        $url = $this->apiUrl('order-info/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getOrderInfo($data){

        $url = $this->apiUrl('order-info/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateOrderInfo($data){

        $url = $this->apiUrl('order-info/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delOrderInfo($data){

        $url = $this->apiUrl('order-info/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addOrders($data){

        $url = $this->apiUrl('orders/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getOrders($data){

        $url = $this->apiUrl('orders/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateOrders($data){

        $url = $this->apiUrl('orders/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delOrders($data){

        $url = $this->apiUrl('orders/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addSaleOrder($data){

        $url = $this->apiUrl('sale-order/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSaleOrder($data){

        $url = $this->apiUrl('sale-order/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSaleOrder($data){

        $url = $this->apiUrl('sale-order/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSaleOrder($data){

        $url = $this->apiUrl('sale-order/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function addSaleMoney($data){

        $url = $this->apiUrl('sale-money/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSaleMoney($data){

        $url = $this->apiUrl('sale-money/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSaleMoney($data){

        $url = $this->apiUrl('sale-money/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSaleMoney($data){

        $url = $this->apiUrl('sale-money/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addWeekend($data){

        $url = $this->apiUrl('weekend/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getWeekend($data){

        $url = $this->apiUrl('weekend/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateWeekend($data){

        $url = $this->apiUrl('weekend/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delWeekend($data){

        $url = $this->apiUrl('weekend/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function addWithdraw($data){

        $url = $this->apiUrl('withdraw/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getWithdraw($data){

        $url = $this->apiUrl('withdraw/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateWithdraw($data){

        $url = $this->apiUrl('withdraw/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delWithdraw($data){

        $url = $this->apiUrl('withdraw/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addSort($data){

        $url = $this->apiUrl('sort/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getSort($data){

        $url = $this->apiUrl('sort/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateSort($data){

        $url = $this->apiUrl('sort/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delSort($data){

        $url = $this->apiUrl('sort/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function addConfig($data){

        $url = $this->apiUrl('config/add');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
    public function getConfig($data){

        $url = $this->apiUrl('config/get');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }

    public function updateConfig($data){

        $url = $this->apiUrl('config/update');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }


    public function delConfig($data){

        $url = $this->apiUrl('config/del');
        $res = $this->easyget($url, $data);
        if ($res === false) {
            return false;
        }
        return $res;
    }
}