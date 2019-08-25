<?php


namespace Saulmoralespa\JustPay\Controller\Payment;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Payment\Transaction;

class Notify extends \Magento\Framework\App\Action\Action
{
    protected $_scopeConfig;

    protected $_paymentHelper;

    protected $_transactionRepository;

    protected $_transactionBuilder;

    protected $_helperData;

    protected $request;

    protected $formKey;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Saulmoralespa\JustPay\Helper\Data $helperData,
        PaymentHelper $paymentHelper,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder
    )
    {
        parent::__construct($context);

        $this->_scopeConfig = $scopeConfig;
        $this->_paymentHelper = $paymentHelper;
        $this->_transactionRepository = $transactionRepository;
        $this->_transactionBuilder = $transactionBuilder;
        $this->_helperData = $helperData;
        $this->request = $request;
        $this->formKey = $formKey;
        $this->request->setParam('form_key', $this->formKey->getFormKey());
    }

    public function execute()
    {
        $request = $this->getRequest();
        $params = $request->getParams();

        if (empty($params))
            exit;

        if (!$request->getParam('amount') ||
            !$request->getParam('channel') ||
            !$request->getParam('currency') ||
            !$request->getParam('signature') ||
            !$request->getParam('time') ||
            !$request->getParam('trans_ID'))
            exit;

        $public_key = $this->_helperData->getPublicKey();
        $secure_key = $this->_helperData->getSecureKey();
        $amount = $request->getParam('amount');
        $time = $request->getParam('time');
        $currency = $request->getParam('currency');
        $trans_id = $request->getParam('trans_ID');
        $channel = $request->getParam('channel');
        $confirm_transid = $trans_id;

        $data_sign = "$public_key$time$channel$amount$currency$trans_id$secure_key";
        $signature = hash('sha256', $data_sign);

        $response_confirm = "$public_key,$time,$channel,$amount,$currency,$trans_id,$confirm_transid,$signature";

        $trans_id = explode('_', $trans_id);
        $order_id = $trans_id[0];

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order_model = $objectManager->get('Magento\Sales\Model\Order');
        $order = $order_model->load($order_id);

        $payment = $order->getPayment();
        $statuses = $this->_helperData->getOrderStates();

        $transaction = $this->_transactionRepository->getByTransactionType(
            Transaction::TYPE_ORDER,
            $payment->getId(),
            $payment->getOrder()->getId()
        );

        $payment->setIsTransactionPending(false);
        $payment->setIsTransactionApproved(true);
        $status = $statuses["approved"];

        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)->setStatus($status);
        $payment->setSkipOrderProcessing(true);

        $invoice = $objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);
        $invoice = $invoice->setTransactionId($payment->getTransactionId())
            ->addComment("Invoice created")
            ->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
        $invoice->register()
            ->pay();
        $invoice->save();

        // Save the invoice to the order
        $transactionInvoice = $this->_objectManager->create('Magento\Framework\DB\Transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());

        $transactionInvoice->save();

        $order->addStatusHistoryComment(
            __('Invoice #%1', $invoice->getId())
        )
            ->setIsCustomerNotified(true);

        $message = __('Payment approved');

        $payment->addTransactionCommentsToOrder($transaction, $message);

        $transaction->save();

        $order->save();

        die($response_confirm);

    }
}