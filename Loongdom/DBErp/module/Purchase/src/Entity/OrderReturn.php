<?php
/**
 * DBERP 进销存系统
 *
 * ==========================================================================
 * @link      http://www.dberp.net/
 * @copyright 北京珑大钜商科技有限公司，并保留所有权利。
 * @license   http://www.dberp.net/license.html License
 * ==========================================================================
 *
 * @author    静静的风 <baron@loongdom.cn>
 *
 */

namespace Purchase\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 采购退货单
 * Class OrderReturn
 * @package Purchase\Entity
 * @ORM\Entity(repositoryClass="Purchase\Repository\OrderReturnRepository")
 * @ORM\Table(name="dberp_purchase_order_return")
 */
class OrderReturn
{
    /**
     * 退货单id
     * @ORM\Id()
     * @ORM\Column(name="order_return_id", type="integer", length=11)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $orderReturnId;

    /**
     * 采购单id
     * @ORM\Column(name="p_order_id", type="integer", length=11)
     */
    private $pOrderId;

    /**
     * 采购单编号
     * @ORM\Column(name="p_order_sn", type="string", length=50)
     */
    private $pOrderSn;

    /**
     * 退货商品金额
     * @ORM\Column(name="p_order_goods_return_amount", type="decimal", scale=4)
     */
    private $pOrderGoodsReturnAmount;

    /**
     * 退货金额
     * @ORM\Column(name="p_order_return_amount", type="decimal", scale=4)
     */
    private $pOrderReturnAmount;

    /**
     * 退货说明
     * @ORM\Column(name="p_order_return_info", type="string", length=500)
     */
    private $pOrderReturnInfo;

    /**
     * 退货时间
     * @ORM\Column(name="return_time", type="integer", length=10)
     */
    private $returnTime;

    /**
     * 退货状态（-1 退货中，-5 退货完成）
     * @ORM\Column(name="return_state", type="integer", length=2)
     */
    private $returnState;

    /**
     * 退货完成时间
     * @ORM\Column(name="return_finish_time", type="integer", length=10)
     */
    private $returnFinishTime;

    /**
     * 操作者id
     * @ORM\Column(name="admin_id", type="integer", length=11)
     */
    private $adminId;

    /**
     * @var
     * @ORM\OneToOne(targetEntity="Purchase\Entity\Order")
     * @ORM\JoinColumn(name="p_order_id", referencedColumnName="p_order_id")
     */
    private $onePOrder;

    /**
     * @return mixed
     */
    public function getOrderReturnId()
    {
        return $this->orderReturnId;
    }

    /**
     * @param mixed $orderReturnId
     */
    public function setOrderReturnId($orderReturnId)
    {
        $this->orderReturnId = $orderReturnId;
    }

    /**
     * @return mixed
     */
    public function getPOrderId()
    {
        return $this->pOrderId;
    }

    /**
     * @param mixed $pOrderId
     */
    public function setPOrderId($pOrderId)
    {
        $this->pOrderId = $pOrderId;
    }

    /**
     * @return mixed
     */
    public function getPOrderSn()
    {
        return $this->pOrderSn;
    }

    /**
     * @param mixed $pOrderSn
     */
    public function setPOrderSn($pOrderSn)
    {
        $this->pOrderSn = $pOrderSn;
    }

    /**
     * @return mixed
     */
    public function getPOrderGoodsReturnAmount()
    {
        return $this->pOrderGoodsReturnAmount;
    }

    /**
     * @param mixed $pOrderGoodsReturnAmount
     */
    public function setPOrderGoodsReturnAmount($pOrderGoodsReturnAmount)
    {
        $this->pOrderGoodsReturnAmount = $pOrderGoodsReturnAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderReturnAmount()
    {
        return $this->pOrderReturnAmount;
    }

    /**
     * @param mixed $pOrderReturnAmount
     */
    public function setPOrderReturnAmount($pOrderReturnAmount)
    {
        $this->pOrderReturnAmount = $pOrderReturnAmount;
    }

    /**
     * @return mixed
     */
    public function getPOrderReturnInfo()
    {
        return $this->pOrderReturnInfo;
    }

    /**
     * @param mixed $pOrderReturnInfo
     */
    public function setPOrderReturnInfo($pOrderReturnInfo)
    {
        $this->pOrderReturnInfo = $pOrderReturnInfo;
    }

    /**
     * @return mixed
     */
    public function getReturnTime()
    {
        return $this->returnTime;
    }

    /**
     * @param mixed $returnTime
     */
    public function setReturnTime($returnTime)
    {
        $this->returnTime = $returnTime;
    }

    /**
     * @return mixed
     */
    public function getReturnState()
    {
        return $this->returnState;
    }

    /**
     * @param mixed $returnState
     */
    public function setReturnState($returnState)
    {
        $this->returnState = $returnState;
    }

    /**
     * @return mixed
     */
    public function getReturnFinishTime()
    {
        return $this->returnFinishTime;
    }

    /**
     * @param mixed $returnFinishTime
     */
    public function setReturnFinishTime($returnFinishTime)
    {
        $this->returnFinishTime = $returnFinishTime;
    }

    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getOnePOrder()
    {
        return $this->onePOrder;
    }

    /**
     * @param mixed $onePOrder
     */
    public function setOnePOrder($onePOrder)
    {
        $this->onePOrder = $onePOrder;
    }

}