import Layout from '../layouts/Layout.vue';
import Store from '../pages/Store.vue';
import StoreApply from '../pages/StoreApply.vue';
import StoreLayout from '../layouts/Store.vue';
import UserLayout from '../layouts/UserLayout.vue';
import UserOrder from '../pages/UserOrder.vue';
import UserCoupon from '../pages/UserCoupon.vue';
import UserIntegral from '../pages/UserIntegral.vue';
import UserSecurity from '../pages/UserSecurity.vue';
import UserAddress from '../pages/UserAddress.vue';
import UserFollow from '../pages/UserFollow.vue';
import SelaesReturn from '../pages/SelaesReturn.vue';
import User from '../pages/User.vue';
import UserFootprint from '../pages/UserFootprint.vue';
import UserAccount from '../pages/UserAccount.vue';
import UserCollect from '../pages/UserCollect.vue';
import UserNotice from '../pages/UserNotice.vue';
import ResetPassword from '../pages/ResetPassword.vue';
import UserPasswordEdit from '../pages/UserPasswordEdit.vue';
import UserEmailEdit from '../pages/UserEmailEdit.vue';
import UserEvaluation from '../pages/UserEvaluate.vue';

import SignUp from '../pages/SignUp.vue';
import SignIn from '../pages/SignIn.vue';

import Home from '../pages/Home.vue';
import Help from '../pages/Help.vue';
import AfterSale from '../pages/AfterSale.vue';
import CustomerServe from '../pages/CustomerServe.vue';
import More from '../pages/More.vue';
import Offer from '../pages/Offer.vue';
import Discount from '../pages/Discount.vue';
import Spike from '../pages/Spike.vue';
import Classification from '../pages/Classification.vue';
import Search from '../pages/Search.vue';
import ProductDetails from '../pages/ProductDetails.vue';
import Evaluation from '../pages/Evaluation.vue';

import SubmitOrder from '../pages/SubmitOrder.vue';
import OrderSuccess from '../pages/OrderSuccess.vue';
import PayResult from '../pages/PayResult.vue';
import CartSettlement from '../pages/CartSettlement.vue';
import PaymentSuccess from '../pages/PaymentSuccess.vue';
import ScanPay from '../pages/ScanPay.vue';

import ShoppingProcess from '../pages/ShoppingProcess/ShoppingProcess.vue';
import ShopProcess from '../pages/ShoppingProcess/ShopProcess.vue';
import PayMethod from '../pages/ShoppingProcess/PayMethod.vue';
import CommonProblem from '../pages/ShoppingProcess/CommonProblem.vue';
import ReturnProcess from '../pages/ShoppingProcess/ReturnProcess.vue';
import ReturnMoney from '../pages/ShoppingProcess/ReturnMoney.vue';
import ContactService from '../pages/ShoppingProcess/ContactService.vue';
import DeliveryMethod from '../pages/ShoppingProcess/DeliveryMethod.vue';
import DeliverySevice from '../pages/ShoppingProcess/DeliverySevice.vue';
import DeliveryTrack from '../pages/ShoppingProcess/DeliveryTrack.vue';
import AboutUs from '../pages/ShoppingProcess/AboutUs.vue';
import ContactUs from '../pages/ShoppingProcess/ContactUs.vue';
import Cooperation from '../pages/ShoppingProcess/Cooperation.vue';

import Refund from '../pages/Refund.vue';
import ReturnOfGoods from '../pages/ReturnOfGoods.vue';

