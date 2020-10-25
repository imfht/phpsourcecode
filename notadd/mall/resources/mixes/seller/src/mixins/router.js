import Layout from '../layouts/Layout.vue';
import Dashboard from '../pages/Dashboard.vue';
import Home from '../pages/Home.vue';
import Goods from '../pages/Goods.vue';
import GoodsAdd from '../pages/GoodsAdd.vue';
import GoodsClaim from '../pages/GoodsClaim.vue';
import GoodsEdit from '../pages/GoodsEdit.vue';
import GoodsEditCategory from '../pages/GoodsEditCategory.vue';
import GoodsNotice from '../pages/GoodsNotice.vue';
import GoodsStandard from '../pages/GoodsStandard.vue';
import GoodsPicture from '../pages/GoodsPicture.vue';
import GoodsPictureManage from '../pages/GoodsPictureManage.vue';
import Shop from '../pages/Shop.vue';
import ShopNavigate from '../pages/ShopNavigate.vue';
import ShopNavigateEdit from '../pages/ShopNavigateEdit.vue';
import ShopNavigateAdd from '../pages/ShopNavigateAdd.vue';
import ShopTrend from '../pages/ShopTrend.vue';
import ShopMessage from '../pages/ShopMessage.vue';
import ShopCategory from '../pages/ShopCategory.vue';
import ShopApplication from '../pages/ShopApplication.vue';
import ShopSupplier from '../pages/ShopSupplier.vue';
import ShopStore from '../pages/ShopStore.vue';
import Statistics from '../pages/Statistics.vue';
import StatisticsGoods from '../pages/StatisticsGoods.vue';
import StatisticsGoodsSet from '../pages/StatisticsGoodsSet.vue';
import StatisticsOperation from '../pages/StatisticsOperation.vue';
import StatisticsOperationSet from '../pages/StatisticsOperationSet.vue';
import StatisticsIndustry from '../pages/StatisticsIndustry.vue';
import StatisticsIndustrySet from '../pages/StatisticsIndustrySet.vue';
import StatisticsFlow from '../pages/StatisticsFlow.vue';
import StatisticsSettlement from '../pages/StatisticsSettlement.vue';
import StatisticsSettlementLook from '../pages/StatisticsSettlementLook.vue';
import Order from '../pages/Order.vue';
import OrderLogistics from '../pages/OrderLogistics.vue';
import OrderDatail from '../pages/OrderDatail.vue';
import OrderSettingShip from '../pages/OrderSettingShip.vue';
import OrderShip from '../pages/OrderShip.vue';
import OrderShipLook from '../pages/OrderShipLook.vue';
import OrderShipSet from '../pages/OrderShipSet.vue';
import OrderSetting from '../pages/OrderSetting.vue';
import OrderWaybill from '../pages/OrderWaybill.vue';
import OrderWaybillSelect from '../pages/OrderWaybillSelect.vue';
import OrderWaybillAdd from '../pages/OrderWaybillAdd.vue';
import OrderWaybillDesign from '../pages/OrderWaybillDesign.vue';
import OrderWaybillEdit from '../pages/OrderWaybillEdit.vue';
import OrderEvaluation from '../pages/OrderEvaluation.vue';
import SalesSpikes from '../pages/SalesSpikes.vue';
import SalesSpikesCreate from '../pages/SalesSpikesCreate.vue';
import SalesSpikesMagage from '../pages/SalesSpikesMagage.vue';
import SalesActive from '../pages/SalesActive.vue';
import SalesActiveCreate from '../pages/SalesActiveCreate.vue';
import SalesFulldown from '../pages/SalesFulldown.vue';
import SalesFulldownCreate from '../pages/SalesFulldownCreate.vue';
import SalesFulldownDetail from '../pages/SalesFulldownDetail.vue';
import Customer from '../pages/Customer.vue';
import CustomerMessage from '../pages/CustomerMessage.vue';
import Account from '../pages/Account.vue';
import ServiceRefund from '../pages/ServiceRefund.vue';
import ServiceRefundCheck from '../pages/ServiceRefundCheck.vue';
import ServiceRefundDeal from '../pages/ServiceRefundDeal.vue';
import ServiceReturns from '../pages/ServiceReturns.vue';
import ServiceReturnsCheck from '../pages/ServiceReturnsCheck.vue';
import ServiceReturnsDeal from '../pages/ServiceReturnsDeal.vue';
import ServiceComplaint from '../pages/ServiceComplaint.vue';

