<?php


namespace Saulmoralespa\JustPay\Controller\Payment;


use Exception;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Payment\Helper\Data as PaymentHelper;

class Data extends \Magento\Framework\App\Action\Action
{
    protected $_helperData;

    protected $_justPayLogger;

    protected $_checkoutSession;

    protected $_orderFactory;

    protected $_resultJsonFactory;

    protected $_url;

    protected $_transactionBuilder;

    protected $_paymentHelper;

    protected $_curl;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Saulmoralespa\JustPay\Helper\Data $helperData,
        \Saulmoralespa\JustPay\Logger\Logger $justPayLogger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        PaymentHelper $paymentHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    )
    {
        parent::__construct($context);

        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_helperData = $helperData;
        $this->_justPayLogger = $justPayLogger;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_url = $context->getUrl();
        $this->_transactionBuilder = $transactionBuilder;
        $this->_paymentHelper = $paymentHelper;
        $this->_curl = $curl;
    }

    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        try{
            $order = $this->_getCheckoutSession()->getLastRealOrder();
            $method = $order->getPayment()->getMethod();

            $public_key = $this->_helperData->getPublicKey();
            $segure_key = $this->_helperData->getSecureKey();
            $time = date('Y-m-d\TH:i:s');
            $channel = $this->getChannel($method);
            $amount = $this->_helperData->getAmount($order);
            $currency = $order->getOrderCurrencyCode();
            $order_id = $order->getId();
            $trans_id = $order_id . "_" . time();
            $time_expired = $this->_helperData->getExpirationTime();
            $url_ok = $this->_url->getUrl('justpay/payment/success');
            $url_error = $this->_url->getUrl('justpay/payment/error', ['order_id' => $order_id]);
            $url_finalizar = $this->_url->getUrl('justpay/payment/finalize');

            $data_sign = "$public_key$time$amount$currency$trans_id$time_expired$url_ok$url_error$channel$segure_key";
            $signature = hash('sha256', $data_sign);

            $address = $this->getAddress($order);

            $payment = $order->getPayment();
            $payment->setTransactionId($trans_id)
                ->setIsTransactionClosed(0);

            $payment->setParentTransactionId($order->getId());
            $payment->setIsTransactionPending(true);
            $transaction = $this->_transactionBuilder->setPayment($payment)
                ->setOrder($order)
                ->setTransactionId($payment->getTransactionId())
                ->build(Transaction::TYPE_ORDER);

            $payment->addTransactionCommentsToOrder($transaction, __('pending'));

            $statuses = $this->_helperData->getOrderStates();
            $status = $statuses["pending"];
            $state = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT;
            $order->setState($state)->setStatus($status);
            $payment->setSkipOrderProcessing(true);
            $order->save();

            $data = [
                'public_key' => $public_key,
                'time' => $time,
                'channel' => $channel,
                'amount' => $amount,
                'currency' => $currency,
                'trans_id' => $trans_id,
                'time_expired' => $time_expired,
                'url_ok' => $url_ok,
                'url_error' => $url_error,
                'url_finalizar' => $url_finalizar,
                'signature' => $signature,
                'name_shopper' => $address->getFirstname(),
                'las_name_Shopper' => $address->getLastname(),
                'email' => $order->getCustomerEmail(),
                'country_code' => $address->getCountryId(),
                'phone' => $address->getTelephone(),
                'mobile' => $address->getTelephone()
            ];

            $this->_curl->post($this->_helperData->getEndPoint(), $data);
            $response = $this->_curl->getBody();
            $result = $this->_resultJsonFactory->create();
            return $result->setData([
                'url' => $response
            ]);
        }catch (Exception $exception){
            $this->_helperData->log($exception->getMessage());
            throw new Exception($exception->getMessage());
        }

    }

    public function getChannel($method)
    {
        $channel = '1';

        if ($method === 'justpay_cash')
            $channel = '2';
        if ($method === 'justpay_cards')
            $channel = '3';

        return $channel;
    }

    public function getAddress($order)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        if ($billingAddress)
            return $billingAddress;

        return $shippingAddress;

    }
}