export default [
    {
        children: [
            {
                component: Home,
                path: '/',
                name: 'home',
            },
            {
                component: ResetPassword,
                path: 'reset-password',
                name: 'reset-password',
            },
            {
                component: ScanPay,
                name: 'scan-pay',
                path: 'scan-pay',
            },
            {
                component: CustomerServe,
                name: 'customer-serve',
                path: 'customer-serve',
            },
            {
                component: Help,
                name: 'help',
                path: 'help',
            },
            {
                component: AfterSale,
                name: 'after-sale',
                path: 'after-sale',
            },
            {
                component: More,
                name: 'more',
                path: 'more',
            },
            {
                component: Offer,
                name: 'offer',
                path: 'offer',
            },
            {
                component: Discount,
                name: 'discount',
                path: 'discount',
            },
            {
                component: Spike,
                name: 'spike',
                path: 'spike',
            },
            {
                component: Refund,
                name: 'refund',
                path: 'refund',
            },
            {
                component: ReturnOfGoods,
                name: 'return-of-goods',
                path: 'return-of-goods',
            },
            {
                component: Classification,
                name: 'classification',
                path: 'classification',
            },
            {
                component: Search,
                name: 'search',
                path: 'search',
            },
            {
                component: ProductDetails,
                name: 'product-details',
                path: 'search/product-details',
            },
            {
                component: SubmitOrder,
                name: 'submit-order',
                path: 'search/product-details/submit-order',
            },
            {
                component: CartSettlement,
                path: 'cart-settlement',
                name: 'cart-settlement',
            },
            {
                component: Evaluation,
                name: 'evaluation',
                path: 'evaluation',
            },
            {
                children: [
                    {
                        component: ShopProcess,
                        name: 'shop-process',
                        path: 'shop-process',
                    },
                    {
                        component: PayMethod,
                        name: 'pay-method',
                        path: 'pay-method',
                    },
                    {
                        component: CommonProblem,
                        name: 'common-problem',
                        path: 'common-problem',
                    },
                    {
                        component: ReturnProcess,
                        name: 'return-process',
                        path: 'return-process',
                    },
                    {
                        component: ReturnMoney,
                        name: 'return-money',
                        path: 'return-money',
                    },
                    {
                        component: ContactService,
                        name: 'contact-service',
                        path: 'contact-service',
                    },
                    {
                        component: DeliveryMethod,
                        name: 'delivery-method',
                        path: 'delivery-method',
                    },
                    {
                        component: DeliverySevice,
                        name: 'delivery-sevice',
                        path: 'delivery-sevice',
                    },
                    {
                        component: DeliveryTrack,
                        name: 'delivery-track',
                        path: 'delivery-track',
                    },
                    {
                        component: AboutUs,
                        name: 'about-us',
                        path: 'about-us',
                    },
                    {
                        component: ContactUs,
                        name: 'contact-us',
                        path: 'contact-us',
                    },
                    {
                        component: Cooperation,
                        name: 'cooperation',
                        path: 'cooperation',
                    },
                ],
                component: ShoppingProcess,
                path: 'shop-process',
                redirect: { name: 'shop-process' },
            },
            {
                component: OrderSuccess,
                name: 'order-success',
                path: 'order-success',
            },
            {
                component: PayResult,
                name: 'pay-result',
                path: 'pay-result',
            },
            {
                component: PaymentSuccess,
                name: 'payment-success',
                path: 'payment-success',
            },
            {
                children: [
                    {
                        component: StoreApply,
                        name: 'businessmen',
                        path: 'apply',
                    },
                ],
                component: StoreLayout,
                path: 'store',
                redirect: {
                    name: 'businessmen',
                },
            },
            {
                component: Store,
                name: 'shop-home',
                path: 'store/shop-home',
            },
            {
                children: [
                    {
                        component: User,
                        name: 'user',
                        path: '/mall/user',
                    },
                    {
                        component: UserOrder,
                        name: 'order',
                        path: 'order',
                    },
                    {
                        component: UserPasswordEdit,
                        name: 'password-edit',
                        path: 'password/edit',
                    },
                    {
                        component: UserEmailEdit,
                        name: 'email-edit',
                        path: 'email/edit',
                    },
                    {
                        component: UserEvaluation,
                        name: 'user-evaluation',
                        path: 'evaluation',
                    },
                    {
                        component: UserCoupon,
                        name: 'coupon',
                        path: 'coupon',
                    },
                    {
                        component: UserIntegral,
                        name: 'integral',
                        path: 'integral',
                    },
                    {
                        component: UserSecurity,
                        name: 'account-security',
                        path: 'account-security',
                    },
                    {
                        component: UserAddress,
                        name: 'address',
                        path: 'address',
                    },
                    {
                        component: UserFollow,
                        name: 'collect-store',
                        path: 'collect-store',
                    },
                    {
                        component: SelaesReturn,
                        name: 'selaes-return',
                        path: 'selaes-return',
                    },
                    {
                        component: UserFootprint,
                        name: 'footprint',
                        path: 'footprint',
                    },
                    {
                        component: UserAccount,
                        name: 'my-account',
                        path: 'my-account',
                    },
                    {
                        component: UserCollect,
                        name: 'my-collect',
                        path: 'my-collect',
                    },
                    {
                        component: UserNotice,
                        name: 'notice',
                        path: 'notice',
                    },
                ],
                component: UserLayout,
                name: 'personnal-center',
                path: 'user',
                redirect: {
                    name: 'user',
                },
            },
        ],
        component: Layout,
        path: '/mall',
    },
    {
        component: SignUp,
        name: 'signup',
        path: '/signup',
    },
    {
        component: SignIn,
        name: 'signin',
        path: '/signin',
    },
];
