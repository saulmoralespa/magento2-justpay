<?php


namespace Saulmoralespa\JustPay\Controller\Payment;

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Payment\Transaction;

class Notify extends \Magento\Framework\App\Action\Action
{
    protected $_scopeConfig;

    protected $_justPayLogger;

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
        \Saulmoralespa\JustPay\Logger\Logger $justPayLogger,
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
        $this->_justPayLogger = $justPayLogger;
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

        $this->_helperData->log($params);
    }
}