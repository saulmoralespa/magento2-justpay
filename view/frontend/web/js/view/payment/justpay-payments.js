define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'justpay_cash',
                component: 'Saulmoralespa_JustPay/js/view/payment/method-renderer/justpay-cash'
            }
        );
        rendererList.push(
            {
                type: 'justpay_online',
                component: 'Saulmoralespa_JustPay/js/view/payment/method-renderer/justpay-online'
            }
        );
        rendererList.push(
            {
                type: 'justpay_cards',
                component: 'Saulmoralespa_JustPay/js/view/payment/method-renderer/justpay-cards'
            }
        );
        return Component.extend({});
    }
);