<?php


namespace Saulmoralespa\JustPay\Controller\Payment;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Payment\Transaction;

class Error extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var PaymentHelper
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Sales\Api\TransactionRepositoryInterface
     */
    protected $_transactionRepository;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface
     */
    protected $_transactionBuilder;

    /**
     * @var \Saulmoralespa\JustPay\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Saulmoralespa\JustPay\Helper\Data $helperData,
        PaymentHelper $paymentHelper,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        parent::__construct($context);

        $this->_scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_transactionRepository = $transactionRepository;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_helperData = $helperData;
        $this->_pageFactory = $pageFactory;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $params = $request->getParams();

        if (empty($params))
            exit;

        if (!$request->getParam('order_id'))
            exit;

        $order_id = $request->getParam('order_id');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order_model = $objectManager->get('Magento\Sales\Model\Order');
        $order = $order_model->load($order_id);
        $failedOrder = \Magento\Sales\Model\Order::STATE_CANCELED;

        $method = $order->getPayment()->getMethod();

        if (!isset($order) || $order->getState() === $failedOrder || strpos($method, 'justpay') === false)
            return $this->_pageFactory->create();

        $payment = $order->getPayment();

        $statuses = $this->_helperData->getOrderStates();

        $transaction = $this->_transactionRepository->getByTransactionType(
            Transaction::TYPE_ORDER,
            $payment->getId(),
            $payment->getOrder()->getId()
        );

        $payment->setIsTransactionDenied(true);
        $status = $statuses["rejected"];

        $order->setState($failedOrder)->setStatus($status);
        $payment->setSkipOrderProcessing(true);

        $message = __('Payment declined');

        $payment->addTransactionCommentsToOrder($transaction, $message);

        $transaction->save();
        $order->cancel()->save();

        return $this->_pageFactory->create();

    }
}