export default function (injection) {
    injection.routers = [
        ...injection.routers,
        {
            children: [
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Dashboard,
                    path: '/',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Home,
                    path: 'home',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Goods,
                    path: 'goods',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsAdd,
                    path: 'goods/add',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsClaim,
                    path: 'goods/claim',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsEdit,
                    path: 'goods/edit',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsEditCategory,
                    path: 'goods/edit/category',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsNotice,
                    path: 'goods/notice',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsStandard,
                    path: 'goods/standard',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsPicture,
                    path: 'goods/picture',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: GoodsPictureManage,
                    path: 'goods/picture/manage',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Shop,
                    path: 'shop',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopNavigate,
                    path: 'shop/navigate',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopNavigateEdit,
                    path: 'shop/navigate/edit',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopNavigateAdd,
                    path: 'shop/navigate/add',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopTrend,
                    path: 'shop/trend',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopMessage,
                    path: 'shop/message',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopCategory,
                    path: 'shop/category',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopApplication,
                    path: 'shop/application',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopSupplier,
                    path: 'shop/supplier',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ShopStore,
                    path: 'shop/store',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Statistics,
                    path: 'statistics',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsGoods,
                    path: 'statistics/goods',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsGoodsSet,
                    path: 'statistics/goods/set',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsOperation,
                    path: 'statistics/operation',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsOperationSet,
                    path: 'statistics/operation/set',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsIndustry,
                    path: 'statistics/industry',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsIndustrySet,
                    path: 'statistics/industry/set',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsFlow,
                    path: 'statistics/flow',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsSettlement,
                    path: 'statistics/settlement',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: StatisticsSettlementLook,
                    path: 'statistics/settlement/look',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Order,
                    path: 'order',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderLogistics,
                    path: 'order/logistics',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderDatail,
                    path: 'order/detail',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderSettingShip,
                    path: 'order/setting/ship',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderShip,
                    path: 'order/ship',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderShipLook,
                    path: 'order/ship/look',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderShipSet,
                    path: 'order/ship/set',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderSetting,
                    path: 'order/setting',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderWaybill,
                    path: 'order/waybill',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderWaybillSelect,
                    path: 'order/waybill/select',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderWaybillAdd,
                    path: 'order/waybill/add',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderWaybillDesign,
                    path: 'order/waybill/design',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderWaybillEdit,
                    path: 'order/waybill/edit',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: OrderEvaluation,
                    path: 'order/evaluation',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesSpikes,
                    path: 'sales/spikes',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesSpikesCreate,
                    path: 'sales/spikes/create',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesSpikesMagage,
                    path: 'sales/spikes/manage',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesActive,
                    path: 'sales/active',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesActiveCreate,
                    path: 'sales/active/create',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesFulldown,
                    path: 'sales/fulldown',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesFulldownCreate,
                    path: 'sales/fulldown/create',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: SalesFulldownDetail,
                    path: 'sales/fulldown/detail',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Customer,
                    path: 'customer',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: CustomerMessage,
                    path: 'customer/message',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: Account,
                    path: 'account',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceRefund,
                    path: 'service/refund',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceRefundCheck,
                    path: 'service/refund/check',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceRefundDeal,
                    path: 'service/refund/deal',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceReturns,
                    path: 'service/returns',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceReturnsCheck,
                    path: 'service/returns/check',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceReturnsDeal,
                    path: 'service/returns/deal',
                },
                {
                    beforeEnter: injection.middleware.requireAuth,
                    component: ServiceComplaint,
                    path: 'service/complaint',
                },
            ],
            component: Layout,
            path: '/seller',
        },
    ];
}